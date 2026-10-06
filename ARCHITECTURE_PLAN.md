# Scalable Shopping Cart System — Architecture & Implementation Plan (Laravel + Folio + Livewire 4 + Flux)

> Goal: deliver a **simple**, **review-friendly** implementation that can scale to **millions of users** by design (clean data model, correct indexing, background processing, caching strategy), without turning the codebase into an over-engineered “framework inside a framework”.

---

## 1) Tech stack & architectural choices

### Stack
- **Backend:** Laravel
- **Routing:** **Laravel Folio** (file-based routing)
- **Frontend:** **Livewire 4** + **Livewire Flux** (UI components)
- **Styling:** Tailwind CSS
- **Media:** Spatie **Media Library** (product images)
- **Slugs:** Spatie **Sluggable** (SEO-friendly URLs)
- **Queues:** Laravel Queue (recommended: **Redis** for scale; **database** is fine for demo)
- **Reports/Tasks:** Laravel Scheduler (cron-driven)
- **Version control:** Git/GitHub

### Patterns (simple but scalable)
- **Repository Pattern** for query/data access boundaries
- **Service Pattern** for business workflows (cart operations, wishlist operations, reporting)
- **Event/Listener** (optional but neat) to decouple “side effects” (low-stock alerts)

---

## 2) Key functional requirements recap

### Core
- Users can browse products.
- Users can add products to a cart, update quantities, remove items.
- Cart is **database-backed** and **user-scoped** (no session/local storage).

### Extra requirements (must)
- **Low Stock Notification:** when stock is “running low”, trigger a **queued** email to a dummy admin.
- **Daily Sales Report:** scheduled job runs **every evening** and emails dummy admin with products sold that day.

### Added requirement (you specified)
- Cart must **expire** after a global TTL.
- When cart expires, items are moved to the user’s **wishlist**.
- User should always have **one active cart**; wishlist exists and is important.
- Must support **concurrent updates** across devices/tabs for the same user.
- Simple product **categories**.

---

## 3) Domain model & database schema

### 3.1 Tables

#### users
Use starter kit auth tables.

#### categories
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| name | string | |
| slug | string | unique index |
| created_at/updated_at | timestamps | |

#### products
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| category_id | big integer | indexed FK |
| name | string | |
| slug | string | unique index |
| description | text | nullable |
| price | decimal(10,2) | |
| stock_quantity | unsigned integer | |
| created_at/updated_at | timestamps | |

> Product images are stored via **Spatie Media Library** (`media` table).

#### carts
Represents the *current* “shopping session” for a user.
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| user_id | big integer | indexed FK |
| status | string | `active`, `expired`, `checked_out` |
| last_activity_at | timestamp | indexed (optional) |
| expires_at | timestamp | indexed |
| created_at/updated_at | timestamps | |

**Important constraints**
- Ensure *one active cart per user*:
  - Option A (portable): enforce in code + index `(user_id, status)` and always keep at most one `active`.
  - Option B (strict DB): partial unique index on `(user_id)` where `status='active'` (DB-dependent; not portable across all DB engines).

#### cart_items
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| cart_id | big integer | indexed FK |
| product_id | big integer | indexed FK |
| quantity | unsigned integer | |
| created_at/updated_at | timestamps | |

**Unique constraint:** `(cart_id, product_id)`.

#### wishlists
One default wishlist per user (extendable later).
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| user_id | big integer | indexed FK |
| name | string | default: `Default` |
| is_default | boolean | default true |
| created_at/updated_at | timestamps | |

**Unique constraint:** `(user_id, is_default)` to guarantee one default.

#### wishlist_items
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| wishlist_id | big integer | indexed FK |
| product_id | big integer | indexed FK |
| created_at/updated_at | timestamps | |

**Unique constraint:** `(wishlist_id, product_id)`.

#### sales (or orders)
Because you need a daily report of “products sold today”, you need a persisted record of sales.
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| user_id | big integer | indexed FK |
| total_amount | decimal(10,2) | |
| created_at/updated_at | timestamps | |

#### sale_items (or order_items)
| column | type | notes |
|---|---|---|
| id | big integer | PK |
| sale_id | big integer | indexed FK |
| product_id | big integer | indexed FK |
| quantity | unsigned integer | |
| unit_price | decimal(10,2) | snapshot price |
| created_at/updated_at | timestamps | |

---

## 4) Eloquent relationships (high-level)

