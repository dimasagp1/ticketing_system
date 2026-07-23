@extends('layouts.toon')

@section('content')
<div class="max-w-5xl mx-auto px-4 space-y-12">

    <!-- Header -->
    <div class="text-center py-6">
        <x-toon-badge color="orange" size="md" class="mb-3">
            VERTICAL COMIC STRIP
        </x-toon-badge>
        <h1 class="font-fredoka font-black text-4xl md:text-6xl text-black uppercase tracking-tight">
            OUR <span class="text-[#FF007A]">TOONWORLD</span> ORIGIN STORY!
        </h1>
        <p class="font-jakarta font-extrabold text-base md:text-xl text-black max-w-xl mx-auto mt-3">
            Goresan komik strip interaktif mengenai kisah bagaimana gravitasi mendadak lenyap dari dunia ToonWorld!
        </p>
    </div>

    <!-- Timeline Comic Strip -->
    @php
        $chapters = [
            [
                'chapter' => 'CHAPTER 01',
                'title' => 'THE GRAVITY BOOM!',
                'date' => 'Tahun 2042',
                'color' => 'yellow',
                'badgeColor' => 'pink',
                'icon' => '💥',
                'sfx' => 'KABOOM!',
                'speech' => '"Tunggu... kenapa pancake sarapan kita melayang ke angkasa?!"',
                'content' => 'Berawal pada Sabtu pagi yang santai ketika Professor Krazy memadukan soda anti-materi dengan gummy bear pelangi. Ledakan dahsyat yang dihasilkan mengubah ToonWorld menjadi surga zero-gravity!',
            ],
            [
                'chapter' => 'CHAPTER 02',
                'title' => 'THE TOON SQUAD ASSEMBLES',
                'date' => 'Tahun 2043',
                'color' => 'pink',
                'badgeColor' => 'blue',
                'icon' => '🚀',
                'sfx' => 'BOING!',
                'speech' => '"Pasukan Bubbly, bersiap untuk lompatan bebas zero-gravity!"',
                'content' => 'Captain Bubbly, Zap Rabbit, dan Orbit Cat bergabung untuk membangun arena bermain melayang terbesar di galaksi. Mereka mendirikan ToonCity yang dikelilingi awan trampolin lentur.',
            ],
            [
                'chapter' => 'CHAPTER 03',
                'title' => 'THE ZERO-GRAVITY REVOLUTION',
                'date' => 'Masa Kini',
                'color' => 'blue',
                'badgeColor' => 'orange',
                'icon' => '⚡',
                'sfx' => 'ZAP!',
                'speech' => '"Bergabunglah ke zona anti-gravitasi dan bebaskan keceriaanmu!"',
                'content' => 'Kini, TOONWORLD terbuka untuk seluruh penjelajah galaksi! Semua pengunjung dapat mengaktifkan mode zero-g, mengoleksi merch langka, dan menikmati petualangan komik Sabtu pagi sepanjang hari.',
            ],
        ];
    @endphp

    <div class="space-y-10 relative before:absolute before:inset-0 before:left-1/2 before:-translate-x-1/2 before:w-2 before:bg-black before:hidden md:before:block">
        @foreach ($chapters as $idx => $panel)
            <x-toon-card :variant="$panel['color']" class="relative overflow-hidden border-4 border-black">
                <!-- Panel Header Bar -->
                <div class="flex items-center justify-between border-b-4 border-black pb-3 mb-4">
                    <x-toon-badge :color="$panel['badgeColor']" size="sm">
                        {{ $panel['chapter'] }} — {{ $panel['date'] }}
                    </x-toon-badge>
                    <x-toon-badge color="black" size="sm">
                        🔊 {{ $panel['sfx'] }}
                    </x-toon-badge>
                </div>

                <!-- Main Content Row -->
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <!-- Comic Artwork Box -->
                    <div class="w-36 h-36 md:w-44 md:h-44 bg-white/90 border-4 border-black rounded-3xl flex items-center justify-center text-7xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] shrink-0 relative">
                        {{ $panel['icon'] }}
                        <span class="absolute -bottom-2 -right-2 bg-black text-white font-fredoka font-black text-xs px-2 py-0.5 rounded-lg">
                            PANEL #{{ $idx + 1 }}
                        </span>
                    </div>

                    <!-- Narrative & Speech Bubble -->
                    <div class="flex-1 space-y-3">
                        <h3 class="font-fredoka font-black text-2xl md:text-3xl uppercase">
                            {{ $panel['title'] }}
                        </h3>

                        <!-- Speech Bubble -->
                        <div class="bg-white/80 border-3 border-black p-3.5 rounded-2xl relative shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                            <p class="font-fredoka font-extrabold text-sm md:text-base italic text-[#FF007A]">
                                {{ $panel['speech'] }}
                            </p>
                        </div>

                        <p class="font-jakarta font-extrabold text-base md:text-lg leading-relaxed opacity-95">
                            {{ $panel['content'] }}
                        </p>
                    </div>
                </div>
            </x-toon-card>
        @endforeach
    </div>

</div>
@endsection
