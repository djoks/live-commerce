<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

    <flux:sidebar sticky stashable class="background-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="{{ config('app.name') }}" class="px-2" />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="/" :current="request()->is('/')">Hello</flux:navlist.item>
            
            <flux:navlist.group expandable heading="Settings" class="mt-4">
                <flux:navlist.item icon="wifi" href="#">Wifi</flux:navlist.item>
                <flux:navlist.item icon="archive-box" href="#">Archives</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        @if($user = auth()->user())
            <flux:dropdown position="bottom" align="start">
                <flux:profile avatar="https://i.pravatar.cc/150?u={{ $user->email }}" name="{{ $user->name }}" />

                <flux:menu>
                    <flux:menu.item icon="arrow-right-start-on-rectangle" href="/logout">Logout</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        @endif
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" />
        <flux:spacer />
    </flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    <flux:toast />

    @fluxScripts
</body>
</html>
