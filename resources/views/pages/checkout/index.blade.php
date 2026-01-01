<?php

use function Laravel\Folio\name;

name('checkout');

?>

<x-layouts.storefront>
    <livewire:page-banner :title="__('Checkout')" :links="[__('Home') => route('home'), __('Checkout') => '#']" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-20 gap-y-10">
            {{-- Billing Details --}}
            <div>
                <livewire:checkout.checkout-form />
            </div>

            {{-- Order Summary --}}
            <div class="pt-8 lg:pt-0">
                <livewire:checkout.checkout-summary />
            </div>
        </div>
    </div>

    {{-- Features Banner --}}
    <div class="bg-[#FAF3EA] dark:bg-zinc-800 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex items-center gap-4">
                    <div class="text-zinc-900 dark:text-white">
                        <flux:icon name="trophy" class="w-12 h-12" />
                    </div>
                    <div>
                        <h3 class="text-[25px] font-semibold text-[#242424] dark:text-white leading-tight">{{ __('High Quality') }}</h3>
                        <p class="text-[#898989] dark:text-zinc-400 font-medium">{{ __('crafted from top materials') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="text-zinc-900 dark:text-white">
                        <flux:icon name="check-badge" class="w-12 h-12" />
                    </div>
                    <div>
                        <h3 class="text-[25px] font-semibold text-[#242424] dark:text-white leading-tight">{{ __('Warranty Protection') }}</h3>
                        <p class="text-[#898989] dark:text-zinc-400 font-medium">{{ __('Over 2 years') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-zinc-900 dark:text-white">
                        <flux:icon name="truck" class="w-12 h-12" />
                    </div>
                    <div>
                        <h3 class="text-[25px] font-semibold text-[#242424] dark:text-white leading-tight">{{ __('Free Shipping') }}</h3>
                        <p class="text-[#898989] dark:text-zinc-400 font-medium">{{ __('Order over 150 $') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-zinc-900 dark:text-white">
                        <flux:icon name="users" class="w-12 h-12" />
                    </div>
                    <div>
                        <h3 class="text-[25px] font-semibold text-[#242424] dark:text-white leading-tight">{{ __('24 / 7 Support') }}</h3>
                        <p class="text-[#898989] dark:text-zinc-400 font-medium">{{ __('Dedicated support') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.storefront>
