<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900 antialiased flex flex-col">
        {{-- Navbar --}}
        <x-navbar />

        {{-- Main Content (with top padding for fixed navbar) --}}
        <main class="pt-20 flex-1">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <x-footer />
        
        @fluxScripts
        

    </body>
</html>
