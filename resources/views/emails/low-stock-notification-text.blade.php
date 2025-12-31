{{ $appName }} - LOW STOCK ALERT
================================

A product in your inventory has fallen below the minimum stock threshold and requires your attention.

PRODUCT DETAILS
---------------
Product: {{ $productName }}
Current Stock: {{ $currentStock }} units
Alert Threshold: {{ $threshold }} units

RECOMMENDED ACTION
------------------
To prevent stockouts and potential lost sales, we recommend reviewing your inventory levels and initiating a restock order if necessary.

View Product: {{ $appUrl }}/shop/{{ $productSlug }}

---

This is an automated notification from {{ $appName }}.
You are receiving this because you are registered as an administrator.

{{ $appUrl }}
