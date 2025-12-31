<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
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
        .alert-badge {
            display: inline-block;
            background-color: #FEF3C7;
            color: #92400E;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .product-info {
            background-color: #F9FAFB;
            border-left: 4px solid #B88E2F;
            padding: 16px;
            margin: 24px 0;
        }
        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 8px 0;
        }
        .stock-details {
            display: flex;
            gap: 24px;
            margin-top: 12px;
        }
        .stock-item {
            text-align: center;
        }
        .stock-label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stock-value {
            font-size: 24px;
            font-weight: 700;
        }
        .stock-value.warning {
            color: #DC2626;
        }
        .stock-value.threshold {
            color: #6B7280;
        }
        .cta-button {
            display: inline-block;
            background-color: #B88E2F;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 24px;
        }
        .cta-button:hover {
            background-color: #9A7628;
        }
        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #E5E7EB;
            font-size: 14px;
            color: #6B7280;
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
        </div>

        <span class="alert-badge">Inventory Alert</span>

        <p>A product in your inventory has fallen below the minimum stock threshold and requires your attention.</p>

        <div class="product-info">
            <p class="product-name">{{ $productName }}</p>
            <div class="stock-details">
                <div class="stock-item">
                    <div class="stock-label">Current Stock</div>
                    <div class="stock-value warning">{{ $currentStock }}</div>
                </div>
                <div class="stock-item">
                    <div class="stock-label">Threshold</div>
                    <div class="stock-value threshold">{{ $threshold }}</div>
                </div>
            </div>
        </div>

        <p>To prevent stockouts and potential lost sales, we recommend reviewing your inventory levels and initiating a restock order if necessary.</p>

        <a href="{{ $appUrl }}/shop/{{ $productSlug }}" class="cta-button">View Product</a>

        <div class="footer">
            <p>This is an automated notification from {{ $appName }}. You are receiving this because you are registered as an administrator.</p>
            <p><a href="{{ $appUrl }}">{{ $appUrl }}</a></p>
        </div>
    </div>
</body>
</html>
