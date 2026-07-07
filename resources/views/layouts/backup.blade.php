<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Backup Manager')) - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ route('backup.styles') }}">
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-gray-900 text-white flex flex-col shrink-0">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-lg font-semibold">{{ __('Backup Manager') }}</h1>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('backup.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('backup.index') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7a2 2 0 012-2h1m9 2h1a2 2 0 012 2M9 11h6m-6 4h3"/>
                    </svg>
                    {{ __('Backups') }}
                </a>
                <a href="{{ route('backup.settings') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('backup.settings') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('Settings') }}
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back to App') }}
                </a>
            </div>
        </aside>

        <main class="flex-1">
            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
