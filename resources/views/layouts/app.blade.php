<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'TanamKu - Katalog & Pemesanan Tanaman Hias')</title>

    <!-- Favicon: TanamKu Leaf Brand -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232F6B4F'><path d='M12 19l9 2-9-18-9 18 9-2zm0 0v-8'/></svg>">

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#2F6B4F', // Hijau tua primer
                            hover: '#24543D',
                            light: '#EAF2ED',
                        },
                        accent: {
                            DEFAULT: '#A3C9A8', // Hijau muda aksen
                            light: '#E3EFE5',
                        },
                        surface: '#F7F9F5',     // Latar lembut
                        dark: '#1F2937',        // Warna teks gelap
                        danger: '#DC2626',      // Bahaya / stok kritis
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7F9F5;
            color: #1F2937;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-surface text-dark antialiased">
    <!-- Navbar (panel admin memakai sidebar sendiri, tanpa navbar atas) -->
    @unless (request()->routeIs('admin.*'))
        @include('partials.navbar')
    @endunless

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer (hanya untuk publik & penjual) -->
    @unless (request()->routeIs('admin.*'))
        @include('partials.footer')
    @endunless

    @stack('scripts')
</body>
</html>
