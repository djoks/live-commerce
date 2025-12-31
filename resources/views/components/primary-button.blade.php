@props(['href' => null])

@php
    $classes = 'inline-block bg-[#B88E2F] hover:bg-[#a17a26] text-white text-base font-bold uppercase py-4 px-12 transition-colors duration-300 cursor-pointer rounded-[10px] text-center';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
