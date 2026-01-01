<?php

declare(strict_types=1);

use App\Enums\CartStatus;
use App\Enums\CartType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

describe('Checkout Page', function () {
    it('redirects to shop when cart is empty', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in');

        $page->navigate('/checkout')
            ->assertPathIs('/shop')
            ->assertNoJavaScriptErrors();
    });

    it('displays checkout page with cart items', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $product = Product::factory()->create([
            'name' => 'Test Chair',
            'price' => 250.00,
            'stock_quantity' => 10,
        ]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertSee('Checkout')
            ->assertSee('Billing details')
            ->assertSee('Test Chair')
            ->assertSee('x 2')
            ->assertNoJavaScriptErrors();
    });

    it('displays billing form fields', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $product = Product::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertSee('Name')
            ->assertSee('Country / Region')
            ->assertSee('Street address')
            ->assertSee('Town / City')
            ->assertSee('ZIP code')
            ->assertSee('Phone')
            ->assertSee('Email address')
            ->assertNoJavaScriptErrors();
    });

    it('displays payment method options', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $product = Product::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertSee('Direct Bank Transfer')
            ->assertSee('Card')
            ->assertSee('Cash On Delivery')
            ->assertNoJavaScriptErrors();
    });

    it('displays order summary with totals', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $product = Product::factory()->create([
            'price' => 100.00,
        ]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertSee('Subtotal')
            ->assertSee('Total')
            ->assertSee('Place order')
            ->assertNoJavaScriptErrors();
    });

    it('prefills user name and email', function () {
        $user = User::factory()->withoutTwoFactor()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $product = Product::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertValue('input[wire\\:model\\.blur="name"]', 'John Doe')
            ->assertValue('input[wire\\:model\\.blur="email"]', 'john@example.com')
            ->assertNoJavaScriptErrors();
    });

    it('displays features banner on checkout page', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $product = Product::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertSee('High Quality')
            ->assertSee('Warranty Protection')
            ->assertSee('Free Shipping')
            ->assertSee('24 / 7 Support')
            ->assertNoJavaScriptErrors();
    });
});

describe('Checkout Flow', function () {
    it('completes checkout successfully', function () {
        $user = User::factory()->withoutTwoFactor()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
        $product = Product::factory()->create([
            'price' => 150.00,
            'stock_quantity' => 10,
        ]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->assertSee('Billing details');

        $page->type('input[wire\\:model\\.blur="streetAddress"]', '123 Main Street')
            ->type('input[wire\\:model\\.blur="city"]', 'New York')
            ->type('input[wire\\:model\\.blur="zipCode"]', '10001')
            ->type('input[wire\\:model\\.blur="phone"]', '+1234567890')
            ->select('select[wire\\:model\\.blur="countryRegion"]', 'United States')
            ->click('Direct Bank Transfer')
            ->click('Place order')
            ->assertSee('Your order has been placed!')
            ->assertNoJavaScriptErrors();
    });

    it('shows validation errors for empty required fields', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $product = Product::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $page = visit('/login');
        $page->type('input[name=email]', $user->email)
            ->type('input[name=password]', 'password')
            ->click('Log in')
            ->assertPathIsNot('/login');

        $page->navigate('/checkout')
            ->clear('input[wire\\:model\\.blur="name"]')
            ->click('Direct Bank Transfer')
            ->click('Place order')
            ->assertSee('Please fix the following errors')
            ->assertNoJavaScriptErrors();
    });
});

describe('Checkout Success Page', function () {
    it('displays order confirmation', function () {
        $page = visit('/checkout/success?order_no=TEST1234');

        $page->assertSee('Your order has been placed!')
            ->assertSee('TEST1234')
            ->assertSee('Thank you for your purchase')
            ->assertSee('Back to Home')
            ->assertNoJavaScriptErrors();
    });

    it('navigates back to home from success page', function () {
        $page = visit('/checkout/success?order_no=TEST1234');

        $page->click('Back to Home')
            ->assertPathIs('/')
            ->assertNoJavaScriptErrors();
    });
});
