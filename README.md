# Live Commerce

A modern e-commerce shopping cart system built with Laravel 12, Livewire 3, and Tailwind CSS 4. This application provides a complete shopping experience with real-time cart management, wishlist functionality, checkout processing, and automated admin notifications.

## Features

- **Product Catalog**: Browse products with category filtering, sorting options, and SEO-friendly slugs
- **Shopping Cart**:  Database-backed cart with real-time updates, quantity management, and automatic expiration
- **Wishlist**: Save products for later; expired cart items automatically move to wishlist
- **Checkout**: Complete order processing with billing details, stock validation, and invoice generation
- **Low Stock Alerts**: Automated email notifications when product stock falls below threshold
- **Daily Sales Reports**: Scheduled evening reports summarizing daily sales activity
- **Authentication**: Full user authentication with two-factor authentication support

## Tech Stack

| Category | Technology |
|----------|------------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Livewire 3, Volt, Flux UI |
| Styling | Tailwind CSS 4 |
| Routing | Laravel Folio (file-based) |
| Database | MySQL / SQLite |
| Media | Spatie Media Library |
| Testing | Pest PHP 4 |
| Code Quality | Laravel Pint, Larastan |

## Prerequisites

### With Docker (Laravel Sail)

- Docker Desktop
- Docker Compose

### Without Docker

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+ or SQLite
- Git

## Installation

### Option 1: Using Docker (Laravel Sail)

Laravel Sail provides a Docker-powered local development environment with zero configuration.

```bash
# Clone the repository
git clone https://github.com/your-username/live-commerce.git
cd live-commerce

# Copy environment file
cp .env.example .env

# Install PHP dependencies using a temporary container
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# Start Sail containers
./vendor/bin/sail up -d

# Generate application key
./vendor/bin/sail artisan key:generate

# Run database migrations and seeders
./vendor/bin/sail artisan migrate --seed

# Create storage symlink
./vendor/bin/sail artisan storage:link

# Install frontend dependencies and build assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

The application will be available at `http://localhost`.

#### Sail Commands Reference

```bash
# Start containers in background
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run Artisan commands
./vendor/bin/sail artisan <command>

# Run Composer commands
./vendor/bin/sail composer <command>

# Run npm commands
./vendor/bin/sail npm <command>

# Access MySQL CLI
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs
```

### Option 2: Without Docker (Local Environment)

#### 1. Clone and Install Dependencies

```bash
# Clone the repository
git clone https://github.com/your-username/live-commerce.git
cd live-commerce

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install
```

#### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 3. Database Setup

**For MySQL:**

```bash
# Update .env with your MySQL credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=live_commerce
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For SQLite:**

```bash
# Create SQLite database file
touch database/database.sqlite

# Update .env
DB_CONNECTION=sqlite
```

#### 4. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

#### 5. Build Frontend Assets

```bash
npm run build
```

#### 6. Create Storage Symlink

```bash
php artisan storage:link
```

## Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# Application
APP_NAME="Live Commerce"
APP_URL=http://localhost

# Database (MySQL example)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=live_commerce
DB_USERNAME=root
DB_PASSWORD=

# Queue (required for background jobs)
QUEUE_CONNECTION=database

# Mail (use 'log' for local development)
MAIL_MAILER=log

# Cart Configuration
CART_TTL_MINUTES=60

# Inventory Management
LOW_STOCK_THRESHOLD=10
ADMIN_EMAIL=admin@example.com

# Currency
APP_CURRENCY_SYMBOL=€
```

### Configuration Options

| Variable | Description | Default |
|----------|-------------|---------|
| `CART_TTL_MINUTES` | Minutes before cart expires and items move to wishlist | `60` |
| `LOW_STOCK_THRESHOLD` | Stock level that triggers low stock notifications | `10` |
| `ADMIN_EMAIL` | Email address for admin notifications | `admin@example.com` |
| `APP_CURRENCY_SYMBOL` | Currency symbol for display | `€` |

## Running the Application

### Development Mode

The easiest way to run all services concurrently:

```bash
composer run dev
```

This single command starts:
- Laravel development server
- Queue worker for background jobs
- Vite for frontend hot-reloading
- Laravel Pail for log tailing

