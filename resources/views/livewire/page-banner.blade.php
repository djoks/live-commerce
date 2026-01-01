<?php

use Livewire\Volt\Component;

/**
 * Reusable Page Banner Component.
 *
 * Displays a full-width background image with a centered title and breadcrumbs.
 * Used on primary pages like Shop, Cart, and Checkout.
 */
new class extends Component {
    public string $title = '';

    /** @var array<string, string> */
    public array $links = [];
};
?>

<div class="relative w-full h-[316px]">
    {{-- Background Image --}}
    <img 
        src="{{ asset('build/images/shop-header.png') }}" 
        alt="{{ $title }} Banner" 
        class="w-full h-full object-cover"
    >
    
    {{-- Overlay Content --}}
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <h1 class="font-medium text-5xl text-zinc-900 mb-2">{{ $title }}</h1>
        
        {{-- Breadcrumbs --}}
        <livewire:breadcrumbs :links="$links" />
    </div>
</div>
