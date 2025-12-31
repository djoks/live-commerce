<?php

declare(strict_types=1);

use App\Mail\DailySalesReport;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config(['app.admin_email' => 'admin@example.com']);
    config(['app.currency_symbol' => '$']);
});

describe('ReportingService', function () {
    it('generates report with correct totals', function () {
        $invoice = Invoice::factory()->create([
            'total_amount' => 150.00,
            'created_at' => today(),
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_name' => 'Product A',
            'quantity' => 2,
            'unit_price' => 50.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_name' => 'Product B',
            'quantity' => 1,
            'unit_price' => 50.00,
        ]);

        $service = app(ReportingService::class);
        $report = $service->getDailySalesReport(today());

        expect($report['total_orders'])->toBe(1)
            ->and($report['total_items_sold'])->toBe(3)
            ->and($report['total_revenue'])->toBe(150.0)
            ->and($report['products'])->toHaveCount(2);
    });

    it('returns empty report when no sales', function () {
        $service = app(ReportingService::class);
        $report = $service->getDailySalesReport(today());

        expect($report['total_orders'])->toBe(0)
            ->and($report['total_items_sold'])->toBe(0)
            ->and($report['total_revenue'])->toBe(0.0)
            ->and($report['products'])->toBeEmpty();
    });

    it('only includes invoices from specified date', function () {
        Invoice::factory()->create([
            'total_amount' => 100.00,
            'created_at' => today(),
        ]);

        Invoice::factory()->create([
            'total_amount' => 200.00,
            'created_at' => today()->subDay(),
        ]);

        $service = app(ReportingService::class);
        $report = $service->getDailySalesReport(today());

        expect($report['total_orders'])->toBe(1)
            ->and($report['total_revenue'])->toBe(100.0);
    });

    it('sends email to admin', function () {
        Mail::fake();

        Invoice::factory()->create(['created_at' => today()]);

        $service = app(ReportingService::class);
        $result = $service->sendDailySalesReport(today());

        expect($result)->toBeTrue();

        Mail::assertSent(DailySalesReport::class, function ($mail) {
            return $mail->hasTo('admin@example.com');
        });
    });

    it('returns false when admin email not configured', function () {
        Mail::fake();
        config(['app.admin_email' => null]);

        $service = app(ReportingService::class);
        $result = $service->sendDailySalesReport(today());

        expect($result)->toBeFalse();
        Mail::assertNothingSent();
    });

    it('groups same product with same price correctly', function () {
        $invoice1 = Invoice::factory()->create(['created_at' => today()]);
        $invoice2 = Invoice::factory()->create(['created_at' => today()]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice1->id,
            'product_name' => 'Widget',
            'quantity' => 2,
            'unit_price' => 25.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice2->id,
            'product_name' => 'Widget',
            'quantity' => 3,
            'unit_price' => 25.00,
        ]);

        $service = app(ReportingService::class);
        $report = $service->getDailySalesReport(today());

        expect($report['products'])->toHaveCount(1);

        $widget = $report['products']->first();
        expect($widget->product_name)->toBe('Widget')
            ->and($widget->total_quantity)->toBe(5)
            ->and($widget->total_revenue)->toBe(125.0);
    });
});

describe('DailySalesReport Mailable', function () {
    it('has correct subject with date', function () {
        $date = Carbon::parse('2025-12-31');

        $mailable = new DailySalesReport(
            productsSold: collect(),
            totalOrders: 0,
            totalRevenue: 0.0,
            totalItemsSold: 0,
            reportDate: $date
        );

        $mailable->assertHasSubject('Daily Sales Report - December 31, 2025');
    });

    it('contains sales data in content', function () {
        $products = collect([
            (object) [
                'product_name' => 'Test Product',
                'unit_price' => 99.99,
                'total_quantity' => 5,
                'total_revenue' => 499.95,
            ],
        ]);

        $mailable = new DailySalesReport(
            productsSold: $products,
            totalOrders: 3,
            totalRevenue: 499.95,
            totalItemsSold: 5,
            reportDate: today()
        );

        $mailable->assertSeeInHtml('Test Product');
        $mailable->assertSeeInHtml('499.95');
        $mailable->assertSeeInText('Test Product');
    });
});

describe('SendDailySalesReport Command', function () {
    it('sends report and displays summary', function () {
        Mail::fake();

        Invoice::factory()->create([
            'total_amount' => 250.00,
            'created_at' => today(),
        ]);

        $this->artisan('report:daily-sales')
            ->expectsOutputToContain('Report sent to')
            ->assertSuccessful();

        Mail::assertSent(DailySalesReport::class);
    });

    it('accepts date option', function () {
        Mail::fake();

        $yesterday = today()->subDay();

        Invoice::factory()->create([
            'total_amount' => 100.00,
            'created_at' => $yesterday,
        ]);

        $this->artisan('report:daily-sales', ['--date' => $yesterday->format('Y-m-d')])
            ->assertSuccessful();

        Mail::assertSent(DailySalesReport::class, function ($mail) use ($yesterday) {
            return $mail->reportDate->isSameDay($yesterday);
        });
    });

    it('fails when admin email not configured', function () {
        config(['app.admin_email' => null]);

        $this->artisan('report:daily-sales')
            ->expectsOutputToContain('Admin email not configured')
            ->assertFailed();
    });
});
