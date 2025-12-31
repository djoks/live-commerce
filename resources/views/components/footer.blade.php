<footer class="bg-white dark:bg-zinc-900 pt-12 pb-10 border-t border-zinc-200 dark:border-zinc-700 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Brand & Address --}}
            <div class="space-y-12">
                <h2 class="font-bold text-2xl text-zinc-900 dark:text-white">Funiro.</h2>
                
                <address class="not-italic text-[#9F9F9F] text-base leading-relaxed">
                    400 University Drive Suite 200 Coral Gables,<br>
                    FL 33134 USA
                </address>
            </div>

            {{-- Links --}}
            <div class="space-y-12">
                <h3 class="font-medium text-[#9F9F9F] text-base">Links</h3>
                
                <nav class="flex flex-col space-y-10">
                    <a href="{{ route('home') }}" class="font-medium text-zinc-900 dark:text-white hover:text-amber-600 transition-colors" wire:navigate>Home</a>
                    <a href="{{ route('shop.index') }}" class="font-medium text-zinc-900 dark:text-white hover:text-amber-600 transition-colors" wire:navigate>Shop</a>
                    <a href="#" class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-600 transition-colors">About</a>
                    <a href="#" class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-600 transition-colors">Contact</a>
                </nav>
            </div>

            {{-- Help --}}
            <div class="space-y-12">
                <h3 class="font-medium text-[#9F9F9F] text-base">Help</h3>
                
                <nav class="flex flex-col space-y-10">
                    <a href="#" class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-600 transition-colors">Payment Options</a>
                    <a href="#" class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-600 transition-colors">Returns</a>
                    <a href="#" class="font-medium text-zinc-400 dark:text-zinc-500 hover:text-amber-600 transition-colors">Privacy Policies</a>
                </nav>
            </div>

            {{-- Newsletter --}}
            <div class="space-y-12">
                <h3 class="font-medium text-[#9F9F9F] text-base">Newsletter</h3>
                
                <form class="flex gap-4 flex-wrap" onsubmit="event.preventDefault();">
                    <input 
                        type="email" 
                        placeholder="Enter Your Email Address" 
                        class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-3 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white placeholder-[#9F9F9F] text-sm flex-1 min-w-[200px]"
                    >
                    <x-primary-button class="text-sm py-3 px-8">
                        Subscribe
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>

    {{-- Separated Copyright Component --}}
    <x-copyright />
</footer>
