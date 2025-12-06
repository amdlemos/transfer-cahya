<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
        </style>
    @endif
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand href="#" logo="https://fluxui.dev/img/demo/logo.png"
                logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Cahya Inc" />

            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="/" wire:navigate>Home</flux:sidebar.item>
            <flux:sidebar.item icon="users" href="/users" wire:navigate>Usuários</flux:sidebar.item>
            <!-- <flux:sidebar.item icon="users" badge="12" href="/users" wire:navigate>Usuários</flux:sidebar.item> -->
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile avatar="https://fluxui.dev/img/demo/user.png" name="{{ auth()->user()->name }}" />
            <flux:menu>
                <flux:menu.item icon="arrow-right-start-on-rectangle" x-data @click.prevent="$refs.logoutForm.submit()">
                    Logout
                </flux:menu.item>

                <form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
    </flux:header>

    <flux:main>
        <flux:heading size="xl" level="1">
            Olá, {{ auth()->user()->name }}
        </flux:heading>
        <flux:text class="mt-2 mb-6 text-base">Here's what's new today</flux:text>
        <flux:separator variant="subtle" />
        {{ $slot }}
    </flux:main>
    @fluxScripts
</body>

</html>
