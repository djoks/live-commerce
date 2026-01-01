<?php

declare(strict_types=1);

use App\Jobs\SendLowStockNotification;
use App\Mail\LowStockNotification;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config(['app.admin_email' => 'admin@example.com']);
    config(['app.low_stock_threshold' => 10]);
});

describe('SendLowStockNotification Job', function () {
    it('sends email to admin when dispatched', function () {
        Mail::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 5,
        ]);

        SendLowStockNotification::dispatchSync($product);

        Mail::assertSent(LowStockNotification::class, function ($mail) use ($product) {
            return $mail->product->id === $product->id
                && $mail->hasTo('admin@example.com');
        });
    });

    it('does not send email when admin email is not configured', function () {
        Mail::fake();
        config(['app.admin_email' => null]);

        $product = Product::factory()->create([
            'stock_quantity' => 5,
        ]);

        SendLowStockNotification::dispatchSync($product);

        Mail::assertNothingSent();
    });
});

describe('LowStockNotification Mailable', function () {
    it('has correct subject with product name', function () {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'stock_quantity' => 3,
        ]);

        $mailable = new LowStockNotification($product);

        $mailable->assertHasSubject('Low Stock Alert: Test Product');
    });

    it('contains product information in content', function () {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'stock_quantity' => 3,
        ]);

        $mailable = new LowStockNotification($product);

        $mailable->assertSeeInHtml('Test Product');
        $mailable->assertSeeInHtml('3');
        $mailable->assertSeeInText('Test Product');
        $mailable->assertSeeInText('3 units');
    });
});

describe('ProductObserver Low Stock Detection', function () {
    it('dispatches job when stock falls below threshold', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 15,
            'low_stock_notified_at' => null,
        ]);

        // Reduce stock below threshold
        $product->update(['stock_quantity' => 8]);

        Queue::assertPushed(SendLowStockNotification::class, function ($job) use ($product) {
            return $job->product->id === $product->id;
        });
    });

    it('sets low_stock_notified_at when notification is dispatched', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 15,
            'low_stock_notified_at' => null,
        ]);

        $product->update(['stock_quantity' => 8]);

        /** @var Product $product */
    $product = $product->fresh();

    expect($product->low_stock_notified_at)->not->toBeNull();
    });

    it('does not dispatch job when already notified', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 8,
            'low_stock_notified_at' => now(),
        ]);

        // Further reduce stock
        $product->update(['stock_quantity' => 5]);

        Queue::assertNotPushed(SendLowStockNotification::class);
    });

    it('does not dispatch job when stock is above threshold', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 20,
            'low_stock_notified_at' => null,
        ]);

        // Reduce stock but stay above threshold
        $product->update(['stock_quantity' => 15]);

        Queue::assertNotPushed(SendLowStockNotification::class);
    });

    it('does not dispatch job when stock reaches zero', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 5,
            'low_stock_notified_at' => null,
        ]);

        // Stock depleted
        $product->update(['stock_quantity' => 0]);

        Queue::assertNotPushed(SendLowStockNotification::class);
    });

    it('resets notification flag when stock is replenished above threshold', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 5,
            'low_stock_notified_at' => now(),
        ]);

        // Restock above threshold
        $product->update(['stock_quantity' => 20]);

        /** @var Product $product */
    $product = $product->fresh();

    expect($product->low_stock_notified_at)->toBeNull();
    });

    it('can notify again after stock is replenished and drops again', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 5,
            'low_stock_notified_at' => now()->subDay(),
        ]);

        // Restock above threshold
        $product->update(['stock_quantity' => 20]);

        // Drop below threshold again
        $product->update(['stock_quantity' => 8]);

        Queue::assertPushed(SendLowStockNotification::class);
        /** @var Product $product */
    $product = $product->fresh();

    expect($product->low_stock_notified_at)->not->toBeNull();
    });

    it('does not dispatch when non-stock fields are updated', function () {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 5,
            'low_stock_notified_at' => null,
        ]);

        // Update non-stock field
        $product->update(['name' => 'Updated Name']);

        Queue::assertNotPushed(SendLowStockNotification::class);
    });
});
