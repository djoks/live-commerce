<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            border-bottom: 3px solid #B88E2F;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header h1 {
            color: #B88E2F;
            font-size: 24px;
            margin: 0;
        }
        .header .date {
            color: #6B7280;
            font-size: 14px;
            margin-top: 4px;
        }
        .intro {
            color: #374151;
            margin-bottom: 24px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        .summary-card {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        .summary-card .label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }
        .summary-card .value.revenue {
            color: #059669;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #E5E7EB;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .products-table th {
            background-color: #F9FAFB;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #E5E7EB;
        }
        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
        }
        .products-table tr:last-child td {
            border-bottom: none;
        }
        .products-table .product-name {
            font-weight: 500;
            color: #111827;
        }
        .products-table .number {
            text-align: right;
            font-family: 'SF Mono', Monaco, monospace;
        }
        .no-sales {
            background-color: #F3F4F6;
            border-radius: 8px;
            padding: 24px;
            margin: 24px 0;
            text-align: center;
        }
        .no-sales .icon {
            font-size: 32px;
            margin-bottom: 12px;
        }
        .no-sales .title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .no-sales .message {
            color: #6B7280;
            font-size: 14px;
        }
        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #E5E7EB;
            font-size: 13px;
            color: #9CA3AF;
        }
        .footer a {
            color: #B88E2F;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <div class="date">{{ $reportDate }}</div>
        </div>

        <p class="intro">Here's your end-of-day sales summary. Review the metrics below to track performance and identify trends.</p>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Orders Placed</div>
                <div class="value">{{ $totalOrders }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Units Sold</div>
                <div class="value">{{ $totalItemsSold }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Revenue</div>
                <div class="value revenue">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        @if($productsSold->isEmpty())
            <div class="no-sales">
                <div class="title">No Sales Recorded</div>
                <div class="message">There were no completed orders on this date. Consider reviewing your traffic sources or promotional campaigns to identify opportunities.</div>
            </div>
        @else
            <h2 class="section-title">Product Breakdown</h2>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="number">Units</th>
                        <th class="number">Price</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productsSold as $product)
                        <tr>
                            <td class="product-name">{{ $product->product_name }}</td>
                            <td class="number">{{ $product->total_quantity }}</td>
                            <td class="number">{{ $currencySymbol }}{{ number_format($product->unit_price, 2) }}</td>
                            <td class="number">{{ $currencySymbol }}{{ number_format($product->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>Automated report generated by {{ $appName }}. Sent to store administrators daily at 8:00 PM.</p>
            <p><a href="{{ $appUrl }}">{{ $appUrl }}</a></p>
        </div>
    </div>
</body>
</html>
