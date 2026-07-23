<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'TOONWORLD — Anti-Gravity Saturday Morning Cartoon Zone' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white font-jakarta antialiased selection:bg-[#FF007A] selection:text-white min-h-screen flex flex-col justify-between overflow-x-hidden relative transition-colors duration-300">

    <!-- Comic Dots Background Pattern Overlay -->
    <div class="fixed inset-0 bg-comic-dots pointer-events-none z-0"></div>

    <!-- Floating Navigation Bar -->
    <header class="sticky top-4 z-50 px-4 max-w-7xl mx-auto w-full">
        <nav class="bg-[#121212] border-4 border-[#FFE600] rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-wrap items-center justify-between gap-4 relative overflow-hidden">
            <!-- Left Side: Logo & Quick Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Brand Logo Badge -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 bg-[#FFE600] border-4 border-black px-4 py-2 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-transform select-none">
                    <div class="w-8 h-8 bg-[#FF007A] border-2 border-black rounded-full flex items-center justify-center text-white font-fredoka font-black text-lg shadow-inner">
                        T
                    </div>
                    <div class="flex flex-col">
                        <span className="font-fredoka font-black text-lg md:text-xl tracking-wider text-black leading-none drop-shadow-[1px_1px_0px_#0055FF]">
                            TOONWORLD
                        </span>
                        <span className="text-[8px] font-extrabold uppercase tracking-widest text-[#FF007A]">
                            NEO-BRUTALIST CARTOONS
                        </span>
                    </div>
                </a>

                <!-- Quick Action Buttons -->
                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 bg-[#00E676] text-black border-3 border-black rounded-xl font-fredoka font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600]">
                            DASHBOARD
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-1.5 bg-[#00E676] text-black border-3 border-black rounded-xl font-fredoka font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600]">
                            DASHBOARD
                        </a>
                    @endauth

                    <!-- Zero Gravity Toggle Button -->
                    <button type="button" id="zero-g-toggle" class="border-3 border-black px-3.5 py-1.5 rounded-xl font-fredoka font-black text-xs flex items-center gap-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] bg-[#FFE600] text-black hover:bg-[#FF007A] hover:text-white transition-all select-none">
                        <span id="zero-g-icon" class="text-sm">🚀</span>
                        <span id="zero-g-text">ZERO-G MODE</span>
                    </button>
                </div>
            </div>

            <!-- Right Side Navigation Badges -->
            <div class="flex items-center gap-2 font-fredoka font-extrabold text-xs">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-2xl border-3 border-black flex items-center gap-1.5 transition-all {{ request()->routeIs('home') ? 'bg-[#FFE600] text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] scale-105' : 'bg-white text-black hover:bg-[#FFE600]' }}">
                    <span>✨</span> BERANDA
                </a>
                <a href="{{ route('toons.index') }}" class="px-4 py-2 rounded-2xl border-3 border-black flex items-center gap-1.5 transition-all {{ request()->routeIs('toons.index') ? 'bg-[#FF007A] text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] scale-105' : 'bg-white text-black hover:bg-[#FF007A] hover:text-white' }}">
                    <span>🚀</span> MEET THE TOONS
                </a>
                <a href="{{ route('shop.index') }}" class="px-4 py-2 rounded-2xl border-3 border-black flex items-center gap-1.5 transition-all {{ request()->routeIs('shop.index') ? 'bg-[#0055FF] text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] scale-105' : 'bg-white text-black hover:bg-[#0055FF] hover:text-white' }}">
                    <span>🛍️</span> TOON SHOP
                </a>
                <a href="{{ route('about') }}" class="px-4 py-2 rounded-2xl border-3 border-black flex items-center gap-1.5 transition-all {{ request()->routeIs('about') ? 'bg-[#FF6B00] text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] scale-105' : 'bg-white text-black hover:bg-[#FF6B00] hover:text-white' }}">
                    <span>📖</span> OUR STORY
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 my-6">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <!-- Footer matching screenshot reference -->
    <footer class="mt-16 border-t-4 border-[#FFE600] bg-black text-white relative overflow-hidden py-12 px-4 z-10">
        <div class="max-w-7xl mx-auto relative z-10 space-y-8">
            <!-- Newsletter Card -->
            <div class="bg-black border-4 border-[#FFE600] rounded-3xl p-6 md:p-8 shadow-[8px_8px_0px_0px_#FFE600] text-center max-w-3xl mx-auto">
                <span class="inline-block bg-[#FF007A] text-white border-2 border-black px-4 py-1 rounded-full font-fredoka font-black text-xs uppercase mb-3 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    COMIC NEWSLETTER
                </span>
                <h3 class="font-fredoka font-black text-3xl md:text-4xl text-white uppercase tracking-tight">
                    GET <span class="text-[#FFE600]">CARTOON NEWS</span> IN YOUR INBOX!
                </h3>
                <p class="font-jakarta font-extrabold text-xs md:text-sm text-gray-300 mt-2 max-w-md mx-auto">
                    Dapatkan kode rahasia zero-gravity, rilis karakter komik baru, dan diskon merchandise eksklusif!
                </p>

                <form onsubmit="alert('Terima kasih telah mendaftar di TOONWORLD Newsletter! 🚀'); return false;" class="mt-6 flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" required placeholder="Masukkan email kamu..." class="flex-1 font-jakarta font-extrabold px-5 py-3 rounded-full border-3 border-white bg-black text-white focus:outline-none focus:ring-4 focus:ring-[#FFE600] text-sm" />
                    <button type="submit" class="font-fredoka font-black text-sm px-6 py-3 rounded-full bg-[#0055FF] text-white border-3 border-black hover:bg-[#FF007A] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                        JOIN NOW 🚀
                    </button>
                </form>
            </div>

            <!-- Social Media Pill Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="#" class="bg-[#0055FF] text-white border-3 border-black px-5 py-2 rounded-full font-fredoka font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:scale-105 transition-transform">
                    💬 DISCORD
                </a>
                <a href="#" class="bg-[#FF007A] text-white border-3 border-black px-5 py-2 rounded-full font-fredoka font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:scale-105 transition-transform">
                    📺 YOUTUBE
                </a>
                <a href="#" class="bg-[#FF6B00] text-white border-3 border-black px-5 py-2 rounded-full font-fredoka font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:scale-105 transition-transform">
                    🐦 TWITTER / X
                </a>
                <a href="#" class="bg-[#00E676] text-black border-3 border-black px-5 py-2 rounded-full font-fredoka font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:scale-105 transition-transform">
                    📷 INSTAGRAM
                </a>
            </div>

            <!-- Bottom Copyright & Back to Top -->
            <div class="flex flex-col sm:flex-row items-center justify-between border-t-2 border-gray-800 pt-6 gap-4 font-fredoka font-extrabold text-xs text-white">
                <div class="flex items-center gap-2">
                    <span class="bg-[#FFE600] text-black px-3 py-1 rounded-full text-xs font-black">
                        TOONWORLD © 2026
                    </span>
                    <span>NEO-BRUTALIST CARTOON EDITION</span>
                </div>

                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="hover:underline flex items-center gap-1 bg-black border-2 border-white px-4 py-1.5 rounded-full shadow-[2px_2px_0px_0px_#FFE600] text-xs font-black">
                    🚀 BACK TO TOP
                </button>
            </div>
        </div>
    </footer>

    <!-- Zero-Gravity Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('zero-g-toggle');
            const icon = document.getElementById('zero-g-icon');
            const text = document.getElementById('zero-g-text');
            let zeroG = false;

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    zeroG = !zeroG;
                    if (zeroG) {
                        toggleBtn.classList.remove('bg-[#FFE600]', 'text-black');
                        toggleBtn.classList.add('bg-[#FF007A]', 'text-white', 'animate-pulse');
                        text.textContent = 'ZERO-G ON!';
                        icon.textContent = '🛸';
                        document.body.classList.add('zero-gravity-active');
                    } else {
                        toggleBtn.classList.remove('bg-[#FF007A]', 'text-white', 'animate-pulse');
                        toggleBtn.classList.add('bg-[#FFE600]', 'text-black');
                        text.textContent = 'ZERO-G MODE';
                        icon.textContent = '🚀';
                        document.body.classList.remove('zero-gravity-active');
                    }
                });
            }
        });
    </script>
</body>
</html>
