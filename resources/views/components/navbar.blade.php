<nav x-data="{ mobileMenuOpen: false }" class="fixed w-full top-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Mobile Menu Button (Left) --}}
            <button 
                class="md:hidden p-2 -ml-2 text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 focus:outline-none"
                @click="mobileMenuOpen = true"
                aria-label="Open menu"
            >
                <flux:icon name="bars-3" class="h-6 w-6" />
            </button>

            {{-- Brand (Desktop Only) --}}
            <a href="{{ route('home') }}" class="hidden md:flex items-center gap-2" wire:navigate>
                <img 
                    src="{{ Vite::asset('resources/images/logo.png') }}" 
                    alt="Furniro Logo" 
                    class="h-8 w-auto"
                >
                <span class="font-montserrat text-2xl font-bold text-zinc-900 dark:text-white">
                    Furniro
                </span>
            </a>

            {{-- Navigation Menu (Desktop) --}}
            <div class="hidden md:flex items-center space-x-8">
                <a 
                    href="{{ route('home') }}" 
                    class="font-medium text-zinc-900 dark:text-white hover:text-amber-500 dark:hover:text-amber-400 transition-colors {{ request()->routeIs('home') ? 'text-amber-500 dark:text-amber-400' : '' }}"
                    wire:navigate
                >
                    Home
                </a>
                <a 
                    href="{{ route('shop.index') }}" 
                    class="font-medium text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors {{ request()->routeIs('shop.index') ? 'text-amber-500 dark:text-amber-400' : '' }}"
                    wire:navigate
                >
                    Shop
                </a>
                <a 
                    href="#" 
                    class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-500 dark:hover:text-amber-400 transition-colors"
                >
                    About
                </a>
                <a 
                    href="#" 
                    class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-500 dark:hover:text-amber-400 transition-colors"
                >
                    Contact
                </a>
                <a 
                    href="{{ route('profile.edit') }}" 
                    class="font-medium text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors {{ request()->routeIs('profile.edit') ? 'text-amber-500 dark:text-amber-400' : '' }}"
                    wire:navigate
                >
                    Account
                </a>
            </div>

            {{-- Actions --}}
            <div class="flex items-center space-x-4">
                {{-- Cart Icon --}}
                <livewire:cart-icon />

                {{-- Theme toggle using Flux's built-in utilities --}}
                <button 
                    x-data
                    @click="$flux.dark = ! $flux.dark"
                    class="p-2 text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors cursor-pointer"
                    aria-label="Toggle dark mode"
                >
                    {{-- Sun icon (shown in dark mode) --}}
                    <img 
                        x-show="$flux.dark"
                        x-cloak
                        src="{{ Vite::asset('resources/images/icons/sun.svg') }}" 
                        alt="Light mode" 
                        class="h-5 w-5 invert"
                    >
                    {{-- Moon icon (shown in light mode) --}}
                    <img 
                        x-show="! $flux.dark"
                        x-cloak
                        src="{{ Vite::asset('resources/images/icons/moon.svg') }}" 
                        alt="Dark mode" 
                        class="h-5 w-5"
                    >
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Drawer --}}
    <div class="relative z-50">
        {{-- Backdrop --}}
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-zinc-900/80"
            aria-hidden="true"
            @click="mobileMenuOpen = false"
            x-cloak
        ></div>

        {{-- Drawer Panel --}}
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 flex h-full w-4/5 max-w-sm flex-col bg-white dark:bg-zinc-900 shadow-xl"
            x-cloak
        >
            {{-- Drawer Header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate @click="mobileMenuOpen = false">
                    <img 
                        src="{{ Vite::asset('resources/images/logo.png') }}" 
                        alt="Furniro Logo" 
                        class="h-8 w-auto"
                    >
                    <span class="font-montserrat text-xl font-bold text-zinc-900 dark:text-white">
                        Furniro
                    </span>
                </a>
                <button 
                    @click="mobileMenuOpen = false" 
                    class="-mr-2 p-2 text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-300"
                >
                    <span class="sr-only">Close menu</span>
                    <flux:icon name="x-mark" class="h-6 w-6" />
                </button>
            </div>

            {{-- Drawer Content --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
                <nav class="flex flex-col space-y-8 items-center">
                    <a 
                        href="{{ route('home') }}" 
                        class="text-lg font-medium text-zinc-900 dark:text-white hover:text-amber-500 dark:hover:text-amber-400 {{ request()->routeIs('home') ? 'text-amber-500 dark:text-amber-400' : '' }}"
                        wire:navigate
                        @click="mobileMenuOpen = false"
                    >
                        Home
                    </a>
                    <a 
                        href="{{ route('shop.index') }}" 
                        class="text-lg font-medium text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 {{ request()->routeIs('shop.index') ? 'text-amber-500 dark:text-amber-400' : '' }}"
                        wire:navigate
                        @click="mobileMenuOpen = false"
                    >
                        Shop
                    </a>
                    <a 
                        href="#" 
                        class="text-lg font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-500 dark:hover:text-amber-400"
                        @click="mobileMenuOpen = false"
                    >
                        About
                    </a>
                    <a 
                        href="#" 
                        class="text-lg font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-500 dark:hover:text-amber-400"
                        @click="mobileMenuOpen = false"
                    >
                        Contact
                    </a>
                    <a 
                        href="{{ route('profile.edit') }}" 
                        class="text-lg font-medium text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 {{ request()->routeIs('profile.edit') ? 'text-amber-500 dark:text-amber-400' : '' }}"
                        wire:navigate
                        @click="mobileMenuOpen = false"
                    >
                        Account
                    </a>
</nav>
            </div>
        </div>
    </div>
    {{-- Cart Drawer --}}
    <livewire:cart-drawer />
</nav>
