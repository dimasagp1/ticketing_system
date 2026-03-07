# Facebook-Style Popup Chat Widget

## 🎨 Fitur Chat Popup

Chat widget telah ditambahkan ke sistem dengan gaya Facebook Messenger!

### ✨ Fitur Utama

1. **Floating Chat Button**
   - Tombol chat melayang di pojok kanan bawah
   - Badge notifikasi untuk pesan baru
   - Gradient purple yang menarik

2. **Chat Panel (Daftar Conversation)**
   - Popup panel untuk melihat semua conversation
   - Search bar untuk mencari conversation
   - Avatar dengan inisial nama
   - Preview pesan terakhir

3. **Multiple Chat Windows**
   - Bisa membuka hingga 3 chat window sekaligus
   - Setiap window bisa di-minimize/maximize
   - Auto-scroll ke pesan terbaru
   - Kirim pesan dengan Enter atau tombol send

### 🎯 Cara Menggunakan

1. **Buka Chat Panel**
   - Klik tombol chat di pojok kanan bawah
   - Panel akan muncul dengan daftar conversation

2. **Buka Chat Window**
   - Klik conversation yang ingin dibuka
   - Window chat akan muncul di sebelah kiri tombol chat
   - Maksimal 3 window bisa dibuka bersamaan

3. **Kirim Pesan**
   - Ketik pesan di input box
   - Tekan Enter atau klik tombol send
   - Pesan akan muncul di bubble biru (sent)

4. **Minimize/Close Window**
   - Klik tombol minus (-) untuk minimize
   - Klik tombol X untuk close
   - Klik header untuk restore window yang di-minimize

### 🎨 Desain

- **Gradient Purple** - Warna utama chat widget
- **Bubble Messages** - Pesan dalam bentuk bubble seperti messenger
- **Smooth Animations** - Transisi halus saat buka/tutup
- **Responsive** - Otomatis menyesuaikan ukuran layar
- **Auto-scroll** - Scroll otomatis ke pesan terbaru

### 📱 Posisi Widget

```
┌─────────────────────────────┐
│                             │
│        Main Content         │
│                             │
│                             │
│                    ┌────┐   │
│                    │Win3│   │
│               ┌────┤Win2│   │
│          ┌────┤Win1│    │   │
│     ┌────┤List│    │    │   │
│     │    └────┘    │    │   │
│     └────┘         └────┘   │
│                         (●) │ ← Chat Button
└─────────────────────────────┘
```

### 🔧 Customization

Anda bisa mengubah:
- Warna gradient di CSS
- Maksimal jumlah window (default: 3)
- Ukuran window
- Auto-refresh interval (default: 5 detik)

### 📝 File yang Ditambahkan

- `resources/views/layouts/partials/chat-widget.blade.php` - Widget component
- Updated `resources/views/layouts/app.blade.php` - Include widget

### 🚀 Fitur Lanjutan (Opsional)

Untuk implementasi penuh, Anda bisa menambahkan:
- Laravel Echo untuk real-time messaging
- Pusher/Socket.io untuk instant updates
- Typing indicator
- Online/offline status
- File upload dalam popup
- Emoji picker
- Message reactions

---

**Status:** ✅ Ready to Use
**Style:** Facebook Messenger
**Position:** Bottom-right corner
