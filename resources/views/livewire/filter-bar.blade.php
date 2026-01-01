<?php

use App\Enums\ProductSortOption;
use Livewire\Volt\Component;

/**
 * Reusable Shop Filter Bar Component.
 *
 * Provides UI controls for pagination limit (perPage) and sorting order (sortBy).
 * Dispatches events to the parent component when values change.
 */
new class extends Component {
    public int $perPage = 16;
    public string $sortBy = 'default';

    public int $firstItem = 1;
    public int $lastItem = 16;
    public int $total = 0;

    public array $perPageOptions = [16, 32, 48];

    /**
     * Handle perPage value change and dispatch filter event.
     *
     * @param  int  $value  The new items per page value.
     */
    public function updatedPerPage(int $value): void
    {
        $this->dispatch('filterChanged', perPage: $value, sortBy: $this->sortBy);
    }

    /**
     * Handle sortBy value change and dispatch filter event.
     *
     * @param  string  $value  The new sort option value.
     */
    public function updatedSortBy(string $value): void
    {
        $this->dispatch('filterChanged', perPage: $this->perPage, sortBy: $value);
    }

    /**
     * Get available sort options from the enum.
     *
     * @return array<string, string>
     */
    public function getSortOptionsProperty(): array
    {
        return ProductSortOption::options();
    }
};
?>

<div class="bg-[#F9F1E7] dark:bg-zinc-800 py-6 mb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
        {{-- Left Side: Results Count --}}
        <p class="text-base text-zinc-900 dark:text-zinc-300">
            Showing <span class="font-medium">{{ $firstItem }}–{{ $lastItem }}</span> of <span class="font-medium">{{ $total }}</span> results
        </p>

        {{-- Right Side: Controls --}}
        <div class="flex w-full md:w-auto justify-between md:justify-start items-center md:gap-8">
            {{-- Show Per Page --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-1 md:gap-3">
                <label for="perPage" class="text-base md:text-xl text-zinc-900 dark:text-white">Show</label>
                <div class="relative bg-white dark:bg-zinc-900">
                    <select 
                        wire:model.live="perPage" 
                        id="perPage" 
                        class="appearance-none bg-white dark:bg-zinc-900 text-zinc-500 pl-4 pr-8 py-3 w-[65px] h-[55px] text-center cursor-pointer focus:outline-none"
                    >
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    {{-- Custom Caret --}}
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-zinc-500">
                        <flux:icon name="chevron-down" class="w-4 h-4" />
                    </div>
                </div>
            </div>

            {{-- Sort By --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-1 md:gap-3 flex-1 md:flex-none pl-4 md:pl-0">
                <label for="sortBy" class="text-base md:text-xl text-zinc-900 dark:text-white">Sort by</label>
                <div class="relative bg-white dark:bg-zinc-900 w-full md:w-auto">
                    <select 
                        wire:model.live="sortBy" 
                        id="sortBy" 
                        class="appearance-none bg-white dark:bg-zinc-900 text-zinc-500 pl-4 pr-8 py-3 w-full md:w-[188px] cursor-pointer focus:outline-none"
                    >
                        @foreach($this->sortOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
