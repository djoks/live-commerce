<?php

declare(strict_types=1);

use App\Models\Product;

describe('Home Page', function () {
    it('displays the home page with hero section', function () {
        $page = visit('/');

        $page->assertSee('New Arrival')
            ->assertSee('Discover Our')
            ->assertSee('New Collection')
            ->assertSee('Buy Now')
            ->assertNoJavaScriptErrors();
    });

    it('displays navigation elements', function () {
        $page = visit('/');

        $page->assertSee('Home')
            ->assertSee('Shop')
            ->assertNoJavaScriptErrors();
    });

    it('displays featured products section', function () {
        Product::factory()->count(4)->create();

        $page = visit('/');

        $page->assertNoJavaScriptErrors();
    });

    it('navigates to shop when clicking Buy Now button', function () {
        $page = visit('/');

        $page->click('Buy Now')
            ->assertPathIs('/shop')
            ->assertSee('Shop')
            ->assertNoJavaScriptErrors();
    });

    it('displays footer with copyright', function () {
        $page = visit('/');

        $page->assertSee(date('Y'))
            ->assertNoJavaScriptErrors();
    });
});
