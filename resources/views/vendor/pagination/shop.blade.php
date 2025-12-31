@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-3">
        {{-- Previous Page Link --}}
        @if (!$paginator->onFirstPage())
            <button 
                type="button"
                wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                wire:loading.attr="disabled"
                class="px-6 py-4 bg-[#F9F1E7] dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-lg font-medium hover:bg-[#e8ddd0] dark:hover:bg-zinc-600 transition-colors"
            >
                Prev
            </button>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-4 py-4 text-zinc-500">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span 
                            aria-current="page"
                            class="px-6 py-4 bg-[#B88E2F] text-white rounded-lg font-medium"
                        >
                            {{ $page }}
                        </span>
                    @else
                        <button 
                            type="button"
                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                            class="px-6 py-4 bg-[#F9F1E7] dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-lg font-medium hover:bg-[#e8ddd0] dark:hover:bg-zinc-600 transition-colors"
                        >
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button 
                type="button"
                wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                wire:loading.attr="disabled"
                class="px-6 py-4 bg-[#F9F1E7] dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-lg font-medium hover:bg-[#e8ddd0] dark:hover:bg-zinc-600 transition-colors"
            >
                Next
            </button>
        @endif
    </nav>
@endif
