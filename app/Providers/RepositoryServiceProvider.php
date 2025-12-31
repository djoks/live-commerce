<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\CartRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\CartContract;
use App\Repositories\Contracts\CategoryContract;
use App\Repositories\Contracts\CustomerContract;
use App\Repositories\Contracts\InvoiceContract;
use App\Repositories\Contracts\ProductContract;
use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Repository service provider.
 *
 * Binds repository interfaces to their Eloquent implementations.
 * Uses Laravel's $bindings property for automatic resolution.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Contract to implementation bindings for dependency injection.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        CategoryContract::class => CategoryRepository::class,
        ProductContract::class => ProductRepository::class,
        CustomerContract::class => CustomerRepository::class,
        CartContract::class => CartRepository::class,
        InvoiceContract::class => InvoiceRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
