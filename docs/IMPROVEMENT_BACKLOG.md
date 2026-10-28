# SambungAsa — Improvement Backlog (Operational Roadmap)

Dokumen ini memuat daftar tugas operasional, peningkatan arsitektural, optimasi pengalaman pengguna (UI/UX), dan pemeliharaan teknis untuk melanjutkan pengembangan SambungAsa. Setiap item dirancang spesifik agar dapat langsung dijadikan instruksi kerja oleh software engineer.

---

### IMP-001: Integrasi Interactive Map Picker pada Form Profil Sekolah
- **Judul:** Widget Pemilihan Titik Koordinat Peta Interaktif (Leaflet.js / OpenStreetMap)
- **Deskripsi Masalah/Kebutuhan:** Saat ini perwakilan sekolah harus memasukkan koordinat latitude dan longitude secara manual dengan mengetik angka desimal pada form. Hal ini rawan terjadi *typo* (salah tanda minus atau posisi koma desimal) yang dapat menyebabkan titik lokasi sekolah terlempar ke luar peta wilayah 3T.
- **Bukti / Lokasi Kode:** [resources/views/school/profile.blade.php](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/resources/views/school/profile.blade.php#L68-L80)
- **Status Saat Ini:** `OPEN`
- **Prioritas:** `P1 — High`
- **Dependensi:** Leaflet.js CDN (aset ringan, zero build dependency)
- **Rencana Implementasi:**
  1. Sisipkan stylesheet dan script Leaflet.js versi terbaru pada *stack styles & scripts* di `resources/views/school/profile.blade.php`.
  2. Tambahkan kontainer peta `<div id="map" style="height: 280px; border-radius: 8px;"></div>` tepat di atas input koordinat.
  3. Inisialisasi peta berpusat pada koordinat default Indonesia Timur / koordinat profil sekolah saat ini.
  4. Pasang event listener `map.on('click')` dan marker `marker.on('dragend')` untuk mengisi otomatis input `#latitude` dan `#longitude`.
- **Acceptance Criteria:**
  - Pengguna dapat mengklik atau menggeser pin marker pada peta dan nilai input latitude/longitude terisi secara instan dan presisi hingga 7 angka desimal.
  - Marker berada di posisi awal yang tepat saat form edit dibuka.
- **Test yang Diperlukan:** Manual visual testing pada desktop dan mobile browser; form submit test memastikan nilai koordinat tersimpan ke basis data.
- **Status Pengerjaan:** `OPEN`

---

### IMP-002: Ekstraksi Business Logic ke Service Layer (Pledge & Need Management)
- **Judul:** Pemisahan Fat Controller ke `PledgeService` dan `NeedService`
- **Deskripsi Masalah/Kebutuhan:** Saat ini logika penguncian database (`lockForUpdate`), verifikasi kuota tersisa, pembuatan kode pelacakan, dan audit log berada langsung di dalam controller method (`PledgeController@store` dan `NeedController@store`). Untuk menjaga kepatuhan terhadap *Single Responsibility Principle (SRP)* dan memudahkan unit testing terisolasi, logika bisnis perlu dipindahkan ke layer Service khusus.
- **Bukti / Lokasi Kode:**
  - [app/Http/Controllers/Contributor/PledgeController.php:L40-L100](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/app/Http/Controllers/Contributor/PledgeController.php#L40-L100)
  - [app/Http/Controllers/School/NeedController.php:L50-L110](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/app/Http/Controllers/School/NeedController.php#L50-L110)
- **Status Saat Ini:** `OPEN`
- **Prioritas:** `P2 — Medium`
- **Dependensi:** Tidak ada
- **Rencana Implementasi:**
  1. Buat direktori baru `app/Services/`.
  2. Buat kelas `App\Services\PledgeService` yang menangani metode `createPledge(User $user, Need $need, array $data): Pledge`.
  3. Buat kelas `App\Services\NeedService` yang menangani metode `createNeed(SchoolProfile $school, array $data): Need`.
  4. Inject service kelas ke controller terkait melalui dependency injection.
- **Acceptance Criteria:**
  - Seluruh alur transaksi database, pengecekan anti over-pledging, dan pencatatan audit log diproses di dalam Service.
  - Controller hanya bertugas memvalidasi request HTTP dan mengembalikan view/redirect.
- **Test yang Diperlukan:** Eksekusi seluruh `Tests\Feature\PledgeFlowTest` dan `Tests\Feature\NeedManagementTest` wajib tetap lulus 100%.
- **Status Pengerjaan:** `OPEN`

---

### IMP-003: Integrasi Pengiriman Email Notifikasi Riil (SMTP Mail Driver)
- **Judul:** Notifikasi Email untuk Peristiwa Penting (Persetujuan Akun, Moderasi Kebutuhan, dan Perubahan Status Kargo)
- **Deskripsi Masalah/Kebutuhan:** Saat ini notifikasi hanya disimpan ke basis data lokal dan ditampilkan di antarmuka web `/notifications`. Donatur atau perwakilan sekolah yang tidak membuka website setiap saat berisiko terlambat mengetahui bahwa bantuannya telah diverifikasi atau tiba di pelabuhan/distrik tujuan.
- **Bukti / Lokasi Kode:** [app/Http/Controllers/NotificationController.php](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/app/Http/Controllers/NotificationController.php)
- **Status Saat Ini:** `OPEN`
- **Prioritas:** `P2 — Medium`
- **Dependensi:** Kredensial SMTP di `.env` (misal: Mailtrap, Brevo, atau Mailgun)
- **Rencana Implementasi:**
  1. Buat Mailable classes: `AccountVerifiedMail`, `PledgeStatusUpdatedMail`, `NeedModeratedMail`.
  2. Rancang template email responsif beraksen Red Broadcast (`resources/views/emails/`).
  3. Panggil `Mail::to($user->email)->queue(new PledgeStatusUpdatedMail(...))` saat status bantuan diperbarui.
- **Acceptance Criteria:**
  - Email terkirim secara asynchronous ke email pengguna yang bersangkutan saat terjadi pembaruan status krusial.
  - Template email menampilkan detail kode tracking, nama kebutuhan, dan tombol langsung ke halaman detail.
- **Test yang Diperlukan:** `Mail::fake()` test cases di `Tests\Feature\PledgeFlowTest` dan `Tests\Feature\AuthTest`.
- **Status Pengerjaan:** `OPEN`

---

### IMP-004: Ekspor Dokumen Berita Acara Serah Terima (BAST) Format PDF
- **Judul:** Pembuatan Lembar BAST Resmi Berkop dan Siap Cetak (PDF Generator)
- **Deskripsi Masalah/Kebutuhan:** Sekolah 3T dan korporasi donatur memerlukan dokumen Berita Acara Serah Terima fisik formal untuk keperluan laporan pertanggungjawaban dana CSR atau audit instansi pendidikan dinas. Saat ini platform baru menyediakan ekspor ringkasan dalam format CSV.
- **Bukti / Lokasi Kode:** [app/Http/Controllers/Admin/ReportController.php:L77-L185](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/app/Http/Controllers/Admin/ReportController.php#L77-L185)
- **Status Saat Ini:** `OPEN`
- **Prioritas:** `P2 — Medium`
- **Dependensi:** Composer package `barryvdh/laravel-dompdf`
- **Rencana Implementasi:**
  1. Pasang dependensi: `composer require barryvdh/laravel-dompdf`.
  2. Buat view template PDF: `resources/views/pdf/bast.blade.php` lengkap dengan format kop surat, klausul penyerahan, tabel rincian barang/pelatihan, dan kolom tanda tangan kepala sekolah serta perwakilan donatur.
  3. Tambahkan endpoint `GET /pledges/{pledge}/bast/download` pada `SchoolPledgeController` dan `Admin\PledgeManagementController`.
- **Acceptance Criteria:**
  - PDF dapat diunduh oleh sekolah penerima, donatur pemilik, dan admin.
  - Dokumen memuat identitas kedua pihak, kode tracking resmi `SA-YYYYMM-XXXX`, tanggal konfirmasi penerimaan, dan catatan kondisi barang.
- **Test yang Diperlukan:** Feature test memastikan response bertipe `application/pdf` dan hak akses terotorisasi.
- **Status Pengerjaan:** `OPEN`

---

### IMP-005: Form Manajemen Kategori & Referensi Wilayah di Portal Admin
- **Judul:** CRUD Antarmuka Admin untuk Penambahan Kategori Kebutuhan dan Pembaruan Wilayah 3T
- **Deskripsi Masalah/Kebutuhan:** Saat ini penambahan kategori kebutuhan baru dan pembaruan data kabupaten 3T harus melalui seeder basis data (`NeedCategorySeeder` & `RegionSeeder`). Administrator non-teknis memerlukan antarmuka visual sederhana di dashboard admin untuk mengelola referensi ini jika pemerintah menetapkan daerah 3T baru.
- **Bukti / Lokasi Kode:**
  - [app/Models/NeedCategory.php](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/app/Models/NeedCategory.php)
  - [app/Models/Region.php](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/app/Models/Region.php)
- **Status Saat Ini:** `OPEN`
- **Prioritas:** `P3 — Low`
- **Dependensi:** `routes/web.php` admin prefix
- **Rencana Implementasi:**
  1. Buat `Admin\CategoryController` dan `Admin\RegionController`.
  2. Daftarkan resource route pada prefix `admin`.
  3. Buat antarmuka data table dengan tombol modal tambah/edit kategori dan ubah status aktif/nonaktif.
- **Acceptance Criteria:**
  - Admin dapat menambah atau menonaktifkan kategori kebutuhan tanpa merusak data lama.
  - Kategori nonaktif tidak muncul pada dropdown formulir pengajuan kebutuhan sekolah baru.
- **Test yang Diperlukan:** Feature test CRUD kategori dan wilayah oleh Admin.
- **Status Pengerjaan:** `OPEN`

---

### IMP-006: Penyempurnaan Skrip Helper Environment Lokal (Developer Tooling)
- **Judul:** Penyediaan Script Runner `artisan.bat` untuk Standardisasi PHP 8.1
- **Deskripsi Masalah/Kebutuhan:** Pada lingkungan Windows developer, perintah `php` bawaan memanggil PHP 8.0 milik XAMPP. Tanpa script runner, developer lain di tim yang tidak mengetahui path Laragon dapat mengalami kendala eksekusi.
- **Bukti / Lokasi Kode:** Root workspace SambungAsa
- **Status Saat Ini:** `OPEN`
- **Prioritas:** `P2 — Medium`
- **Dependensi:** Tidak ada
- **Rencana Implementasi:**
  1. Buat file `artisan.bat` di root direktori yang secara eksplisit memanggil path PHP 8.1 yang valid atau mengecek versi PHP sebelum menjalankan artisan.
  2. Dokumentasikan panduan setting PATH Windows pada `README.md`.
- **Acceptance Criteria:**
  - Menjalankan `.\artisan.bat test` di PowerShell atau CMD otomatis menggunakan PHP 8.1.
- **Test yang Diperlukan:** Eksekusi `.\artisan.bat --version` dari terminal CMD biasa.
- **Status Pengerjaan:** `OPEN`
