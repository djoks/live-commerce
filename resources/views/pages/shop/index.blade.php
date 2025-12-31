<?php

use function Laravel\Folio\name;

name('shop.index');

?>

<x-layouts.storefront title="Shop">
    {{-- Shop Header / Banner --}}
    <livewire:page-banner title="Shop" :links="['Shop' => route('shop.index')]" />

    {{-- Product Listing with Filter Bar --}}
    <livewire:shop.product-listing />
</x-layouts.storefront>
