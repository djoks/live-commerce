<?php

use Livewire\Volt\Component;

/**
 * Reusable Breadcrumbs Navigation Component.
 *
 * Renders a navigation trail based on a provided array of links.
 * "Home" is automatically prepended.
 */
new class extends Component {
    /** 
     * @var array<string, string> Key is the label, value is the URL.
     * Example: ['Shop' => '/shop', 'Product' => '#'] 
     */
    public array $links = [];
};
?>

<nav class="flex items-center justify-center gap-2 font-medium text-base">
    <a href="{{ route('home') }}" class="text-zinc-900 font-medium hover:text-[#B88E2F] transition-colors">Home</a>
    
    @foreach($links as $label => $url)
        <span class="text-zinc-900">></span>
        @if($loop->last)
            <span class="text-zinc-900 font-light">{{ $label }}</span>
        @else
            <a href="{{ $url }}" class="text-zinc-900 font-medium hover:text-[#B88E2F] transition-colors">{{ $label }}</a>
        @endif
    @endforeach
</nav>
