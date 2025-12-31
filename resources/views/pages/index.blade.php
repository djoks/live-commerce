<?php

use function Laravel\Folio\name;

name('home');

?>

<x-layouts.storefront title="Home">
    {{-- Hero Section --}}
    <div class="relative w-full h-[600px] lg:h-[720px] bg-zinc-100 dark:bg-zinc-800">
        {{-- Background Image --}}
        <img 
            src="{{ Vite::asset('resources/images/cover.png') }}" 
            alt="New Arrival" 
            class="w-full h-full object-cover object-bottom"
        >

        {{-- Caption Card --}}
        <div class="absolute top-[10%] right-[5%] md:top-[20%] md:right-[10%] max-w-sm md:max-w-[643px] w-[calc(100%-2rem)] bg-[#FFF3E3] dark:bg-zinc-900/95 dark:backdrop-blur-sm px-6 py-8 md:px-[39px] md:py-[37px] rounded-[10px] shadow-sm">
            <div class="space-y-4">
                <span class="font-semibold tracking-[3px] uppercase text-zinc-900 dark:text-zinc-100 text-base">
                    New Arrival
                </span>
                
                <h1 class="font-poppins text-3xl md:text-[52px] font-bold text-[#B88E2F] leading-[1.2] pb-2">
                    Discover Our <br> New Collection
                </h1>

                <p class="text-zinc-900 dark:text-zinc-300 text-sm md:text-[18px] mb-8 font-medium leading-relaxed">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.
                </p>

                <div class="pt-8">
                    <x-primary-button href="{{ route('shop.index') }}" class="py-[25px] px-[72px]">
                        Buy Now
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Featured Products Section --}}
    <livewire:featured-products />

    {{-- Inspiration Section --}}
    <div class="w-full bg-white dark:bg-zinc-900 py-12 md:py-16">
        <div class="text-center mb-8 md:mb-12">
            <h3 class="font-medium text-lg md:text-xl text-zinc-600 dark:text-zinc-400 mb-2">
                Share your setup with
            </h3>
            <h2 class="font-bold text-3xl md:text-[40px] text-zinc-800 dark:text-white">
                #FuniroFurniture
            </h2>
        </div>

        <div class="w-full overflow-hidden">
            <img 
                src="{{ Vite::asset('resources/images/furniture-grid.png') }}" 
                alt="Furniture Grid Inspiration" 
                class="w-full h-auto object-cover"
            >
        </div>
    </div>
</x-layouts.storefront>
