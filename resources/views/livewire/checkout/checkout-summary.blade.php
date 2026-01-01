<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\PaymentMethod;
use Livewire\Attributes\On;

new class extends Component {
    public $cart;
    public $paymentMethod = '';

    /**
     * Initialize the summary with cart data and customer's default payment method.
     * Redirects to shop if cart is empty.
     */
    public function mount(CartService $cartService)
    {
        if (Auth::check()) {
            $this->cart = $cartService->getOrCreateActiveCart(Auth::user());
            
            // Get customer's default payment method
            $customer = Customer::where('user_id', Auth::id())->first();
            if ($customer) {
                $defaultPayment = PaymentMethod::where('customer_id', $customer->id)
                    ->where('is_default', true)
                    ->first();
                if ($defaultPayment) {
                    $this->paymentMethod = $defaultPayment->type;
                }
            }
        }

        if (!$this->cart || $this->cart->items->isEmpty()) {
            return $this->redirect(route('shop.index'));
        }
    }

    /**
     * Refresh cart from database when cart-updated event is dispatched.
     */
    #[On('cart-updated')]
    public function refreshCart(CartService $cartService)
    {
        if (Auth::check()) {
            $this->cart = $cartService->getOrCreateActiveCart(Auth::user());
        }
    }

    /**
     * Get the cart subtotal (sum of price * quantity for all items).
     *
     * @return float|int
     */
    public function getSubtotalProperty()
    {
        return $this->cart ? $this->cart->items->sum(fn($item) => $item->product->price * $item->quantity) : 0;
    }

    /**
     * Dispatch order submission event to checkout-form component.
     */
    public function placeOrder()
    {
        $this->dispatch('trigger-order-submission', paymentMethod: $this->paymentMethod)->to('checkout.checkout-form');
    }
};
?>

<div class="border-l border-gray-200 dark:border-zinc-700 pl-8 lg:pl-12">
    @if($cart)
        @foreach($cart->items as $item)
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-gray-500 dark:text-zinc-400">
                    {{ $item->product->name }} <span class="text-black dark:text-white font-medium ml-2">x {{ $item->quantity }}</span>
                </div>
                <div class="text-right text-gray-900 dark:text-white">
                    {{ config('app.currency_symbol') }} {{ number_format($item->product->price * $item->quantity, 0, '.', ',') }}
                </div>
            </div>
        @endforeach
    @endif

    <div class="grid grid-cols-2 gap-4 mb-4 pt-4">
        <div class="text-gray-900 dark:text-white">{{ __('Subtotal') }}</div>
        <div class="text-right text-gray-900 dark:text-white">
            {{ config('app.currency_symbol') }} {{ number_format($this->subtotal, 0, '.', ',') }}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="text-gray-900 dark:text-white">{{ __('Discount') }}</div>
        <div class="text-right text-gray-900 dark:text-white">
            {{ config('app.currency_symbol') }} 0
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="text-gray-900 dark:text-white">{{ __('Tax') }}</div>
        <div class="text-right text-gray-900 dark:text-white">
            {{ config('app.currency_symbol') }} 0
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-8 pt-4 border-b border-gray-200 dark:border-zinc-700 pb-8">
        <div class="text-gray-900 dark:text-white">{{ __('Total') }}</div>
        <div class="text-right text-2xl font-bold text-[#B88E2F]">
            {{ config('app.currency_symbol') }} {{ number_format($this->subtotal, 0, '.', ',') }}
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="space-y-4 mb-8">
        <div>
            <div class="flex items-center mb-2">
                <input id="direct-bank-transfer" type="radio" value="bank" wire:model.live="paymentMethod" class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                <label for="direct-bank-transfer" class="ml-3 block text-base font-medium text-gray-900 dark:text-white">
                    {{ __('Direct Bank Transfer') }}
                </label>
            </div>
            <p class="ml-7 text-sm text-gray-500 dark:text-zinc-400 font-light text-justify" x-show="$wire.paymentMethod === 'bank'">
                {{ __('Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.') }}
            </p>
        </div>

        <div>
            <div class="flex items-center">
                <input id="card" type="radio" value="card" wire:model.live="paymentMethod" class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                <label for="card" class="ml-3 block text-base font-medium text-gray-900 dark:text-white">
                    {{ __('Card') }}
                </label>
            </div>
            <p class="ml-7 text-sm text-gray-500 dark:text-zinc-400 font-light text-justify mt-2" x-show="$wire.paymentMethod === 'card'" x-cloak>
                {{ __('Pay securely with your credit or debit card.') }}
            </p>
        </div>

        <div>
            <div class="flex items-center">
                <input id="cash-on-delivery" type="radio" value="cash_on_delivery" wire:model.live="paymentMethod" class="h-4 w-4 text-black border-gray-300 focus:ring-black">
                <label for="cash-on-delivery" class="ml-3 block text-base font-medium text-gray-900 dark:text-white">
                    {{ __('Cash On Delivery') }}
                </label>
            </div>
            <p class="ml-7 text-sm text-gray-500 dark:text-zinc-400 font-light text-justify mt-2" x-show="$wire.paymentMethod === 'cash_on_delivery'" x-cloak>
                {{ __('Pay with cash upon delivery.') }}
            </p>
        </div>
    </div>

    <div class="mb-8">
        <p class="text-sm text-gray-900 dark:text-white text-justify">
            {{ __('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our') }} <span class="font-bold">{{ __('privacy policy.') }}</span>
        </p>
    </div>

    <div class="flex justify-end">
        <x-primary-button wire:click="placeOrder">
            {{ __('Place order') }}
        </x-primary-button>
    </div>
</div>
