<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 

new class extends Component {
    public $count = 0;

    /**
     * Initialize the cart icon with the current item count.
     */
    public function mount(CartService $cartService)
    {
        $this->updateCartCount($cartService);
    }

    /**
     * Update the cart item count from the database.
     * Called on mount and when cart-updated event is dispatched.
     */
    #[On('cart-updated')] 
    public function updateCartCount(CartService $cartService) 
    {
        if (Auth::check()) {
             $cart = $cartService->getOrCreateActiveCart(Auth::user());
             $this->count = $cart->items->sum('quantity');
        } else {
            $this->count = 0;
        }
    }
};
?>

<button 
    class="relative p-2 text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors cursor-pointer"
    type="button"
    x-data
    @click="$dispatch('toggle-cart-drawer')"
>
    <img 
        src="{{ asset('build/images/icons/cart.svg') }}" 
        alt="Cart" 
        class="h-6 w-6 dark:invert"
    >
    
    @if($count > 0)
        <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-zinc-900 leading-none">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</button>
