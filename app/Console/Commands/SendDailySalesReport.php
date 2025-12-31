<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Command to send daily sales report to admin.
 *
 * Delegates to ReportingService for report generation and email delivery.
 */
class SendDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-sales
                            {--date= : The date to generate the report for (Y-m-d format, defaults to today)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily sales report email to the admin';

    /**
     * Execute the console command.
     */
    public function handle(ReportingService $reportingService): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::today();

        $this->info("Generating sales report for {$date->format('F j, Y')}...");

        if (! $reportingService->sendDailySalesReport($date)) {
            $this->error('Admin email not configured. Set ADMIN_EMAIL in your .env file.');

            return self::FAILURE;
        }

        $report = $reportingService->getDailySalesReport($date);

        $this->info('Report sent to '.config('app.admin_email'));
        $this->table(
            ['Metric', 'Value'],
            [
                ['Orders', $report['total_orders']],
                ['Items Sold', $report['total_items_sold']],
                ['Revenue', config('app.currency_symbol').number_format($report['total_revenue'], 2)],
                ['Products', $report['products']->count()],
            ]
        );

        return self::SUCCESS;
    }
}