- `Category hasMany Product`
- `Product belongsTo Category`
- `User hasMany Cart`
- `User hasOne defaultWishlist`
- `Cart belongsTo User`
- `Cart hasMany CartItem`
- `CartItem belongsTo Product`
- `Sale belongsTo User`
- `Sale hasMany SaleItem`
- `SaleItem belongsTo Product`

---

## 5) Folio routing + UI pages (Livewire + Flux)

### Folio pages (suggested)
```
resources/views/pages/
  index.blade.php
  products/index.blade.php
  products/[Product:slug].blade.php
  cart.blade.php
  wishlist.blade.php
  categories/[Category:slug].blade.php
```

### Livewire components (suggested)
```
app/Livewire/
  Products/
    ProductIndex.php
    ProductShow.php
  Cart/
    CartPanel.php
  Wishlist/
    WishlistPanel.php
```

### Flux usage (examples)
- `<flux:button>` for add/remove/update actions.
- `<flux:input>` for quantities.
- `<flux:modal>` for confirm remove (optional).

---

## 6) Application structure (folders) — scalable but simple

```
app/
  Models/
  Livewire/
  Repositories/
    Contracts/
      ProductRepositoryInterface.php
      CartRepositoryInterface.php
      WishlistRepositoryInterface.php
      SaleRepositoryInterface.php
    Eloquent/
      ProductRepository.php
      CartRepository.php
      WishlistRepository.php
      SaleRepository.php
  Services/
    CartService.php
    WishlistService.php
    CheckoutService.php
    ReportingService.php
  Jobs/
    SendLowStockAlertJob.php
    SendDailySalesReportJob.php
    ExpireCartsJob.php   (optional; see scheduler approach below)
  Notifications/
    LowStockNotification.php
    DailySalesReportNotification.php
  Console/
    Commands/
      SendDailySalesReport.php (optional; job-only is fine)
      ExpireCarts.php          (optional; job-only is fine)
  Events/ (optional)
  Listeners/ (optional)
```

**Rule of thumb**
- Repositories: **how to query/write**
- Services: **what should happen** (rules, workflows, transactions)
- Livewire components: **UI state + calling services**

---

## 7) Cart lifecycle (global TTL + wishlist move)

### 7.1 Config
Add to `.env`:
- `CART_TTL_MINUTES=60`
- `LOW_STOCK_THRESHOLD=5`
- `ADMIN_EMAIL=admin@example.com`
- `REPORT_TIME=19:00` (evening run time; keep configurable)

### 7.2 “One active cart per user”
In `CartService::getOrCreateActiveCart(User $user)`:
1. Find active cart for the user where `status='active'`.
2. If found and `expires_at > now()`: return it.
3. If found but expired: expire it (move items to wishlist, mark status), then create a new active cart.
4. If not found: create new active cart.

### 7.3 Sliding expiration
On **any cart activity** (view, add, update, remove):
- update `last_activity_at = now()`
- update `expires_at = now()->addMinutes(CART_TTL_MINUTES)`

This is the simplest and most user-friendly expiry definition.

### 7.4 Expiration process (move to wishlist)
When expiring a cart:
1. Get user’s default wishlist (`firstOrCreate`).
2. For each `cart_item`:
   - insert into `wishlist_items` if not already there
3. Delete cart items (or keep and mark as migrated; deleting keeps it simple)
4. Mark cart `status='expired'`

---

## 8) Concurrency (multi-device, multi-tab)

### 8.1 Database truth
Cart is DB-backed and user-scoped. Every device reads/writes the same cart rows.

### 8.2 Race conditions to handle
- Two devices add the same product simultaneously.
- Two devices update the same cart item simultaneously.

**Mitigations**
- Unique constraint `(cart_id, product_id)` on `cart_items`.
- Use atomic operations:
  - `updateOrCreate` + increment logic inside a transaction
  - or DB-level `upsert` where supported
- For stock correctness at “sale time”:
  - Use a transaction + **row lock** on products (`SELECT ... FOR UPDATE`) during checkout.

### 8.3 UX sync (optional)
- Livewire polling (`wire:poll.5s`) on cart panel.
- Broadcasting events (more complex, optional for this assignment).

---

## 9) Checkout (sales record + stock decrement)

Even though “payments” are not required, a **checkout** action is needed to create “sold today” data.

### 9.1 Checkout workflow (transactional)
In `CheckoutService::checkout(User $user)`:
1. Load active cart with cart items + products.
2. Validate cart not expired.
3. Begin DB transaction.
4. For each item:
   - lock product row
   - ensure `stock_quantity >= quantity`
