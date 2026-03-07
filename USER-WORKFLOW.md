# Alur Sistem Pengajuan Proyek (User Journey)

Dokumen ini menjelaskan langkah demi langkah bagaimana seorang **Client (User)** menggunakan sistem aplikasi Antrian Project untuk mengajukan, memantau, dan menyelesaikan proyek/tiket.

## Diagram Alir (Mermaid)

```mermaid
sequenceDiagram
    actor Client
    participant Sistem
    actor SuperAdmin
    actor Developer

    Note over Client,Sistem: FASE 1: PENGAJUAN
    Client->>Sistem: Mengisi Form Tiket Baru (Judul, Prioritas, Requirement, Lampiran)
    Sistem-->>Client: Memberikan Status "Open" atau "Pending Approval"
    
    Note over Sistem,SuperAdmin: FASE 2: PERSETUJUAN & PENUGASAN
    Sistem->>SuperAdmin: Notifikasi Pengajuan Baru
    SuperAdmin->>Sistem: Review Tiket
    alt Ditolak
        SuperAdmin-->>Sistem: Tolak Pengajuan
        Sistem-->>Client: Notifikasi Penolakan
    else Disetujui
        SuperAdmin->>Sistem: Approve & Tentukan Deadline (SLA)
        SuperAdmin->>Sistem: Assign ke Developer (Masuk Queue)
    end

    Note over Sistem,Developer: FASE 3: PENGERJAAN
    Sistem->>Developer: Notifikasi Tugas Baru di Queue
    Developer->>Sistem: Update Progress & Update Status In Progress
    Sistem-->>Client: Bisa melihat Persentase Progress Real-Time

    Note over Client,Developer: FASE 4: KOMUNIKASI & REVISI
    Client->>Developer: Diskusi via Fitur Chat
    Developer-->>Client: Konfirmasi / Minta Data Tambahan
    Client->>Sistem: Mengajukan Revisi (Jika Ada)
    
    Note over Developer,Sistem: FASE 5: PENYELESAIAN
    Developer->>Sistem: Upload Hasil Akhir & Tandai "Resolved"
    Sistem-->>Client: Notifikasi Proyek Selesai
    Client->>Sistem: Menyetujui Hasil (Tutup Tiket)
    Sistem-->>SuperAdmin: Proyek Tercatat Selesai
```

## Penjelasan Langkah Detail

### 1. Fase Pengajuan (Submission)
- User (*Client*) *login* dan masuk ke *Dashboard*.
- Mengeklik tombol **Tiket Baru** atau masuk menu **Project Requests > Buat Baru**.
- Mengisi detail proyek meliputi:
  - Judul Proyek
  - Kategori
  - Skala Prioritas (Rendah, Sedang, Tinggi)
  - Detail *Requirements* (Kebutuhan proyek)
  - Melampirkan *file* pendukung pendukung.
- Tiket akan masuk ke *database* dengan status `Open`.

### 2. Fase Persetujuan & Penugasan (Approval & Assignment)
- **Super Admin (atau Admin)** mendapatkan notifikasi tiket baru.
- Admin mengevaluasi kelayakan proyek. Jika proyek dirasa kurang sesuai, Admin bisa menolak (*reject*).
- Jika disetujui, Admin merubah status proyek menjadi `In Progress` (Atau *Assigning*), menyesuaikan *Deadline/SLA*, dan menugaskannya ke **Queue Developer** tertentu.

### 3. Fase Pengerjaan (Implementation)
- **Developer** (*Programmer/Designer*) mendapat notifikasi tugas baru.
- Developer memperbarui progres (*Progress Input*) secara rutin, seperti 10%, 50%, dsb., disertai keterangan pekerjaan hari itu.
- *Client* dapat melihat pergerakan progres ini secara *real-time* di *dashboard* mereka.

### 4. Fase Interaksi & Revisi (Interaction & Revision)
- Selama pengerjaan, jika *Client* atau *Developer* membutuhkan komunikasi langsung, mereka dapat menggunakan fitur **Chat**.
- Jika proyek sudah dikirim (misal versi Beta) namun *Client* menemukan ada yang kurang, *Client* bisa melakukan permintaan revisi memalui sistem, yang akan mencatat log revisi dan mengembalikan status ke antrian prioritas.

### 5. Fase Penyelesaian (Resolution)
- Setelah selesai, *Developer* mengunggah dokumen/hasil final dan merubah status tiket menjadi `Resolved`.
- *Client* mengecek hasil akhir. Jika telah sepakat dan sesuai, tiket dinyatakan `Closed` secara permanen.
- Data tiket ini kemudian akan otomatis terekap dalam **Laporan Sistem (PDF/Excel)** yang digunakan oleh manajemen untuk meninjau performa staf dan jumlah layanan yang berhasil diselesaikan.
