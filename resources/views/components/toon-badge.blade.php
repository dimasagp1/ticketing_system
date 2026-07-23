@props([
    'color' => 'yellow',
    'size' => 'md',
])

@php
    $colors = [
        'yellow' => 'bg-[#FFE600] text-black',
        'pink' => 'bg-[#FF007A] text-white',
        'blue' => 'bg-[#0055FF] text-white',
        'orange' => 'bg-[#FF6B00] text-white',
        'green' => 'bg-[#00E676] text-black',
        'purple' => 'bg-[#9000FF] text-white',
        'black' => 'bg-black text-[#FFE600]',
        'white' => 'bg-white text-black',
    ];

    $sizes = [
        'sm' => 'text-[10px] md:text-xs px-2.5 py-0.5 border-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
        'md' => 'text-xs md:text-sm px-4 py-1.5 border-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]',
    ];

    $colorClass = $colors[$color] ?? $colors['yellow'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $classes = "font-fredoka font-black border-black rounded-full uppercase tracking-wider inline-flex items-center gap-1.5 select-none " . $colorClass . " " . $sizeClass;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
