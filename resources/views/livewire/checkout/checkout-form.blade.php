<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Http\Requests\PlaceOrderRequest;
use Monarobase\CountryList\CountryListFacade as Countries;

new class extends Component {
    // Billing Details
    public $name = '';
    public $countryRegion = '';
    public $streetAddress = '';
    public $city = '';
    public $province = '';
    public $zipCode = '';
    public $phone = '';
    public $email = '';
    public $additionalInfo = '';

    public function mount() {
        if(Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;

            // Prefill from customer profile if exists
            $customer = Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $this->streetAddress = $customer->delivery_address ?? '';
                $this->city = $customer->city ?? '';
                $this->zipCode = $customer->postal_code ?? '';
                $this->phone = $customer->phone ?? '';
                $this->countryRegion = $customer->country ?? '';
            }
        }
    }

    public function getCountriesProperty(): array
    {
        return Countries::getList('en');
    }

    #[On('trigger-order-submission')]
    public function submitOrder($paymentMethod)
    {
        $formRequest = new PlaceOrderRequest();
        
        $this->validate(
            collect($formRequest->rules())
                ->except(['paymentMethod']) // Payment method is validated in summary
                ->toArray(),
            $formRequest->messages(),
            $formRequest->attributes()
        );

        // Place order logic (mock)
        // In real app: Create Order model, attach items from cart, set billing address, set payment method.
        
        // Emulating processing delay
        sleep(1);

        // Success (Notification or Redirect)
        session()->flash('success', 'Order placed successfully!');
        $this->redirect(route('home'));
    }
};
?>

<div>
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Billing details</h2>
    
    <div class="space-y-6">
        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Name</label>
            <input type="text" wire:model="name" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Country / Region</label>
            <div class="relative">
                <select wire:model="countryRegion" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 appearance-none focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
                    @foreach($this->countries as $code => $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Street address</label>
            <input type="text" wire:model="streetAddress" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
            @error('streetAddress') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Town / City</label>
            <input type="text" wire:model="city" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Province</label>
            <div class="relative">
                <select wire:model="province" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 appearance-none focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
                    <option>Western Province</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">ZIP code</label>
            <input type="text" wire:model="zipCode" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
            @error('zipCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Phone</label>
            <input type="tel" wire:model="phone" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-base font-medium text-gray-900 dark:text-white">Email address</label>
            <input type="email" wire:model="email" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <input type="text" wire:model="additionalInfo" placeholder="Additional information" class="w-full rounded-[10px] border border-[#9F9F9F] dark:border-zinc-700 py-4 px-4 focus:ring-[#B88E2F] focus:border-[#B88E2F] dark:bg-zinc-800 dark:text-white mt-8">
        </div>
    </div>
</div>
