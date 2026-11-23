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
                    },
                    animation: {
                        'float': 'float 5s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 1.5s infinite',
                        'pulse-soft': 'pulseSoft 3s ease-in-out infinite',
                        'shimmer': 'shimmer 2.5s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-8px)' },
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1', transform: 'scale(1)' },
                            '50%': { opacity: '0.85', transform: 'scale(1.05)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        html {
            scroll-behavior: auto !important;
        }
        html.lenis, html.lenis body {
            height: auto;
        }
        .lenis.lenis-smooth {
            scroll-behavior: auto !important;
        }
        .lenis.lenis-smooth [data-lenis-prevent] {
            overscroll-behavior: contain;
        }
        .lenis.lenis-stopped {
            overflow: hidden;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Modern sleek scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
            border: 2px solid #F1F5F9;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        /* Selection Highlight */
        ::selection {
            background-color: #EAF2ED;
            color: #2F6B4F;
        }
        /* Smooth Global Form & Button Micro-interactions */
        input, select, textarea {
            transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
        }
        button, .btn, a.btn {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        button:active, .btn:active {
            transform: scale(0.97);
        }
        /* Smooth card lift */
        .card-lift {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }
        .card-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.09), 0 8px 16px -6px rgba(15, 23, 42, 0.04);
        }
        /* Animation & Interactive Utilities */
        @keyframes alertSlideIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.99);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .animate-alert-in {
            animation: alertSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-7px) rotate(1deg); }
        }
        .animate-float {
            animation: floatSlow 5s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: floatSlow 6.5s ease-in-out 1.5s infinite;
        }
        .hover-lift {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.09), 0 8px 16px -6px rgba(15, 23, 42, 0.04);
        }
        .active-press {
            transition: transform 0.15s ease-out;
        }
        .active-press:active {
            transform: scale(0.96);
        }
        /* Smooth marquee news ticker */
        @keyframes marqueeScroll {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: inline-flex;
            width: max-content;
            animation: marqueeScroll 35s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
        .mask-marquee {
            mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-surface text-dark antialiased">
    <!-- Top Scroll Progress Indicator (GSAP Motion) -->
    <div id="scroll-progress-bar" class="fixed top-0 left-0 right-0 h-[2.5px] bg-gradient-to-r from-emerald-500 via-teal-400 to-accent z-[60] origin-left scale-x-0 pointer-events-none"></div>

    <!-- Navbar (panel admin memakai sidebar sendiri, tanpa navbar atas) -->
    @unless (request()->routeIs('admin.*'))
        @include('partials.navbar')
    @endunless

    <!-- Main Content Area -->
    <main class="flex-grow pb-20 md:pb-0">
        @yield('content')
    </main>

    <!-- Footer (hanya untuk publik & penjual) -->
    @unless (request()->routeIs('admin.*'))
        @include('partials.footer')

        <!-- Sticky Mobile Bottom Navigation (Marketplace Style) -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] px-4 pt-2 pb-[calc(0.5rem+env(safe-area-inset-bottom,0px))] flex justify-around items-center">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('home') ? 'text-primary' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-bold">Beranda</span>
            </a>
            <a href="{{ route('plants.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('plants.*') ? 'text-primary' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-[10px] font-bold">Katalog</span>
            </a>
            @auth
                @if (auth()->user()->isBuyer())
                    <a href="{{ route('carts.index') }}" class="flex flex-col items-center gap-0.5 relative {{ request()->routeIs('carts.*') ? 'text-primary' : 'text-slate-500 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if (auth()->user()->cartCount() > 0)
                            <span class="absolute -top-1 right-2 min-w-[1rem] h-[1rem] px-1 rounded-full bg-danger text-white text-[9px] font-bold flex items-center justify-center">
                                {{ auth()->user()->cartCount() }}
                            </span>
                        @endif
                        <span class="text-[10px] font-bold">Keranjang</span>
                    </a>
                    <a href="{{ route('orders.mine') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('orders.mine') ? 'text-primary' : 'text-slate-500 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-[10px] font-bold">Pesanan</span>
                    </a>
                @elseif (auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('seller.*') ? 'text-primary' : 'text-slate-500 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-[10px] font-bold">Toko Saya</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-slate-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-[10px] font-bold">Akun</span>
                </a>
            @endauth
        </div>
    @endunless

    <!-- GSAP 3.12.5 & Lenis 1.1.20 CDN (jsDelivr High Speed) -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.20/dist/lenis.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Check user motion preference
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // 1. LENIS SMOOTH SCROLL (Native height preserved, synced with GSAP ticker)
            let lenis = null;
            if (typeof Lenis !== 'undefined' && !prefersReducedMotion) {
                try {
                    lenis = new Lenis({
                        duration: 1.35,
                        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                        orientation: 'vertical',
                        gestureOrientation: 'vertical',
                        smoothWheel: true,
                        wheelMultiplier: 0.9,
                        touchMultiplier: 1.5,
                    });

                    // Sync GSAP ticker with Lenis raf for zero jitter
                    if (typeof gsap !== 'undefined') {
                        gsap.ticker.add((time) => {
                            lenis.raf(time * 1000);
                        });
                        gsap.ticker.lagSmoothing(0);
                    } else {
                        function raf(time) {
                            lenis.raf(time);
                            requestAnimationFrame(raf);
                        }
                        requestAnimationFrame(raf);
                    }

                    window.lenis = lenis;

                    // Smooth anchor scroll using Lenis
                    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                        anchor.addEventListener('click', function (e) {
                            const targetId = this.getAttribute('href');
                            if (targetId && targetId !== '#' && document.querySelector(targetId)) {
                                e.preventDefault();
                                lenis.scrollTo(targetId, { offset: -70, duration: 1.2 });
                            }
                        });
                    });
                } catch (e) {
                    console.warn('Lenis fallback', e);
                }
            }

            // 2. GSAP MOTION ENHANCEMENTS (Visible, dynamic, zero layout breaking)
            if (typeof gsap !== 'undefined') {
                // Reading Progress Bar at the top of viewport (Synced with scroll)
                const progressBar = document.getElementById('scroll-progress-bar');
                if (progressBar) {
                    const updateProgress = () => {
                        const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
                        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                        const progress = height > 0 ? (winScroll / height) : 0;
                        gsap.to(progressBar, {
                            scaleX: progress,
                            duration: 0.1,
                            ease: 'none',
                            overwrite: 'auto'
                        });
                    };
                    if (lenis) {
                        lenis.on('scroll', updateProgress);
                    } else {
                        window.addEventListener('scroll', updateProgress, { passive: true });
                    }
                }

                // Smooth Header elevation & glassmorphism on scroll
                const headerEl = document.querySelector('header');
                if (headerEl) {
                    const updateHeader = () => {
                        const top = window.scrollY || document.documentElement.scrollTop;
                        if (top > 20) {
                            headerEl.classList.add('shadow-md', 'bg-white/95');
                        } else {
                            headerEl.classList.remove('shadow-md');
                        }
                    };
                    if (lenis) {
                        lenis.on('scroll', updateHeader);
                    } else {
                        window.addEventListener('scroll', updateHeader, { passive: true });
                    }
                }

                if (!prefersReducedMotion) {
                    // A. Animated Numeric Counters with GSAP
                    document.querySelectorAll('.stat-counter').forEach((el) => {
                        const target = parseFloat(el.getAttribute('data-target'));
                        const suffix = el.getAttribute('data-suffix') || '';
                        if (!isNaN(target)) {
                            const counterObj = { val: 0 };
                            gsap.to(counterObj, {
                                val: target,
                                duration: 1.8,
                                ease: 'power2.out',
                                onUpdate: () => {
                                    el.textContent = Math.floor(counterObj.val) + suffix;
                                }
                            });
                        }
                    });

                    // B. Hero Floating Motion (Balanced Left & Right)
                    gsap.to('.hero-float-left', {
                        y: -7,
                        duration: 3.5,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut'
                    });
                    gsap.to('.hero-float-1', {
                        y: -6,
                        duration: 3.2,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut'
                    });
                    gsap.to('.hero-float-2', {
                        y: 6,
                        duration: 3.8,
                        repeat: -1,
                        yoyo: true,
                        ease: 'sine.inOut'
                    });

                    // C. Ambient Orbs Gentle Horizontal Wave
                    document.querySelectorAll('.ambient-orb').forEach((orb, i) => {
                        gsap.to(orb, {
                            x: (i % 2 === 0 ? 18 : -18),
                            duration: 5 + i * 1.5,
                            repeat: -1,
                            yoyo: true,
                            ease: 'sine.inOut'
                        });
                    });

                    // D. Interactive Magnetic Spring on Cards & Buttons
                    document.querySelectorAll('.card-lift, .hover-lift').forEach((el) => {
                        el.addEventListener('mouseenter', () => {
                            gsap.to(el, { y: -5, duration: 0.25, ease: 'power2.out', overwrite: 'auto' });
                        });
                        el.addEventListener('mouseleave', () => {
                            gsap.to(el, { y: 0, duration: 0.25, ease: 'power2.out', overwrite: 'auto' });
                        });
                    });

                    // E. Subtle Hero Banner Entrance Glide (Non-destructive, translate only)
                    if (document.querySelector('.hero-main-banner')) {
                        gsap.from('.hero-main-banner', {
                            y: 16,
                            duration: 0.8,
                            ease: 'power3.out',
                            clearProps: 'all'
                        });
                    }
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
