<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('user-password.edit')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <flux:navlist.item :href="route('two-factor.show')" wire:navigate>{{ __('Two-Factor Auth') }}</flux:navlist.item>
            @endif
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>

            <flux:modal.trigger name="logout-modal">
                <flux:navlist.item as="button" class="w-full text-start">{{ __('Log Out') }}</flux:navlist.item>
            </flux:modal.trigger>

            <flux:modal name="logout-modal" class="min-w-[400px]">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Log Out?') }}</flux:heading>
                        <flux:subheading>{{ __('Are you sure you want to log out of your account?') }}</flux:subheading>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <flux:button type="submit" variant="danger">{{ __('Log Out') }}</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
