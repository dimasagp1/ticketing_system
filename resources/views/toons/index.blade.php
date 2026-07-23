@extends('layouts.toon')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-12">

    <!-- Header & Section Badge -->
    <div class="text-center py-6">
        <div class="inline-block bg-[#FF007A] text-white border-4 border-black px-6 py-2 rounded-full font-fredoka font-black text-xs md:text-sm uppercase tracking-wider shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-4 select-none">
            INTERACTIVE CHARACTER GALLERY
        </div>
        <h1 class="font-fredoka font-black text-5xl md:text-7xl text-[#0055FF] dark:text-[#0055FF] uppercase tracking-tight text-stroke-sm drop-shadow-[4px_4px_0px_#FFE600]">
            TOON
        </h1>
    </div>

    <!-- Characters Grid -->
    @php
        $characters = [
            [
                'name' => 'CAPTAIN BUBBLY',
                'role' => 'LEADER & JETPACK SPECIALIST',
                'color' => 'bg-[#FFE600]',
                'textColor' => 'text-black',
                'icon' => '🚀',
                'sfx' => 'BOING!',
                'bounciness' => 98,
                'chaos' => 45,
                'desc' => 'Captain Bubbly tercipta dalam ledakan soda kosmik! Dilengkapi jetpack permen karet...',
            ],
            [
                'name' => 'ZAP RABBIT',
                'role' => 'ELECTRIC SPEEDSTER',
                'color' => 'bg-[#FF007A]',
                'textColor' => 'text-black',
                'icon' => '⚡',
                'sfx' => 'ZAP!',
                'bounciness' => 75,
                'chaos' => 92,
                'desc' => 'Pelari kilat yang tak sengaja menembus pelangi listrik. Meninggalkan jejak percikan pink panas!',
            ],
            [
                'name' => 'ORBIT CAT',
                'role' => 'ZERO-G STARGAZER',
                'color' => 'bg-[#0055FF]',
                'textColor' => 'text-black',
                'icon' => '🐱‍🚀',
                'sfx' => 'WOOSH!',
                'bounciness' => 82,
                'chaos' => 60,
                'desc' => 'Kucing luar angkasa yang gemar melayang menangkap tikus bintang. Helmnya...',
            ],
            [
                'name' => 'DINO BOB',
                'role' => 'GENTLE HEAVYWEIGHT',
                'color' => 'bg-[#FF6B00]',
                'textColor' => 'text-black',
                'icon' => '🦖',
                'sfx' => 'KABOOM!',
                'bounciness' => 90,
                'chaos' => 88,
                'desc' => 'Dinosaurus raksasa terbuat dari karet komik 100%! Setiap kali menginjak bumi, tanah berubah...',
            ],
            [
                'name' => 'PROFESSOR KRAZY',
                'role' => 'MASTER OF PHYSICS',
                'color' => 'bg-white',
                'textColor' => 'text-black',
                'icon' => '🧪',
                'sfx' => 'TA-DA!',
                'bounciness' => 70,
                'chaos' => 95,
                'desc' => 'Otak di balik mesin zero-gravity ToonWorld! Menciptakan ketapel gravitasi dan dispenser...',
            ],
            [
                'name' => 'ASTRO PANDA',
                'role' => 'COSMIC BAMBOO DEFENDER',
                'color' => 'bg-[#00E676]',
                'textColor' => 'text-black',
                'icon' => '🐼',
                'sfx' => 'POP!',
                'bounciness' => 88,
                'chaos' => 40,
                'desc' => 'Mengorbit ToonWorld dalam kapsul bambu raksasa. Menyeimbangkan ketenangan dengan...',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($characters as $char)
            <div class="{{ $char['color'] }} {{ $char['textColor'] }} border-4 border-black rounded-3xl p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                
                <!-- Top Badges Row -->
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="bg-black text-white font-fredoka font-black text-[10px] md:text-xs px-3 py-1 rounded-full border-2 border-black tracking-wider uppercase truncate">
                        {{ $char['role'] }}
                    </span>
                    <span class="bg-black text-[#FFE600] font-fredoka font-black text-xs px-3 py-1 rounded-full border-2 border-black shrink-0">
                        🔊 {{ $char['sfx'] }}
                    </span>
                </div>

                <!-- Illustration Container Box -->
                <div class="w-full h-48 bg-white border-4 border-black rounded-2xl flex items-center justify-center text-7xl shadow-inner my-2 group-hover:scale-[1.02] transition-transform">
                    <span class="group-hover:scale-125 transition-transform duration-300 select-none">
                        {{ $char['icon'] }}
                    </span>
                </div>

                <!-- Character Name & Bio -->
                <div class="mt-3">
                    <h3 class="font-fredoka font-black text-2xl uppercase tracking-wide text-black">
                        {{ $char['name'] }}
                    </h3>
                    <p class="font-jakarta font-extrabold text-xs md:text-sm text-black leading-relaxed mt-1 line-clamp-2">
                        {{ $char['desc'] }}
                    </p>
                </div>

                <!-- Stat Bars -->
                <div class="mt-5 space-y-2 border-t-4 border-black pt-4 font-fredoka font-black text-xs text-black">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span>BOUNCINESS</span>
                            <span>{{ $char['bounciness'] }}%</span>
                        </div>
                        <div class="w-full bg-black/20 border-2 border-black rounded-full h-3 overflow-hidden p-0.5">
                            <div class="bg-black h-full rounded-full" style="width: {{ $char['bounciness'] }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-1">
                            <span>CHAOS LEVEL</span>
                            <span>{{ $char['chaos'] }}%</span>
                        </div>
                        <div class="w-full bg-black/20 border-2 border-black rounded-full h-3 overflow-hidden p-0.5">
                            <div class="bg-[#FF007A] h-full rounded-full" style="width: {{ $char['chaos'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Select Character Button -->
                <div class="mt-5">
                    <button onclick="alert('Karakter {{ $char['name'] }} terpilih! {{ $char['sfx'] }} 🚀');" class="w-full font-fredoka font-black text-xs md:text-sm uppercase py-3 rounded-full bg-black text-white hover:bg-[#FFE600] hover:text-black border-3 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 transition-all select-none flex items-center justify-center gap-2">
                        SELECT CHARACTER 🚀
                    </button>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
