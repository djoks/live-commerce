<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use App\Repositories\Contracts\CartContract;
use App\Repositories\Contracts\InvoiceContract;
use App\Repositories\Contracts\ProductContract;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service class for handling order checkout processes.
 *
 * Manages inventory validation, order calculation, invoice creation, and stock reduction.
 */
class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private CartContract $cartRepository,
        private InvoiceContract $invoiceRepository,
        private ProductContract $productRepository
    ) {}

    /**
     * Process the checkout for a user's active cart.
     *
     * Validates stock, calculates totals (including tax), creates a pending invoice,
     * reduces product stock, and marks the cart as sold.
     *
     * @param  User  $user  The authenticated user performing checkout.
     * @return Invoice The created invoice.
     *
     * @throws InvalidArgumentException If the cart is empty or stock is insufficient.
     */
    public function checkout(User $user, array $billingData): Invoice
    {
        $cart = $this->cartService->getOrCreateActiveCart($user);
        $cart->loadMissing('items.product');

        if ($cart->items->isEmpty()) {
            throw new InvalidArgumentException('Cart is empty');
        }

        return DB::transaction(function () use ($user, $cart, $billingData) {
            // Validate stock and lock products
            foreach ($cart->items as $item) {
                $product = $this->productRepository->findForUpdate($item->product_id);

                if (! $product || $product->stock_quantity < $item->quantity) {
                    $available = $product?->stock_quantity ?? 0;
                    throw new InvalidArgumentException(
                        "Insufficient stock for {$item->product->name}. Available: {$available}"
                    );
                }
            }

            // Calculate totals
            $subTotal = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
            $tax = round($subTotal * 0.1, 2); // 10% tax
            $totalAmount = $subTotal + $tax;

            // Prepare invoice data using provided billing details
            $invoiceData = [
                'user_id' => $user->id,
                'customer_name' => $billingData['name'] ?? $user->name,
                'customer_email' => $billingData['email'] ?? $user->email,
                'delivery_address' => $billingData['streetAddress'] ?? null,
                'city' => $billingData['city'] ?? null,
                'postal_code' => $billingData['zipCode'] ?? null,
                'country' => $billingData['countryRegion'] ?? null,
                'province' => $billingData['province'] ?? null,
                'phone' => $billingData['phone'] ?? null,
                'additional_info' => $billingData['additionalInfo'] ?? null,
                'sub_total' => $subTotal,
                'discount' => null,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'status' => InvoiceStatus::Pending,
                'payment_method' => $billingData['paymentMethod'] ?? 'bank',
                'order_no' => $this->generateOrderNumber(),
            ];

            // Prepare invoice items
            $items = $cart->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price,
            ])->toArray();

            // Create invoice
            $invoice = $this->invoiceRepository->create($invoiceData, $items);

            // Reduce stock (observer handles low stock notifications)
            foreach ($cart->items as $item) {
                $this->productRepository->reduceStock($item->product, $item->quantity);
            }

            // Mark cart as sold
            $this->cartRepository->markAsSold($cart);

            return $invoice;
        });
    }

    /**
     * Generate a unique order number.
     */
    private function generateOrderNumber(): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $max = strlen($chars) - 1;

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $chars[random_int(0, $max)];
            }
        } while ($this->invoiceRepository->orderNumberExists($code));

        return $code;
    }
}
