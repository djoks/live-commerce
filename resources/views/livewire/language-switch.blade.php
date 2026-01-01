<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

new class extends Component {
    public string $currentLocale = 'en';

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function toggleLocale(): void
    {
        $newLocale = $this->currentLocale === 'en' ? 'pl' : 'en';
        
        Session::put('locale', $newLocale);
        App::setLocale($newLocale);
        $this->currentLocale = $newLocale;
        
        $this->redirect(request()->header('Referer'), navigate: true);
    }
}; ?>

<button 
    wire:click="toggleLocale"
    class="p-2 text-zinc-600 dark:text-zinc-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors cursor-pointer flex items-center justify-center"
    aria-label="{{ __('Switch Language') }}"
>
    {{-- Language Icon --}}
    <img 
        src="{{ Vite::asset('resources/images/icons/lang.svg') }}" 
        alt="Language" 
        class="h-6 w-6 dark:invert"
    >
    <span class="ml-2 font-bold text-sm">{{ strtoupper($currentLocale) }}</span>
</button>