### Manual Startup

If you prefer to run services separately:

```bash
# Terminal 1: Start the web server
php artisan serve

# Terminal 2: Start the queue worker (required for email notifications)
php artisan queue:work

# Terminal 3: Start Vite for frontend development
npm run dev

# Terminal 4 (optional): Start the scheduler for daily reports
php artisan schedule:work
```

### Production

```bash
# Build optimized frontend assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run queue worker as a daemon (use Supervisor in production)
php artisan queue:work --daemon

# Set up cron for scheduled tasks
# Add to crontab: * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Background Jobs

### Queue Worker

The queue worker processes background jobs like low stock notifications:

```bash
php artisan queue:work
```

### Scheduler

The scheduler runs the daily sales report at 8:00 PM:

```bash
# Development
php artisan schedule:work

# Production (add to crontab)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Manual Commands

```bash
# Send daily sales report manually
php artisan report:daily-sales

# Send report for a specific date
php artisan report:daily-sales --date=2025-12-30
```

## Testing

This project uses Pest PHP for testing, including browser tests with Playwright.

### Unit & Feature Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/DailySalesReportTest.php

# Run tests with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

### Browser Tests

Browser tests require Playwright and built frontend assets:

```bash
# Install Playwright browsers (first time only)
npx playwright install chromium --with-deps

# Build assets before running browser tests
npm run build

# Run browser tests
php artisan test tests/Browser

# Run browser tests in headed mode (opens browser)
php artisan test tests/Browser --headed

# Run browser tests with debug (pauses on failure)
php artisan test tests/Browser --debug

# Run specific browser test
php artisan test tests/Browser/HomePageTest.php
```

### Code Quality

```bash
# Fix code style with Pint
vendor/bin/pint

# Run static analysis with Larastan
vendor/bin/phpstan analyse
```

## Project Architecture

```
app/
├── Console/Commands/     # Artisan commands (e.g., SendDailySalesReport)
├── Enums/                # PHP enums (CartStatus, CartType, InvoiceStatus, etc.)
├── Jobs/                 # Queued jobs (SendLowStockNotification)
├── Mail/                 # Mailable classes (DailySalesReport, LowStockNotification)
├── Models/               # Eloquent models
├── Observers/            # Model observers (ProductObserver for stock monitoring)
├── Repositories/         # Repository pattern implementation
│   └── Contracts/        # Repository interfaces
├── Services/             # Business logic services
│   ├── CartService.php
│   ├── CheckoutService.php
│   ├── ReportingService.php
│   └── WishlistService.php
└── Providers/            # Service providers

resources/views/
├── components/           # Blade components
├── emails/               # Email templates
├── livewire/             # Livewire components
└── pages/                # Folio page-based routes
    ├── index.blade.php   # Homepage
    ├── shop/             # Product catalog
    └── checkout/         # Checkout flow
```

### Key Design Patterns

- **Repository Pattern** — Data access abstraction via contracts and implementations
- **Service Layer** — Business logic encapsulation (CartService, CheckoutService, etc.)
- **Observer Pattern** — Real-time stock monitoring via ProductObserver
- **Queue Jobs** — Asynchronous email notifications

## Database Schema

| Table | Description |
|-------|-------------|
| `users` | User accounts with authentication |
| `categories` | Product categories |
| `products` | Product catalog with stock tracking |
| `carts` | Shopping carts and wishlists |
| `cart_items` | Items within carts |
| `invoices` | Completed orders |
| `invoice_items` | Order line items |
| `customers` | Customer billing profiles |
| `media` | Product images (Spatie Media Library) |

## Default Credentials

After running seeders, you can log in with:

| Email | Password |
|-------|----------|
| `test@example.com` | `password` |

## Troubleshooting

### Common Issues

**Assets not loading:**
```bash
npm run build
# or for development
npm run dev
```

**Queue jobs not processing:**
```bash
php artisan queue:work
```

**Storage links broken:**
```bash
php artisan storage:link
```

**Clear all caches:**
```bash
php artisan optimize:clear
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`php artisan test`)
5. Run code style fixes (`vendor/bin/pint`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
