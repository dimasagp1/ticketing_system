# Blueprint Implementasi Ticketing Teknis

Dokumen ini berisi rencana implementasi lanjutan untuk menangani ticket masalah teknis operasional:

- wifi
- printer
- komputer
- software install
- supporting

Blueprint ini disesuaikan dengan struktur aplikasi saat ini (Laravel + modul ticketing pada `project_requests`).

## 1. Tujuan Implementasi

1. Menyediakan alur ticket teknis yang lebih cepat dari alur proyek biasa.
2. Menyediakan report progres yang berbeda untuk kebutuhan operasional, audit SLA, dan manajemen.
3. Menjaga kompatibilitas dengan modul eksisting (`ProjectRequest`, `SuperAdminController`, `ReportController`).

## 2. Kondisi Saat Ini (Ringkas)

1. `project_requests.ticket_category` sudah memiliki nilai `technical_support`.
2. Ticket sudah punya field SLA dasar: `sla_response_due_at`, `sla_resolution_due_at`, `first_responded_at`, `resolved_at`, `closed_at`.
3. Report existing di `SuperAdminController@reports` masih berfokus ke tren bulanan umum.
4. Report PDF existing di `ReportController@exportPdf` belum dipisah khusus report teknis.

## 3. Alur Ticketing Teknis yang Diusulkan

1. User membuat ticket kategori `technical_support`.
2. User memilih subkategori: `wifi`, `printer`, `komputer`, `software_install`, `supporting`.
3. Sistem hitung prioritas otomatis berdasarkan impact + urgency.
4. Ticket masuk status `open` dan antrian triage.
5. Admin/helpdesk melakukan triage, mengisi teknisi, lalu status `in_progress`.
6. Jika butuh data user, status `pending_user`.
7. Jika sementara dihentikan, status `paused`.
8. Jika selesai tindakan, status `resolved`.
9. User konfirmasi hasil, status `closed`.
10. Jika gagal, ticket dapat `reopened` (opsional tahap 2).

## 4. Perubahan Data (Tahap 1)

Tambahan kolom di `project_requests`:

1. `technical_subcategory` enum: `wifi`, `printer`, `komputer`, `software_install`, `supporting` (nullable).
2. `location_detail` string nullable.
3. `asset_code` string nullable.
4. `affected_users_count` unsigned integer default 1.
5. `escalation_level` tinyInteger default 0.
6. `escalated_at` timestamp nullable.
7. `reopened_count` unsigned tinyInteger default 0.

Index tambahan:

1. index `ticket_category`, `technical_subcategory`, `ticket_status`.
2. index `sla_resolution_due_at`, `ticket_status`.

Catatan:

1. Kolom ini dibuat nullable/bertahap agar aman untuk data ticket non-teknis.

## 5. Perubahan Validasi dan Bisnis Rules

Pada create/update ticket:

1. Jika `ticket_category = technical_support`, maka `technical_subcategory` wajib.
2. Jika `impact = critical` dan `urgency = critical`, tetapkan SLA paling ketat.
3. Saat status berubah ke `resolved`, isi `resolved_at` jika null.
4. Saat status berubah ke `closed`, isi `closed_at` jika null.
5. Saat status kembali dari `resolved/closed` ke aktif, naikkan `reopened_count`.

## 6. Matriks SLA Teknis (Awal)

1. Critical: response 15 menit, resolve 4 jam.
2. High: response 30 menit, resolve 8 jam.
3. Medium: response 2 jam, resolve 24 jam.
4. Low: response 4 jam, resolve 72 jam.

Formula prioritas:

- gunakan kombinasi impact + urgency.
- contoh nilai prioritas: `priority_score = impact_weight + urgency_weight`.

## 7. Desain Report Progres yang Berbeda

### A. Report Operasional Harian

Tujuan: monitoring cepat untuk tim support.

KPI utama:

1. Total ticket teknis masuk hari ini.
2. Ticket resolved hari ini.
3. Backlog aktif.
4. First response time rata-rata.
5. MTTR rata-rata.
6. SLA compliance harian.

Breakdown:

1. per subkategori teknis.
2. per prioritas.
3. per teknisi.

### B. Report Audit SLA dan Eskalasi

Tujuan: kontrol kepatuhan proses.

KPI utama:

