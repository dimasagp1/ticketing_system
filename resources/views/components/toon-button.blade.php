@props([
    'variant' => 'blue',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'yellow' => 'bg-[#FFE600] text-black hover:bg-[#FF6B00] hover:text-white',
        'blue' => 'bg-[#0055FF] text-white hover:bg-[#FF007A]',
        'pink' => 'bg-[#FF007A] text-white hover:bg-[#FFE600] hover:text-black',
        'orange' => 'bg-[#FF6B00] text-white hover:bg-[#0055FF]',
        'green' => 'bg-[#00E676] text-black hover:bg-[#FFE600]',
        'purple' => 'bg-[#9000FF] text-white hover:bg-[#FF007A]',
        'white' => 'bg-white text-black hover:bg-[#FFFBEA]',
        'black' => 'bg-black text-white hover:bg-[#FFE600] hover:text-black',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs md:text-sm border-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]',
        'md' => 'px-6 py-3 text-sm md:text-base border-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:shadow-[9px_9px_0px_0px_rgba(0,0,0,1)] active:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
        'lg' => 'px-8 py-4 text-base md:text-xl border-4 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] active:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]',
    ];

    $variantClass = $variants[$variant] ?? $variants['blue'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $classes = "font-fredoka font-extrabold border-black rounded-2xl transition-all duration-150 cursor-pointer select-none inline-flex items-center justify-center gap-2 active:translate-x-[3px] active:translate-y-[3px] hover:-translate-y-1 " . $variantClass . " " . $sizeClass;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
