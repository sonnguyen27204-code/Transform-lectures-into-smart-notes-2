<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Lectio') · AI Notes</title>

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
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }
    </style>

    @stack('head')
</head>
<body class="bg-stone-50 text-stone-900 antialiased">

@yield('content')

@stack('scripts')
</body>
</html>
