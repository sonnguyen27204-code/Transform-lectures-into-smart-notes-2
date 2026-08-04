<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Lectio - Transform lectures into smart notes with AI">

    <title>@yield('title', 'Dashboard') · Lectio</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%231c1917'/%3E%3Crect x='8' y='8' width='6' height='6' fill='%23292524'/%3E%3Crect x='18' y='8' width='6' height='6' fill='%23292524'/%3E%3Crect x='8' y='18' width='16' height='6' fill='%23292524'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        stone: {
                            50: '#fafaf9', 100: '#f5f5f4', 200: '#e7e5e3',
                            300: '#d6d3d1', 400: '#a8a29e', 500: '#78716c',
                            600: '#57534e', 700: '#44403c', 800: '#292524', 900: '#1c1917', 950: '#0c0a09',
                        },
                    },
                    boxShadow: {
                        'soft': '0 2px 8px -2px rgba(0,0,0,0.08), 0 4px 16px -4px rgba(0,0,0,0.06)',
                        'soft-lg': '0 4px 12px -4px rgba(0,0,0,0.1), 0 8px 24px -8px rgba(0,0,0,0.08)',
                    },
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }
        .dark body {
            background-color: #0c0a09;
            color: #fafaf9;
        }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 antialiased">

<div class="min-h-screen flex flex-col">
    {{-- Sidebar --}}
    @auth
        @include('partials.sidebar')
    @endauth

    {{-- Main --}}
    <div class="flex-1 flex flex-col lg:ml-64">
        {{-- Topbar --}}
        @auth
            @include('partials.topbar')
        @endauth

        {{-- Flash messages --}}
        <div class="px-6 lg:px-8 pt-4 space-y-2 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <x-ui.alert type="success" dismissible>{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error" dismissible>{{ session('error') }}</x-ui.alert>
            @endif
            @if(session('warning'))
                <x-ui.alert type="warning" dismissible>{{ session('warning') }}</x-ui.alert>
            @endif
            @if(session('info'))
                <x-ui.alert type="info" dismissible>{{ session('info') }}</x-ui.alert>
            @endif
        </div>

        {{-- Content --}}
        <main class="flex-1 px-6 lg:px-8 py-8 max-w-7xl mx-auto w-full">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('partials.footer')
    </div>
</div>

@stack('scripts')
</body>
</html>
