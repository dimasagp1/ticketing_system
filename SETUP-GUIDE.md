# Panduan Setup Database - Antrian Project

## 📋 Ringkasan File yang Sudah Dibuat

### ✅ Database Migrations (10 file baru)
1. `2025_12_20_164300_create_project_requests_table.php`
2. `2025_12_20_164301_create_project_requirements_table.php`
3. `2025_12_20_164302_create_project_approvals_table.php`
4. `2025_12_20_164303_create_project_revisions_table.php`
5. `2025_12_20_164304_create_project_stages_table.php`
6. `2025_12_20_164305_create_project_progress_logs_table.php`
7. `2025_12_20_164306_create_chat_conversations_table.php`
8. `2025_12_20_164307_add_conversation_to_chats_table.php`
9. `2025_12_20_164308_add_role_and_status_to_users_table.php`
10. `2025_12_20_164309_create_activity_logs_table.php`

### ✅ Models (11 file)
1. `ProjectRequest.php` - Model untuk project request
2. `ProjectRequirement.php` - Model untuk file requirements
3. `ProjectApproval.php` - Model untuk approval workflow
4. `ProjectRevision.php` - Model untuk revision
5. `ProjectStage.php` - Model untuk tahapan project
6. `ProjectProgressLog.php` - Model untuk tracking progress
7. `ChatConversation.php` - Model untuk conversation chat
8. `Chat.php` - Model untuk chat messages
9. `Queue.php` - Model untuk queue project
10. `ActivityLog.php` - Model untuk activity logs
11. `User.php` - Updated dengan role-based access

### ✅ Controllers (6 file)
1. `ProjectRequestController.php` - CRUD project requests
2. `ProjectApprovalController.php` - Approval workflow
3. `ProjectProgressController.php` - Progress tracking
4. `ChatController.php` - Chat functionality
5. `SuperAdminController.php` - Super admin dashboard
6. `UserManagementController.php` - User management

### ✅ Middleware (2 file)
1. `CheckRole.php` - Role-based access control
2. `CheckSuperAdmin.php` - Super admin only access

### ✅ Seeders (2 file)
1. `RoleSeeder.php` - Test users untuk semua role
2. `ProjectStageSeeder.php` - 9 tahapan workflow

### ✅ Routes
- `web.php` - Updated dengan 50+ routes baru

---

## 🚀 Cara Menjalankan Setup

### Opsi 1: Menggunakan Laragon Terminal (RECOMMENDED)

1. **Buka Laragon**
2. **Klik tombol "Terminal"** di Laragon (atau klik kanan pada Laragon tray icon → Terminal)
3. **Jalankan perintah berikut satu per satu:**

