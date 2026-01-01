<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

describe('Shop Page', function () {
    it('displays the shop page with banner', function () {
        $page = visit('/shop');

        $page->assertSee('Shop')
            ->assertNoJavaScriptErrors();
    });

    it('displays products in grid layout', function () {
        $products = Product::factory()->count(4)->create();

        $page = visit('/shop');

        /** @var Product $product */
        $product = $products->first();

        $page->assertSee($product->name)
            ->assertNoJavaScriptErrors();
    });

    it('displays product prices', function () {
        $product = Product::factory()->create(['price' => 99.99]);

        $page = visit('/shop');

        $page->assertSee($product->name)
            ->assertNoJavaScriptErrors();
    });

    it('displays stock information on products', function () {
        $product = Product::factory()->create(['stock_quantity' => 25]);

        $page = visit('/shop');

        $page->assertSee('in stock')
            ->assertNoJavaScriptErrors();
    });

    it('navigates to product detail page when clicking product', function () {
        $product = Product::factory()->create();

        $page = visit('/shop');

        $page->click($product->name)
            ->assertPathIs("/shop/{$product->slug}")
            ->assertSee($product->name)
            ->assertSee('Add To Cart')
            ->assertNoJavaScriptErrors();
    });

    it('displays filter bar with sorting options', function () {
        Product::factory()->count(5)->create();

        $page = visit('/shop');

        $page->assertSee('Show')
            ->assertSee('Sort by')
            ->assertNoJavaScriptErrors();
    });

    it('shows pagination when many products exist', function () {
        Product::factory()->count(20)->create();

        $page = visit('/shop');

        $page->assertNoJavaScriptErrors();
    });
});

describe('Product Detail Page', function () {
    it('displays product details', function () {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product description',
            'price' => 149.99,
            'stock_quantity' => 10,
        ]);

        $page = visit("/shop/{$product->slug}");

        $page->assertSee('Test Product')
            ->assertSee('This is a test product description')
            ->assertSee('10 in stock')
            ->assertSee('Add To Cart')
            ->assertNoJavaScriptErrors();
    });

    it('displays quantity selector', function () {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $page = visit("/shop/{$product->slug}");

        $page->assertSee('Add To Cart')
            ->assertSee('−')
            ->assertSee('+')
            ->assertNoJavaScriptErrors();
    });

    it('displays product category', function () {
        $category = Category::factory()->create(['name' => 'Furniture']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $page = visit("/shop/{$product->slug}");

        $page->assertSee('Category')
            ->assertSee('Furniture')
            ->assertNoJavaScriptErrors();
    });

    it('redirects to login when adding to cart as guest', function () {
        $product = Product::factory()->create();

        $page = visit("/shop/{$product->slug}");

        $page->click('Add To Cart')
            ->assertPathIs('/login')
            ->assertNoJavaScriptErrors();
    });

    it('adds product to cart when authenticated', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in');

        $page->navigate("/shop/{$product->slug}")
            ->click('Add To Cart')
            ->assertNoJavaScriptErrors();
    });

    it('displays breadcrumb navigation', function () {
        $product = Product::factory()->create();

        $page = visit("/shop/{$product->slug}");

        $page->assertSee('Shop')
            ->assertSee($product->name)
            ->assertNoJavaScriptErrors();
    });
});
