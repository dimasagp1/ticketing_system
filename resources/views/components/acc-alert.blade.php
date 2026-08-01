@props([
    'count' => 0,
    'route' => null,
])

@if($count > 0)
    <div class="mb-4 p-4 border-4 border-black bg-[#FF007A] text-white rounded-3xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] flex flex-wrap items-center justify-between gap-3 transition-all duration-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#FFE600] text-black border-3 border-black flex items-center justify-center font-fredoka font-black text-2xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                ⚠️
            </div>
            <div>
                <h4 class="font-fredoka font-black text-lg text-white mb-0 uppercase tracking-wide flex items-center gap-2">
                    ALERT ACC TIKET: {{ $count }} TIKET MENUNGGU PERSETUJUAN!
                    <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-0.5 rounded-full">ACTION REQUIRED</span>
                </h4>
                <p class="font-jakarta font-extrabold text-xs text-white/90 mb-0 mt-0.5">
                    Ada tiket baru yang membutuhkan persetujuan (ACC), evaluasi kelayakan, dan penetapan SLA dari Anda.
                </p>
            </div>
        </div>
        @if($route)
            <a href="{{ $route }}" class="inline-flex items-center gap-2 bg-[#FFE600] hover:bg-white text-black font-fredoka font-black border-3 border-black rounded-2xl px-4 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all text-sm no-underline shrink-0">
                <i class="fas fa-check-circle"></i> TINJAU & ACC SEKARANG
            </a>
        @endif
    </div>
@endif
