# Troubleshooting Notification System

## Cara Test Notifikasi

### 1. Buka Browser Console
- Tekan F12 di browser
- Pilih tab "Console"
- Refresh halaman

### 2. Cek Error di Console
Anda akan melihat log seperti:
```
Notification counts: {total: 0, unread_messages: 0, ...}
```

### 3. Test Manual via Browser Console

Jalankan command ini di console:

```javascript
// Test get notification counts
$.get('/notifications/counts', function(data) {
    console.log('Counts:', data);
});

// Test get notifications list
$.get('/notifications/list', function(data) {
    console.log('List:', data);
});
```

### 4. Cek Route

Pastikan route berfungsi dengan mengakses langsung:
- `http://localhost/antrian-project/public/notifications/counts`
- `http://localhost/antrian-project/public/notifications/list`

Harus mengembalikan JSON.

### 5. Simulasi Notifikasi

Untuk test, buat data dummy:

**A. Buat Pesan Baru (sebagai client/developer)**
1. Login sebagai client1@example.com
2. Buka Chat → New Conversation
3. Kirim pesan
4. Login sebagai developer yang dituju
5. Badge harus muncul!

**B. Buat Approval Pending (sebagai client)**
1. Login sebagai client
2. Buat Project Request baru
3. Submit request
4. Login sebagai admin
5. Badge approval harus muncul!

### 6. Common Issues

**Issue: Badge tidak muncul**
- Cek console untuk error
- Pastikan sudah login
- Cek apakah ada data (pesan/approval)

**Issue: Error 404 on /notifications/counts**
- Jalankan: `php artisan route:clear`
- Jalankan: `php artisan cache:clear`

**Issue: Error 500**
- Cek Laravel log: `storage/logs/laravel.log`
- Mungkin ada error di NotificationController

### 7. Debug Mode

Tambahkan ini di console untuk debug:
```javascript
// Enable debug
setInterval(function() {
    $.get('/notifications/counts', function(data) {
        console.log('Auto-refresh:', new Date(), data);
    });
}, 5000); // Every 5 seconds
```

### 8. Manual Test Badge

Test badge secara manual di console:
```javascript
// Show badge with count
$('#notification-count').text('5').show();
$('#chat-notification-badge').text('3').show();

// Hide badge
$('#notification-count').hide();
$('#chat-notification-badge').hide();
```

## Expected Behavior

✅ **Saat ada notifikasi:**
- Badge merah muncul di bell icon
- Angka menunjukkan jumlah notifikasi
- Title browser: "(3) Antrian Project"
- Chat badge menunjukkan unread messages

✅ **Saat tidak ada notifikasi:**
- Badge tersembunyi
- Title browser: "Antrian Project"

✅ **Auto-refresh:**
- Update setiap 30 detik
- Tidak perlu refresh halaman

## Quick Fix Commands

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list | grep notification
```

Seharusnya muncul:
```
GET|HEAD  notifications/counts
GET|HEAD  notifications/list
POST      notifications/mark-read
```