5. Create `sale` row (and `sale_items` rows).
6. Decrement stock for each product.
7. Clear cart items.
8. Mark cart `status='checked_out'`.
9. Commit.
10. After commit:
    - trigger low-stock alerts (async).

### 9.2 Low-stock “threshold crossing” (avoid spam)
Only send an alert when stock *crosses* the threshold:
- If previous stock was `> threshold` and new stock is `<= threshold` → notify.

---

## 10) Low stock notification (Job/Queue)

### Implementation
- Dispatch `SendLowStockAlertJob(product_id, new_stock)` when threshold crossing occurs.
- Job sends `LowStockNotification` to `ADMIN_EMAIL` (or a seeded admin user).

### Queue driver
- **Redis** recommended for scale.
- Database queue acceptable for demo.

Run worker:
```bash
php artisan queue:work
```

---

## 11) Daily Sales Report (Scheduler)

### Report content (simple + useful)
Email includes:
- Date
- Total quantity sold
- Total revenue
- Breakdown per product:
  - product name
  - quantity sold
  - revenue (qty * unit_price)

### Scheduler
- `SendDailySalesReportJob` scheduled using Laravel scheduler at `REPORT_TIME`.

Cron entry (server):
```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## 12) Installation notes for the selected packages

### 12.1 Laravel starter kit (Livewire)
Use a starter kit that includes auth scaffolding.

### 12.2 Laravel Folio
Install Folio and configure base path for pages. Keep pages in `resources/views/pages`.

### 12.3 Livewire Flux
- Install via composer.
- Add Flux styles/scripts to layout.
- Ensure Tailwind config includes Flux templates.

### 12.4 Spatie Media Library
- Install and publish migrations/config.
- Run migrations.
- On Product model:
  - `implements HasMedia`
  - `use InteractsWithMedia`
- Use conversions for thumbnails.

### 12.5 Spatie Sluggable
- Install
- Add `slug` columns to models
- In Product/Category models:
  - `use HasSlug`
  - `getSlugOptions()` generating from `name`

---

## 13) Indexing and scaling checklist

### Must-have DB indexes
- `products.slug` unique
- `categories.slug` unique
- `products.category_id`
- `carts.user_id, carts.status`
- `carts.expires_at`
- `cart_items.cart_id`
- unique `(cart_id, product_id)`
- `wishlists.user_id, is_default`
- unique `(wishlist_id, product_id)`
- `sale_items.sale_id`
- `sales.created_at`

### Caching
- Cache category list and frequently accessed product listings.
- Optionally cache product detail by slug (short TTL).

### Read scaling (later)
- Add read replicas for catalog browsing (read-heavy).
- Keep writes (cart updates, checkout) on primary.

---

## 14) Suggested “first implementation sprint” order

1. Scaffold Laravel + starter kit (auth)
2. Install Folio + basic pages
3. Create migrations & models (categories/products/carts/cart_items/wishlist/etc.)
4. Seed categories + products + dummy admin email/user
5. Implement Product listing & detail pages
6. Implement cart add/update/remove (service + repo)
7. Implement cart expiry logic + wishlist migration (service + scheduled cleanup)
8. Implement checkout + sales tables
9. Implement low-stock job + notification
10. Implement daily report job + notification + scheduler
11. Add basic tests + README

---

## 15) Minimal test plan (high value, low effort)

- Cart is always scoped to authenticated user.
- Same user across devices updates the same cart (feature test: simulate two requests).
- Cart expiry moves items to wishlist and issues new active cart.
- Checkout creates sale + sale_items and decrements stock.
- Low-stock job is dispatched when threshold is crossed.
- Daily report job builds correct aggregates.

---

## 16) README essentials (what reviewers love)
- Setup instructions
- Seed instructions
- Queue worker instructions
- Scheduler instructions
- Admin email used
- Any env vars required

---

## Appendix: Example interfaces (shape only)

```php
interface CartRepositoryInterface {
    public function findActiveByUserId(int $userId): ?Cart;
    public function createActive(int $userId, \DateTimeInterface $expiresAt): Cart;
    public function markExpired(Cart $cart): void;
    public function markCheckedOut(Cart $cart): void;
}

interface CartServiceInterface {
    public function getOrCreateActiveCart(User $user): Cart;
    public function addItem(User $user, int $productId, int $qty): void;
    public function updateItem(User $user, int $productId, int $qty): void;
    public function removeItem(User $user, int $productId): void;
    public function expireIfNeeded(User $user): void;
}
```

Keep the actual code lean—these are just boundaries.
