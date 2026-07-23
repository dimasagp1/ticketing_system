<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TOONWORLD Ticketing Portal') }}</title>

        <!-- Google Fonts: Fredoka & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-jakarta text-black antialiased bg-[#FFFBEA] selection:bg-[#FF007A] selection:text-white min-h-screen flex flex-col justify-center items-center p-4 relative overflow-x-hidden">
        
        <!-- Comic Dots Background Pattern -->
        <div class="fixed inset-0 bg-comic-dots pointer-events-none z-0"></div>

        <div class="relative z-10 w-full max-w-md my-8 flex flex-col items-center">
            <!-- Brand Badge Logo -->
            <a href="{{ route('dashboard') }}" class="mb-6 inline-flex items-center gap-2 bg-[#FFE600] border-4 border-black px-5 py-2.5 rounded-full shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-transform select-none">
                @if(\App\Helpers\SettingsHelper::get('app_logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('app_logo')) }}" alt="Logo" class="h-8 w-auto max-w-[160px] object-contain">
                @else
                    <div class="w-8 h-8 bg-[#FF007A] border-2 border-black rounded-full flex items-center justify-center text-white font-fredoka font-black text-sm shadow-inner">
                        <i class="fas fa-life-ring"></i>
                    </div>
                    <span class="font-fredoka font-black text-xl tracking-wider text-black text-stroke-sm leading-none drop-shadow-[1px_1px_0px_#0055FF]">
                        {{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}
                    </span>
                @endif
            </a>

            <!-- Card Container -->
            <div class="w-full bg-white border-4 border-black rounded-3xl p-6 md:p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