```bash
cd c:\laragon\www\antrian-project

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# Buat storage link
php artisan storage:link

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Opsi 2: Menggunakan Command Prompt

1. **Buka Command Prompt**
2. **Masuk ke folder Laragon PHP:**
```cmd
cd C:\laragon\bin\php\php-8.2.4-Win32-vs16-x64
```

3. **Jalankan perintah:**
```cmd
php.exe C:\laragon\www\antrian-project\artisan migrate:fresh --seed
php.exe C:\laragon\www\antrian-project\artisan storage:link
php.exe C:\laragon\www\antrian-project\artisan config:clear
```

### Opsi 3: Menggunakan Laragon Menu

1. **Klik kanan pada Laragon tray icon**
2. **Pilih: Quick app → antrian-project → Terminal**
3. **Jalankan:**
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

---

## 👥 Akun Login yang Tersedia

Setelah seeder dijalankan, Anda bisa login dengan akun berikut:

### Super Admin
- **Email:** superadmin@antrian.com
- **Password:** password
- **Akses:** Full access ke semua fitur

### Admin
- **Email:** admin@antrian.com
- **Password:** password
- **Akses:** Approval, user management (terbatas)

### Developer 1
- **Email:** developer1@antrian.com
- **Password:** password
- **Akses:** Assigned projects, chat, progress update

### Developer 2
- **Email:** developer2@antrian.com
- **Password:** password
- **Akses:** Assigned projects, chat, progress update

### Client 1
- **Email:** client1@example.com
- **Password:** password
- **Akses:** Create project request, chat, view progress

### Client 2
- **Email:** client2@example.com
- **Password:** password
- **Akses:** Create project request, chat, view progress

### Client 3
- **Email:** client3@example.com
- **Password:** password
- **Akses:** Create project request, chat, view progress

---

## 📊 Tahapan Project yang Tersedia

Setelah seeder dijalankan, sistem akan memiliki 9 tahapan workflow:

1. **Request Submitted** - Project request telah disubmit
2. **Under Review** - Sedang direview oleh admin
3. **Requirements Analysis** - Analisis kebutuhan
4. **Design Phase** - Fase desain
5. **Development** - Fase development
6. **Testing** - Fase testing
7. **Client Review** - Review oleh client
8. **Deployment** - Deployment ke production
9. **Completed** - Project selesai

---

## 🔍 Verifikasi Setup

Setelah menjalankan migrasi, cek apakah berhasil:

### 1. Cek Database
Buka phpMyAdmin atau database client Anda dan pastikan tabel-tabel berikut sudah ada:
- `project_requests`
- `project_requirements`
- `project_approvals`
- `project_revisions`
- `project_stages`
- `project_progress_logs`
- `chat_conversations`
- `activity_logs`

### 2. Cek Users
Tabel `users` harus memiliki 7 user dengan role berbeda-beda.

### 3. Cek Project Stages
Tabel `project_stages` harus memiliki 9 tahapan.

---

## 🎯 Fitur yang Sudah Tersedia

### 1. Request Project
- ✅ Upload requirement files (PDF, DOCX, images)
- ✅ Submit untuk approval
- ✅ Revision workflow
- ✅ Version control untuk files

### 2. Approval Workflow
- ✅ Admin bisa approve/reject
- ✅ Request revision dengan notes
- ✅ Auto convert ke queue saat approved

### 3. Progress Tracking
- ✅ 9 tahapan workflow
- ✅ Update progress per stage
- ✅ Activity logging
- ✅ Timeline view

### 4. Chat to Developer
- ✅ Real-time messaging (AJAX ready)
- ✅ File upload dalam chat
- ✅ Unread message tracking
- ✅ Conversation management

### 5. Super Admin Dashboard
- ✅ User management (CRUD)
- ✅ Activity logs dengan filter
- ✅ Reports dan analytics
- ✅ System settings

---

## 🔐 Role-Based Access Control

### Client
- Create project request
- Upload requirements
- View own projects
- Chat dengan developer
- View progress

### Developer
- View assigned projects
- Update progress
- Chat dengan client
- Log activities

### Admin
- Approve/reject requests
- Request revisions
- Assign projects
- View all data

### Super Admin
- Full access
- User management
- System settings
- Activity logs
- Reports

---

## 📁 Struktur Folder Storage

Pastikan folder berikut ada dan writable:

```
storage/
├── app/
│   └── public/
│       ├── requirements/     (untuk project requirement files)
│       └── chat-files/       (untuk chat file uploads)
└── logs/
```

Jika folder belum ada, akan dibuat otomatis saat upload file pertama kali.

---

## ⚠️ Troubleshooting

### Error: "SQLSTATE[42S01]: Base table or view already exists"
**Solusi:** Gunakan `migrate:fresh` untuk drop semua tabel dan buat ulang:
```bash
php artisan migrate:fresh --seed
```

### Error: "Class 'RoleSeeder' not found"
**Solusi:** Jalankan composer dump-autoload:
```bash
composer dump-autoload
php artisan migrate:fresh --seed
```

### Error: "The stream or file could not be opened"
**Solusi:** Pastikan folder storage writable:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Atau di Windows, klik kanan folder → Properties → Security → Edit → Full Control

---

## 🎨 Next Steps: Frontend Development

Backend sudah 100% selesai. Yang perlu dibuat selanjutnya adalah:

1. **Views** - Blade templates untuk UI
2. **AdminLTE Integration** - Untuk dashboard yang menarik
3. **JavaScript** - Untuk real-time chat dan AJAX
4. **CSS** - Untuk progress tracker dengan arrows

Semua controller dan routing sudah siap, tinggal buat tampilan UI-nya!

---

## 📞 Support

Jika ada pertanyaan atau error, silakan cek:
1. Laravel log: `storage/logs/laravel.log`
2. Activity logs di database
3. Browser console untuk JavaScript errors

---

**Selamat! Backend project Anda sudah lengkap! 🎉**