1. Jumlah ticket breach SLA.
2. Distribusi aging ticket.
3. Jumlah eskalasi level 1/2.
4. Reopen rate.
5. Ticket overdue aktif.

### C. Report Manajerial Bulanan

Tujuan: analisa tren dan keputusan resource.

KPI utama:

1. Tren volume ticket teknis per bulan.
2. Top subkategori gangguan.
3. Lokasi/aset paling sering bermasalah.
4. Teknisi dengan beban tertinggi.
5. Tren MTTR dan SLA compliance bulanan.

## 8. Mapping Implementasi ke File

1. Migration baru:
   - `database/migrations/*_add_technical_ticket_fields_to_project_requests_table.php`
2. Model:
   - `app/Models/ProjectRequest.php` (fillable, casts, helper subkategori)
3. Controller ticket:
   - `app/Http/Controllers/ProjectRequestController.php` (validasi dan SLA)
4. Controller report super admin:
   - `app/Http/Controllers/SuperAdminController.php` (method report teknis)
5. Controller report PDF:
   - `app/Http/Controllers/ReportController.php` (filter `technical`)
6. Routes:
   - `routes/web.php` (route laporan teknis harian/audit/manajerial)
7. View report:
   - `resources/views/super-admin/reports-technical.blade.php`
   - `resources/views/reports/technical-pdf.blade.php`
8. Seeder role/user support (opsional tahap 2):
   - `database/seeders/RoleSeeder.php`

## 9. Desain Endpoint Report Teknis

1. `GET /super-admin/reports/technical`
   - dashboard report teknis (summary + chart + tabel).
2. `GET /super-admin/reports/technical/export?format=csv&period=daily`
   - export CSV report operasional.
3. `GET /reports/technical/pdf?period=weekly`
   - export PDF report teknis.

Filter query yang disarankan:

1. `from`, `to`
2. `subcategory`
3. `priority`
4. `assigned_to`
5. `location`

## 10. Query KPI (Contoh Eloquent)

Dataset dasar report teknis:

```php
$base = ProjectRequest::query()
    ->where('ticket_category', 'technical_support')
    ->when($from && $to, fn ($q) => $q->whereBetween('created_at', [$from, $to]));
```

SLA compliance:

```php
$resolved = (clone $base)->whereNotNull('resolved_at');

$withinSla = (clone $resolved)
    ->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')
    ->count();

$slaCompliance = $resolved->count() > 0
    ? round(($withinSla / $resolved->count()) * 100, 2)
    : 0;
```

MTTR (jam):

```php
$mttrHours = (clone $resolved)
    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) / 60 as mttr')
    ->value('mttr');
```

First response time (menit):

```php
$frtMinutes = (clone $base)
    ->whereNotNull('first_responded_at')
    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_responded_at)) as frt')
    ->value('frt');
```

## 11. Tahapan Pengerjaan (Rekomendasi)

1. Sprint 1
   - migration field teknis
   - validasi form + penyimpanan subkategori
2. Sprint 2
   - dashboard report teknis (operasional harian)
   - export CSV teknis
3. Sprint 3
   - report audit SLA + aging + eskalasi
   - report PDF teknis
4. Sprint 4
   - report manajerial bulanan + insight
   - hardening + UAT

## 12. UAT Checklist

1. Ticket teknis dapat dibuat dengan subkategori wajib.
2. SLA terisi otomatis sesuai prioritas.
3. Report harian menampilkan KPI dengan benar.
4. Export CSV/PDF sesuai filter.
5. Overdue dan breach SLA tampil konsisten.
6. Data non-teknis tidak rusak oleh perubahan baru.

## 13. Backward Compatibility

1. Semua report lama tetap berjalan.
2. Field baru bersifat tambahan dan tidak memaksa ticket non-teknis.
3. Status lama tetap dipakai (`open`, `in_progress`, `pending_user`, `paused`, `resolved`, `closed`, `cancelled`).

## 14. Catatan Role Support

Karena enum role saat ini hanya `client`, `developer`, `admin`, `super_admin`, implementasi awal bisa:

1. gunakan `admin` sebagai helpdesk,
2. gunakan `developer` sebagai teknisi.

Jika ingin role khusus `helpdesk` dan `technician`, perlu migration enum users + update middleware + update seeder.
