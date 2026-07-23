# TOONWORLD — Spesifikasi UI/UX & Design System (Laravel Refactoring Guide)

Dokumen ini berisi panduan komprehensif UI/UX, *design system*, dan langkah-langkah instruksional untuk melakukan renovasi/perombakan antarmuka (*frontend overhaul*) pada sistem berbasis **Laravel PHP** yang sudah ada agar mengadopsi estetika **TOONWORLD (Neo-Brutalism + Cartoon Aesthetic)**.

---

## 1. Konsep & Filosofi Desain

**TOONWORLD** menggabungkan dua estetika visual yang penuh energi:
* **Neo-Brutalism:** Karakteristik garis tepi tebal (*bold black borders*), bayangan tajam (*hard drop shadows* tanpa blur), kontras tinggi, dan warna-warna tak kenal takut.
* **Cartoon Pop-Art:** Bentuk-bentuk gelembung (*bubbly/organic curves*), elemen visual komik (panel, stiker, *burst icon*), serta kesan interaktif yang menyenangkan dan ramah pengguna.

---

## 2. Token Desain & Konfigurasi Tailwind CSS

Tambahkan ekstensi konfigurasi berikut pada file `tailwind.config.js` di proyek Laravel Anda:

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        toon: {
          yellow: '#FFE600',
          blue: '#0055FF',
          pink: '#FF007A',
          orange: '#FF6B00',
          green: '#00E676',
          purple: '#A855F7',
          cream: '#FFFBEA',
          dark: '#000000',
          white: '#FFFFFF',
        }
      },
      fontFamily: {
        comic: ['"Fredoka"', '"Bungee"', 'cursive', 'sans-serif'],
        body: ['"Plus Jakarta Sans"', 'sans-serif'],
      },
      boxShadow: {
        'toon-sm': '3px 3px 0px 0px #000000',
        'toon': '5px 5px 0px 0px #000000',
        'toon-lg': '8px 8px 0px 0px #000000',
        'toon-xl': '12px 12px 0px 0px #000000',
        'toon-pressed': '1px 1px 0px 0px #000000',
      },
      borderWidth: {
        '3': '3px',
        '4': '4px',
        '5': '5px',
        '6': '6px',
      },
      borderRadius: {
        'toon': '20px',
        'toon-lg': '28px',
        'toon-full': '9999px',
      }
    },
  },
  plugins: [],
}
```

---

## 3. Komponen Utama Blade (`resources/views/components/`)

### 3.1. Tombol Bubbly (`<x-toon-button>`)
*File: `resources/views/components/toon-button.blade.php`*

```html
@props([
    'variant' => 'yellow', // yellow, blue, pink, orange, green
    'size' => 'md',       // sm, md, lg
    'type' => 'button',
    'href' => null
])

@php
$bgColors = [
    'yellow' => 'bg-toon-yellow hover:bg-yellow-300 text-black',
    'blue'   => 'bg-toon-blue hover:bg-blue-600 text-white',
    'pink'   => 'bg-toon-pink hover:bg-pink-600 text-white',
    'orange' => 'bg-toon-orange hover:bg-orange-500 text-white',
    'green'  => 'bg-toon-green hover:bg-emerald-400 text-black',
];

$sizes = [
    'sm' => 'px-4 py-2 text-sm font-bold border-3 shadow-toon-sm rounded-xl',
    'md' => 'px-6 py-3 text-base font-extrabold border-4 shadow-toon rounded-2xl',
    'lg' => 'px-8 py-4 text-xl font-black border-4 shadow-toon-lg rounded-3xl',
];

$classes = "inline-flex items-center justify-center font-comic tracking-wide transition-all duration-150 active:translate-x-1 active:translate-y-1 active:shadow-toon-pressed border-black cursor-pointer " . ($bgColors[$variant] ?? $bgColors['yellow']) . " " . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
```

### 3.2. Panel Komik / Kartu (`<x-toon-card>`)
*File: `resources/views/components/toon-card.blade.php`*

```html
@props([
    'bgColor' => 'bg-white',
    'borderColor' => 'border-black',
    'shadow' => 'shadow-toon-lg',
    'rotate' => 'none' // none, left, right
])

@php
$rotations = [
    'none' => 'rotate-0',
    'left' => '-rotate-1 hover:rotate-0',
    'right' => 'rotate-1 hover:rotate-0',
];
@endphp

<div {{ $attributes->merge([
    'class' => "border-4 {$borderColor} {$bgColor} {$shadow} rounded-toon-lg p-6 transition-transform duration-200 " . ($rotations[$rotate] ?? 'rotate-0')
]) }}>
    {{ $slot }}
</div>
```

---

## 4. Arsitektur Halaman Utama

### 4.1. Beranda (`resources/views/home.blade.php`)
* **Hero Section:** Banner sambutan raksasa dengan tipografi komik melengkung/miring, stiker/elemen gelembung, dan tombol CTA besar *bubbly*.
* **Highlight Features:** Grid 3 kolom berisi kartu-kartu bergaya panel komik.
* **Banner Interaktif:** Promo cepat dengan latar belakang pola *polka dot* hitam-kuning.

### 4.2. Meet the Toons (`resources/views/toons/index.blade.php`)
* **Filter Bar:** Tombol kategori dengan warna pastel-neon berbeda.
* **Galeri Karakter:** Grid dinamis. Setiap kartu berisi potret karakter dengan bingkai tebal, tag statistik (Power, Speed, Fun), dan tombol bio pop-up/modal.

### 4.3. Toon Shop (`resources/views/shop/index.blade.php`)
* **Layout Grid Komik:** Produk diletakkan dalam kotak bergaris batas hitam tebal mirip halaman komik.
* **Harga & Diskon:** Penggunaan badge *burst/starburst* kuning-merah.
* **Quick Add to Cart:** Tombol interaktif dengan efek suara/animasi mikro saat diklik.

### 4.4. Our Story (`resources/views/about.blade.php`)
* **Timeline Strip:** Alur narasi horizontal/vertikal bergaya alur *comic strip* dengan ilustrasi di tiap poin penting.

---

## 5. Rencana Refactoring Laravel (Langkah Demi Langkah)

1. **Instalasi Asset & Asset Bundling:**
   * Install Tailwind CSS via Vite (`npm install -D tailwindcss postcss autoprefixer`).
   * Import Font Google (*Fredoka* & *Plus Jakarta Sans*) di file header `resources/views/layouts/app.blade.php`.
2. **Setup Base Layout:**
   * Buat `resources/views/layouts/toon.blade.php` sebagai master template baru.
3. **Migrasi View Bergradual:**
   * Ganti elemen HTML standar dengan Blade Components (`<x-toon-button>`, `<x-toon-card>`).
4. **Perbaikan Kontroller & Routing:**
   * Pertahankan logika bisnis (Controllers, Models, Database) yang sudah ada. Cukup sesuaikan parameter yang dilempar ke View jika ada komponen UI baru.

---
