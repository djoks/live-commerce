<?php
use function Laravel\Folio\name;
use Illuminate\Support\Facades\Session;

name('checkout.success');
?>

<x-layouts.storefront>
    <div class="min-h-[60vh] flex flex-col items-center justify-center py-20 px-4">
        <div class="text-center max-w-lg">
            {{-- Illustration --}}
            <div class="mb-8 flex justify-center">
                <img src="{{ asset('build/images/checkout-complete.png') }}" alt="Order Successful" class="max-w-[220px] md:max-w-[250px]">
            </div>

            {{-- Success Message --}}
            <h1 class="text-xl md:text-xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('Your order has been placed!') }}
            </h1>

            @if(request('order_no'))
                <div class="mb-4">
                    <p class="text-gray-500 dark:text-zinc-400">{{ __('Order Number') }}</p>
                    <p class="text-2xl font-bold text-[#B88E2F]">{{ request('order_no') }}</p>
                </div>
            @endif

            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                {{ __('Thank you for your purchase.') }}<br>{{ __('Your order has been received.') }}
            </p>

            {{-- Home Button --}}
            <x-primary-button href="{{ route('home') }}">
                {{ __('Back to Home') }}
            </x-primary-button>
        </div>
    </div>
</x-layouts.storefront>
