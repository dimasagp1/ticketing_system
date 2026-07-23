@extends('layouts.toon')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-12">

    <!-- Header -->
    <div class="text-center py-6">
        <x-toon-badge color="yellow" size="md" class="mb-3">
            COMIC BOOK STOREFRONT
        </x-toon-badge>
        <h1 class="font-fredoka font-black text-4xl md:text-6xl text-black uppercase tracking-tight">
            TOON <span class="text-[#FF007A]">MERCH & GADGET</span> SHOP!
        </h1>
        <p class="font-jakarta font-extrabold text-base md:text-xl text-black max-w-2xl mx-auto mt-3">
            Koleksi merchandise resmi Saturday Morning, gadget zero-gravity, apparel komik, dan barang langka ToonWorld!
        </p>
    </div>

    <!-- Product Grid -->
    @php
        $products = [
            [
                'id' => 1,
                'name' => 'Anti-Gravity Bubble Jetpack',
                'category' => 'GADGETS',
                'price' => '89.99',
                'badge' => 'BESTSELLER',
                'color' => 'yellow',
                'icon' => '🚀',
                'desc' => 'Sistem pendorong zero-g resmi dengan bahan bakar permen karet kosmik bertenaga tinggi!',
            ],
            [
                'id' => 2,
                'name' => 'ToonWorld Oversized Hoodie',
                'category' => 'APPAREL',
                'price' => '54.50',
                'badge' => 'HOT MERCH',
                'color' => 'pink',
                'icon' => '🧥',
                'desc' => 'Hoodie komik super lembut dengan bordir garis tebal hitam dan patch Neo-Brutalist.',
            ],
            [
                'id' => 3,
                'name' => 'Captain Bubbly Vinyl Figure',
                'category' => 'COLLECTIBLES',
                'price' => '34.00',
                'badge' => 'LIMITED',
                'color' => 'blue',
                'icon' => '🤖',
                'desc' => 'Figur vinyl 10 inci buatan tangan dengan kepala lentur dan alas melayang metalik.',
            ],
            [
                'id' => 4,
                'name' => 'Zap Rabbit Lightning Cap',
                'category' => 'APPAREL',
                'price' => '28.00',
                'badge' => 'NEW',
                'color' => 'orange',
                'icon' => '🧢',
                'desc' => 'Topi komik dengan badge petir menyala dalam gelap (glow-in-the-dark).',
            ],
            [
                'id' => 5,
                'name' => 'Zero-G Quantum Slingshot',
                'category' => 'GADGETS',
                'price' => '42.00',
                'badge' => 'RARE',
                'color' => 'purple',
                'icon' => '🎯',
                'desc' => 'Meluncurkan marshmallow kartun hingga jarak 100 meter melayang di udara!',
            ],
            [
                'id' => 6,
                'name' => 'ToonCity Hologram NFT Card',
                'category' => 'DIGITAL ART',
                'price' => '19.99',
                'badge' => 'EXCLUSIVE',
                'color' => 'green',
                'icon' => '🎴',
                'desc' => 'Kartu koleksi 3D komik Sabtu pagi animasi dengan efek suara khas.',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($products as $prod)
            <x-toon-card :variant="$prod['color']" class="flex flex-col justify-between group">
                <!-- Top Badge Row -->
                <div class="flex items-center justify-between mb-3">
                    <x-toon-badge color="black" size="sm">
                        {{ $prod['category'] }}
                    </x-toon-badge>
                    <x-toon-badge color="pink" size="sm">
                        {{ $prod['badge'] }}
                    </x-toon-badge>
                </div>

                <!-- Image Box -->
                <div class="w-full h-52 bg-white/90 border-4 border-black rounded-2xl flex items-center justify-center relative overflow-hidden shadow-inner my-2 group-hover:rotate-1 transition-transform">
                    <span class="text-8xl group-hover:scale-110 transition-transform duration-300 select-none">
                        {{ $prod['icon'] }}
                    </span>

                    <!-- Sticker Price Tag -->
                    <div class="absolute bottom-3 right-3 bg-white border-3 border-black px-3 py-1 rounded-xl font-fredoka font-black text-lg text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        ${{ $prod['price'] }}
                    </div>
                </div>

                <!-- Info -->
                <div class="mt-4">
                    <h3 class="font-fredoka font-black text-xl uppercase">
                        {{ $prod['name'] }}
                    </h3>
                    <p class="font-jakarta font-extrabold text-sm opacity-90 mt-2 line-clamp-2">
                        {{ $prod['desc'] }}
                    </p>
                </div>

                <!-- BUY NOW Button -->
                <div class="mt-6">
                    <x-toon-button variant="blue" size="md" class="w-full" onclick="alert('Item {{ $prod['name'] }} berhasil ditambahkan ke keranjang belanja TOONWORLD! 🚀');">
                        🛒 BUY NOW — ${{ $prod['price'] }}
                    </x-toon-button>
                </div>
            </x-toon-card>
        @endforeach
    </div>

</div>
@endsection
