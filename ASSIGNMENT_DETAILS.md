# Assignment Brief — Simple E-commerce Shopping Cart (Laravel)

## Project
**Simple E-commerce Shopping Cart**

Build a simple e-commerce shopping cart system where authenticated users can:
- Browse products
- Add products to a cart
- Update item quantities
- Remove items

Each product must have:
- `name`
- `price`
- `stock_quantity`

---

## Required stack
- **Backend:** Laravel
- **Frontend:** Livewire 4 (recommended) / React / Vue (choose one)
- **Styling:** Tailwind CSS
- **Routing (chosen approach):** Laravel Folio
- **UI Kit (chosen approach):** Livewire Flux
- **Media:** Spatie Media Library
- **Slugs:** Spatie Sluggable
- **Version control:** Git + GitHub

---

## Authentication
Use Laravel’s built-in authentication from the chosen **starter kit**.

---

## Cart requirements
- Each shopping cart must be associated with the **authenticated user**.
- Cart actions must be stored and retrieved based on the **currently authenticated user**.
- Do **not** use session or local storage for cart persistence.
- The cart must support concurrent updates (same user across devices/tabs).

---

## Cart expiration + wishlist behaviour
- Cart is stored in the database and expires after a **global TTL** (configured).
- When a cart expires:
  - Cart items are moved into the user’s **default wishlist**
  - User must still have **one active cart** available after expiry

Wishlist is an important feature and must exist.

---

## Product catalogue requirements
- Products must support **simple categorisation**.
- Products should use **slugs** for URLs.
- Products should support **images** via Media Library.

---

## Key requirements (background tasks)

### 1) Low Stock Notification
When a product’s stock is running low:
- Trigger a Laravel **Job/Queue**
- Send an email to a **dummy admin user**

### 2) Daily Sales Report
Implement a scheduled job (cron):
- Runs **every evening**
- Sends a report of all products sold that day to the dummy admin email

Note: even without payments, the system needs a “checkout/complete sale” action to generate “sold today” records.

---

## Submission
- Host the code on **GitHub**
- Add **@dylanmichaelryan** as a collaborator to the repository

---

## Deliverables checklist (what “done” looks like)
- [ ] Starter kit auth works (register/login/logout)
- [ ] Product listing and product detail page
- [ ] Category filtering or browsing
- [ ] DB-backed cart per user (no sessions)
- [ ] Add/update/remove cart items
- [ ] Cart expires after TTL; items moved to wishlist; new active cart available
- [ ] Wishlist page (view items)
- [ ] Checkout creates daily “sold” records and decrements stock
- [ ] Low stock email sent via queued job
- [ ] Daily sales report sent via scheduler
- [ ] Repo pushed to GitHub and collaborator added
- [ ] `README.md` with setup + run steps

---

## Suggested environment variables
- `CART_TTL_MINUTES=60`
- `LOW_STOCK_THRESHOLD=5`
- `ADMIN_EMAIL=admin@example.com`
- `REPORT_TIME=19:00`

---

## Local run requirements (document in README)
- `php artisan migrate --seed`
- `php artisan queue:work`
- Scheduler: `php artisan schedule:work` (dev) or cron in production
