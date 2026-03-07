# Chat Widget Troubleshooting Guide

## 🔍 Langkah-Langkah Debug

### 1. Buka Browser Console
- Tekan **F12** di browser
- Pilih tab **Console**
- Refresh halaman (Ctrl+R)

### 2. Cek Error yang Muncul

Lihat apakah ada error seperti:
- ❌ `404 Not Found` - Route tidak ditemukan
- ❌ `419 Page Expired` - CSRF token issue
- ❌ `500 Internal Server Error` - Error di controller
- ❌ `403 Forbidden` - Authorization issue

### 3. Test Manual di Console

Jalankan command ini di browser console:

```javascript
// Test 1: Cek apakah jQuery loaded
console.log('jQuery version:', $.fn.jquery);

// Test 2: Cek CSRF token
console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

// Test 3: Test API conversations
$.ajax({
    url: '/api/chat/conversations',
    method: 'GET',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    },
    success: function(data) {
        console.log('✅ Conversations API works:', data);
    },
    error: function(xhr, status, error) {
        console.error('❌ Conversations API error:', xhr.status, xhr.responseText);
    }
});

// Test 4: Cek apakah chat widget element ada
console.log('Chat toggle exists:', $('#chat-toggle').length > 0);
console.log('Chat panel exists:', $('#chat-panel').length > 0);
```

## 🛠️ Common Issues & Solutions

### Issue 1: Chat Button Tidak Muncul

**Kemungkinan:**
- Chat widget partial tidak di-include

**Solusi:**
```bash
# Cek apakah file ada
ls resources/views/layouts/partials/chat-widget.blade.php

# Clear view cache
php artisan view:clear
```

### Issue 2: Error 404 pada /api/chat/conversations

**Kemungkinan:**
- Route belum di-register
- Cache route lama

**Solusi:**
```bash
# Clear route cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Cek route terdaftar
php artisan route:list | grep "api/chat"
```

Harus muncul:
```
GET|HEAD  api/chat/conversations
GET|HEAD  api/chat/{conversation}/messages
POST      api/chat/{conversation}/send
```

### Issue 3: Error 419 Page Expired

**Kemungkinan:**
- CSRF token tidak dikirim

**Solusi:**
Pastikan di `<head>` ada:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Issue 4: Conversations Tidak Load

**Kemungkinan:**
- Belum ada data conversation
- Authorization issue

**Solusi:**
```bash
# Cek di database
php artisan tinker
```

Lalu di tinker:
```php
// Cek jumlah conversations
\App\Models\ChatConversation::count();

// Cek conversations untuk user tertentu
$user = \App\Models\User::find(1);
$user->clientConversations()->count();
$user->developerConversations()->count();
```

### Issue 5: Send Message Gagal

**Kemungkinan:**
- CSRF token issue
- Validation error
- Authorization error

**Cek di Console:**
```javascript
// Test send message manual
$.ajax({
    url: '/api/chat/1/send',  // Ganti 1 dengan conversation ID yang ada
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    },
    data: {
        message: 'Test message from console'
    },
    success: function(data) {
        console.log('✅ Send message works:', data);
    },
    error: function(xhr) {
        console.error('❌ Send message error:', xhr.status, xhr.responseText);
    }
});
```

## 📋 Checklist Debugging

Centang yang sudah dicek:

- [ ] Browser console tidak ada error
- [ ] jQuery loaded (cek dengan `$.fn.jquery`)
- [ ] CSRF token ada di meta tag
- [ ] Chat widget element muncul di DOM
- [ ] Route `/api/chat/conversations` return 200
- [ ] Ada data conversation di database
- [ ] User sudah login
- [ ] Cache sudah di-clear

## 🔧 Quick Fix Commands

Jalankan semua command ini:

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list | grep chat

# Check if seeder ran
php artisan tinker
>>> \App\Models\User::count()
>>> \App\Models\ChatConversation::count()
>>> exit
```

## 📊 Expected Behavior

### Saat Klik Chat Button:
1. Panel muncul dari kanan bawah
2. Loading spinner muncul
3. AJAX call ke `/api/chat/conversations`
4. Conversations list muncul
5. Badge unread count update

### Saat Klik Conversation:
1. Chat window popup muncul
2. Loading spinner di chat body
3. AJAX call ke `/api/chat/{id}/messages`
4. Messages muncul di chat window
5. Auto-scroll ke bawah

### Saat Send Message:
1. Input disabled sementara
2. AJAX POST ke `/api/chat/{id}/send`
3. Message muncul di chat window
4. Input cleared dan enabled kembali
5. Auto-scroll ke bawah

## 🐛 Debug Mode

Tambahkan ini di console untuk debug mode:

```javascript
// Enable verbose logging
window.chatDebug = true;

// Monitor all AJAX calls
$(document).ajaxSend(function(event, jqxhr, settings) {
    console.log('📤 AJAX Request:', settings.type, settings.url);
});

$(document).ajaxComplete(function(event, jqxhr, settings) {
    console.log('📥 AJAX Response:', settings.url, jqxhr.status);
});

$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    console.error('❌ AJAX Error:', settings.url, jqxhr.status, jqxhr.responseText);
});
```

## 📞 Informasi yang Dibutuhkan untuk Debug

Jika masih belum bisa, berikan informasi ini:

1. **Error di Console** (screenshot atau copy text)
2. **Network Tab** - Status code dari request `/api/chat/conversations`
3. **Response** - Apa yang dikembalikan oleh API
4. **User Role** - Login sebagai apa? (client/developer/admin)
5. **Data** - Apakah ada conversation di database?

## 🎯 Test Scenario

### Scenario 1: Test dengan Data Dummy

```bash
php artisan tinker
```

```php
// Create test conversation
$client = \App\Models\User::where('role', 'client')->first();
$developer = \App\Models\User::where('role', 'developer')->first();

$conv = \App\Models\ChatConversation::create([
    'client_id' => $client->id,
    'developer_id' => $developer->id,
    'subject' => 'Test Chat Widget',
    'status' => 'active',
    'last_message_at' => now()
]);

// Create test message
\App\Models\Chat::create([
    'conversation_id' => $conv->id,
    'user_id' => $client->id,
    'message' => 'Hello from test!',
    'message_type' => 'text',
    'is_read' => false
]);

echo "✅ Test conversation created with ID: " . $conv->id;
exit
```

Sekarang coba buka chat widget dan lihat apakah conversation muncul.

---

**Beritahu saya:**
1. Error apa yang muncul di console?
2. Apa yang terjadi saat klik tombol chat?
3. Sudah jalankan `php artisan route:clear`?
