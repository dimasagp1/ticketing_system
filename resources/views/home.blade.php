@extends('layouts.toon')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-16">

    <!-- HERO SECTION -->
    <section class="relative py-12 md:py-20 flex flex-col items-center text-center overflow-hidden">
        <!-- Floating Sticker Badges -->
        <div class="inline-flex items-center gap-2 bg-[#FF007A] text-white border-4 border-black px-5 py-2 rounded-full font-fredoka font-black text-sm md:text-base shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] mb-6 select-none animate-wobble">
            <span>✨ SATURDAY MORNING CARTOON ZONE</span>
            <x-toon-badge color="yellow" size="sm">NEW!</x-toon-badge>
        </div>

        <!-- Giant Comic Headline -->
        <h1 class="font-fredoka font-black text-5xl md:text-7xl lg:text-8xl tracking-tight text-black leading-none uppercase select-none my-4">
            WELCOME TO THE <br />
            <span class="relative inline-block text-[#0055FF] text-stroke-md drop-shadow-[6px_6px_0px_#FFE600]">
                ANTI-GRAVITY
                <span class="absolute -top-6 -right-10 bg-[#FF6B00] text-white text-xs md:text-sm font-fredoka font-black px-3 py-1 rounded-xl border-3 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] rotate-12 hidden sm:inline-block">
                    PHYSICS READY!
                </span>
            </span>
            <span class="text-[#FF007A] text-stroke-md drop-shadow-[6px_6px_0px_#000000]">
                ZONE!
            </span>
        </h1>

        <!-- Subtitle -->
        <p class="font-jakarta font-extrabold text-lg md:text-2xl text-black max-w-2xl mx-auto my-6 leading-relaxed bg-[#FFFBEA] border-4 border-black rounded-2xl p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
            Rasakan petualangan Sabtu pagi bergaya komik retro Neo-Brutalisme dengan fisika zero-gravity, galeri karakter squishy, dan toko merchandise ikonik!
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-4 mt-4">
            <x-toon-button href="{{ route('toons.index') }}" variant="blue" size="lg">
                🚀 MEET THE TOONS
            </x-toon-button>

            <x-toon-button href="{{ route('shop.index') }}" variant="yellow" size="lg">
                ⚡ EXPLORE TOON SHOP
            </x-toon-button>

            <x-toon-button href="{{ route('about') }}" variant="pink" size="lg">
                📖 OUR STORY
            </x-toon-button>
        </div>

        <!-- Hero Character Floating Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 w-full max-w-5xl">
            <x-toon-card variant="yellow" class="text-center group cursor-pointer hover:rotate-3">
                <span class="text-6xl block mb-2 group-hover:scale-125 transition-transform duration-300">🚀</span>
                <h3 class="font-fredoka font-black text-xl text-black uppercase">CAPTAIN BUBBLY</h3>
                <x-toon-badge color="pink" size="sm" class="mt-2">BOING!</x-toon-badge>
            </x-toon-card>

            <x-toon-card variant="pink" class="text-center group cursor-pointer hover:-rotate-3">
                <span class="text-6xl block mb-2 group-hover:scale-125 transition-transform duration-300">⚡</span>
                <h3 class="font-fredoka font-black text-xl text-white uppercase">ZAP RABBIT</h3>
                <x-toon-badge color="yellow" size="sm" class="mt-2">ZAP!</x-toon-badge>
            </x-toon-card>

            <x-toon-card variant="blue" class="text-center group cursor-pointer hover:rotate-3">
                <span class="text-6xl block mb-2 group-hover:scale-125 transition-transform duration-300">🐱‍🚀</span>
                <h3 class="font-fredoka font-black text-xl text-white uppercase">ORBIT CAT</h3>
                <x-toon-badge color="orange" size="sm" class="mt-2">WOOSH!</x-toon-badge>
            </x-toon-card>

            <x-toon-card variant="orange" class="text-center group cursor-pointer hover:-rotate-3">
                <span class="text-6xl block mb-2 group-hover:scale-125 transition-transform duration-300">🦖</span>
                <h3 class="font-fredoka font-black text-xl text-white uppercase">DINO BOB</h3>
                <x-toon-badge color="green" size="sm" class="mt-2">KABOOM!</x-toon-badge>
            </x-toon-card>
        </div>
    </section>

    <!-- FEATURE HIGHLIGHTS PANEL -->
    <section class="py-8">
        <div class="text-center mb-10">
            <x-toon-badge color="yellow" size="md" class="mb-2">
                TOONWORLD FEATURES
            </x-toon-badge>
            <h2 class="font-fredoka font-black text-4xl md:text-5xl text-black uppercase">
                MENGAPA KAMU HARUS MASUK KE <span class="text-[#FF007A]">TOONWORLD?</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <x-toon-card variant="white" class="flex flex-col justify-between">
                <div class="w-16 h-16 bg-[#FFE600] border-4 border-black rounded-2xl flex items-center justify-center text-3xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] mb-4">
                    🎨
                </div>
                <h3 class="font-fredoka font-black text-2xl text-black uppercase">NEO-BRUTALIST DESIGN</h3>
                <p class="font-jakarta font-extrabold text-sm text-gray-700 mt-2 leading-relaxed">
                    Garis tebal 4px, bayangan kontras keras 6px, serta warna-warna pop komik Sabtu pagi yang memanjakan mata!
                </p>
                <div class="mt-6">
                    <x-toon-button href="{{ route('toons.index') }}" variant="yellow" size="sm" class="w-full">
                        LIHAT GALERI
                    </x-toon-button>
                </div>
            </x-toon-card>

            <x-toon-card variant="white" class="flex flex-col justify-between">
                <div class="w-16 h-16 bg-[#FF007A] border-4 border-black rounded-2xl flex items-center justify-center text-3xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] mb-4">
                    🚀
                </div>
                <h3 class="font-fredoka font-black text-2xl text-black uppercase">ZERO-GRAVITY PHYSICS</h3>
                <p class="font-jakarta font-extrabold text-sm text-gray-700 mt-2 leading-relaxed">
                    Nyalakan sakelar Zero-G Mode di navigasi untuk mengambangkan seluruh elemen kartun di layar secara bebas!
                </p>
                <div class="mt-6">
                    <x-toon-button href="{{ route('about') }}" variant="pink" size="sm" class="w-full">
                        BACA ORIGIN STORY
                    </x-toon-button>
                </div>
            </x-toon-card>

            <x-toon-card variant="white" class="flex flex-col justify-between">
                <div class="w-16 h-16 bg-[#0055FF] border-4 border-black rounded-2xl flex items-center justify-center text-3xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] mb-4">
                    🛍️
                </div>
                <h3 class="font-fredoka font-black text-2xl text-black uppercase">TOON SHOP MERCH</h3>
                <p class="font-jakarta font-extrabold text-sm text-gray-700 mt-2 leading-relaxed">
                    Dapatkan apparel, figur vinyl terbatas, dan gadget komik retro terbaik langsung dari Toon Shop!
                </p>
                <div class="mt-6">
                    <x-toon-button href="{{ route('shop.index') }}" variant="blue" size="sm" class="w-full">
                        KUNJUNGI SHOP
                    </x-toon-button>
                </div>
            </x-toon-card>
        </div>
    </section>

</div>
@endsection
