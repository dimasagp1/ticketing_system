@props([
    'variant' => 'white',
    'header' => null,
    'footer' => null,
])

@php
    $variants = [
        'white' => 'bg-white text-black',
        'cream' => 'bg-[#FFFBEA] text-black',
        'yellow' => 'bg-[#FFE600] text-black',
        'pink' => 'bg-[#FF007A] text-white',
        'blue' => 'bg-[#0055FF] text-white',
        'orange' => 'bg-[#FF6B00] text-white',
        'green' => 'bg-[#00E676] text-black',
    ];

    $variantClass = $variants[$variant] ?? $variants['white'];
    $classes = "border-4 border-black rounded-3xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all duration-200 relative overflow-hidden " . $variantClass;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if ($header)
        <div className="border-b-4 border-black pb-4 mb-4 font-fredoka font-black text-xl uppercase">
            {{ $header }}
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>

    @if ($footer)
        <div className="border-t-4 border-black pt-4 mt-6">
            {{ $footer }}
        </div>
    @endif
</div>
