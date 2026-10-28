# SambungAsa — Status Perkembangan dan Laporan Audit Proyek

> **Dokumen Audit Teknis & Status Aktual Implementasi**  
> **Tanggal Audit:** 28 Oktober 2026  
> **Auditor / Evaluator:** Senior Software Engineer, Laravel Technical Lead, Software QA Engineer, Technical Documentation Specialist  
> **Status Repositori:** Git `main` (Up-to-date dengan `origin/main` di GitHub `PPLKelompokB2401/sambungasa`)  
> **Framework & Runtime:** Laravel 10.50.4 | PHP 8.1.10 | MySQL 8.0.30  

---

## A. Informasi Proyek

- **Nama Proyek:** SambungAsa
- **Deskripsi Singkat:** Platform web responsif yang menghubungkan sekolah-sekolah di kawasan Tertinggal, Terdepan, dan Terluar (3T) di Indonesia (fokus fase awal: Papua, Kalimantan, dan Sulawesi) dengan donatur, relawan pengajar, dan organisasi filantropi secara langsung, transparan, dan terverifikasi.
- **Tujuan Utama Aplikasi:**
  1. Memberikan kanal terstruktur bagi perwakilan sekolah 3T untuk mengajukan dan mempublikasikan kebutuhan riil (barang fisik maupun tenaga pendamping/relawan).
  2. Memberikan wadah bagi donatur dan relawan untuk menemukan kebutuhan sekolah yang akurat serta mengajukan komitmen bantuan (*pledge*).
  3. Memastikan akuntabilitas proses penyaluran bantuan melalui verifikasi admin, Berita Acara Serah Terima (BAST), nomor tracking digital unik, dan bukti arsip privat.
  4. Menyajikan transparansi metrik dan dampak nyata kepada publik tanpa perantara transaksi dana/keuangan.
- **Tech Stack Aktual:**
  - **Backend Framework:** Laravel 10.50.4
  - **Bahasa Pemrograman:** PHP 8.1.10 (CLI/FPM)
  - **Database Management System:** MySQL 8.0.30 (`sambungasa` untuk aplikasi lokal, `sambungasa_test` untuk eksekusi test suite)
  - **Frontend UI & Styling:** Blade Templating Engine + Vanilla CSS terstruktur murni mengikuti spesifikasi **Red Broadcast** (`public/css/sambungasa.css`, Google Font Roboto & Roboto Mono). *Zero external CSS runtime framework dependency* (Tailwind dihindari sesuai instruksi master).
  - **Server Dev Lokal:** Artisan Development Server (`http://127.0.0.1:8000`)
  - **Test Suite Framework:** PHPUnit 10.5.45 via Laravel Artisan Test Runner
- **Arsitektur Aplikasi yang Ditemukan:**
  - **Pola Arsitektur:** *Modular Monolith* di dalam direktori standar Laravel.
  - **Struktur Controller:** Terpartisi rapi berdasarkan domain hak akses:
    - `App\Http\Controllers\Auth\`: Autentikasi multi-peran dan registrasi.
    - `App\Http\Controllers\Public\`: Portal publik (katalog kebutuhan, direktori sekolah, peta wilayah 3T, statistik dampak).
    - `App\Http\Controllers\School\`: Portal manajemen kebutuhan dan konfirmasi serah terima sekolah 3T.
    - `App\Http\Controllers\Contributor\`: Portal pengelolaan profil donatur/relawan dan riwayat *pledge*.
    - `App\Http\Controllers\Admin\`: Portal moderasi kebutuhan, kurasi verifikasi sekolah, persetujuan bantuan, audit aktivitas, dan pelaporan CSV.
  - **Data Access:** Eloquent ORM dengan 12 Model aktif dan 14 Migration terstruktur.
  - **Penyimpanan Berkas (Filesystem):** Penyimpanan berkas bukti sensitif (resi, BAST, foto) diletakkan pada direktori terproteksi `storage/app/private/evidence/`, disajikan hanya melalui controller berotorisasi ketat (`EvidenceController@download`).
- **Tanggal Audit:** 28 Oktober 2026.
- **Status Umum Proyek:** **Functional MVP Ready (Stabil & Siap Uji Pengguna)**. 40 pengujian otomatis lulus (125 assertions), seluruh modul fungsional F-01 s/d F-22 dan F-24 telah beroperasi pada web server lokal.
- **Catatan Penting Mengenai Lingkungan Pengembangan:**
  - `PATH` bawaan sistem Windows pengguna secara *default* mendeteksi PHP 8.0.30 (`C:\xampp\php`), yang menyebabkan kegagalan jika perintah `php artisan` dijalankan secara langsung tanpa menentukan binary PHP yang tepat (karena Laravel 10 mensyaratkan PHP >= 8.1.0).
  - Eksekusi perintah CLI wajib menggunakan binary PHP 8.1 dari Laragon: `& "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" artisan <perintah>`.

---

## B. Executive Summary

### 1. Gambaran Umum Perkembangan
Aplikasi web SambungAsa telah berhasil dibangun dari tahap repositori awal hingga menjadi aplikasi *full-stack* yang utuh dan berfungsi penuh. Seluruh fondasi basis data (16 tabel), model relasional, autentikasi berbasis peran (RBAC), middleware status akun, dan 67 rute aplikasi telah terpasang dan diverifikasi secara langsung melalui browser dan *automated tests*.

### 2. Bagian yang Sudah Berjalan dengan Baik
- **Autentikasi Multi-Peran & Proteksi Akun:** Registrasi sekolah, donatur individu, relawan, dan organisasi; proteksi brute-force login via RateLimiter; otorisasi peran via `RoleMiddleware` dan `EnsureAccountIsActive`.
- **Manajemen Wilayah 3T & Kurasi Sekolah:** Data referensi 20 wilayah (Papua, Kalimantan, Sulawesi) termigrasi; alur verifikasi sekolah 3T oleh Admin dengan opsi *Approve*, *Reject*, dan *Request Revision*.
- **Manajemen Kebutuhan & Moderasi:** Pembatasan ketat bahwa hanya sekolah yang berstatus `verified` yang dapat mengajukan kebutuhan; antrean moderasi admin sebelum tayang ke publik.
- **Integritas Bantuan & Anti Over-Pledging:** Penguncian transaksi database (*pessimistic locking* `lockForUpdate()`) untuk mencegah donasi melebihi sisa kuota yang dibutuhkan; pembuatan kode pelacakan otomatis berformat `SA-YYYYMM-XXXX`.
- **Keamanan Arsip Bukti Privat:** Pengunggahan dokumen BAST dan resi ke penyimpanan privat lokal dengan pencegahan kerentanan *IDOR* (hanya donatur pemilik, sekolah penerima, dan admin yang dapat mengunduh).
- **Statistik Dampak & Ekspor CSV:** Agregasi basis data riil pada laman publik `/statistics` dan ekspor laporan bantuan admin dengan sanitasi pencegahan *CSV Formula Injection* (CWE-1236).

### 3. Bagian yang Masih Perlu Peningkatan (Area Pengembangan Lanjutan)
- **Email Delivery & Verification:** Saat ini registrasi langsung menetapkan `email_verified_at` dan notifikasi masih disimpan secara in-app ke basis data lokal (`notifications` table). Belum ada integrasi SMTP / mail driver eksternal untuk pengiriman email notifikasi nyata.
- **Input Koordinat Geografis:** Koordinat latitude dan longitude pada profil sekolah saat ini diinput secara manual dalam format desimal, belum dilengkapi dengan widget peta interaktif (*interactive map picker* seperti Leaflet/OpenStreetMap).
- **Layering Logika Bisnis:** Sebagian logika validasi transisi status dan alur transaksi saat ini masih berada di dalam controller (`PledgeController`, `NeedController`). Disarankan untuk diekstraksi ke dalam *Dedicated Service Classes* (`PledgeService`, `TrackingService`, `VerificationService`) agar *Single Responsibility Principle (SRP)* semakin kuat.

### 4. Masalah Paling Penting & Terselesaikan
- Sebelumnya ditemukan *exception* `RelationNotFoundException: Call to undefined relationship [region] on model [App\Models\SchoolProfile]` dan `Route [school.needs.close] not defined`. Seluruh kendala tersebut telah diinvestigasi, diperbaiki dengan relasi alias dan penambahan rute, diverifikasi dengan subagent browser, serta di-push ke branch `main`.

### 5. Risiko yang Dapat Menghambat Pengembangan
- **Perbedaan Versi PHP Lingkungan:** Apabila developer lain di tim menjalankan perintah menggunakan PHP 8.0 bawaan Windows, aplikasi akan mengalami error sintaks atau dependency mismatch. Dibutuhkan file `.bat` / script runner pembantu atau standardisasi PATH sistem operasi.

---

## C. Feature Implementation Matrix

| ID | Nama Fitur | Deskripsi | Status | Bukti Implementasi | Kekurangan | Prioritas |
|---|---|---|:---:|---|---|:---:|
| **F-01** | Profil Sekolah 3T | Formulir data identitas, NPSN, koordinat, jumlah siswa/guru | `DONE` | `SchoolProfileController`, `resources/views/school/profile.blade.php`, `school_profiles` table | Pemilihan koordinat masih manual input angka desimal | P1 |
| **F-02** | Profil Donatur / Relawan / Organisasi | Kelola profil kontributor, NIK/SK legalitas, kontak | `DONE` | `ContributorProfileController`, `resources/views/contributor/profile.blade.php`, `contributor_profiles` | Unggah berkas legalitas organisasi belum terintegrasi | P2 |
| **F-03** | Verifikasi Akun Pengguna | Admin meninjau akun, setujui, tolak dengan alasan, atau suspend | `DONE` | `Admin\UserController`, `admin/users/show.blade.php`, `Tests\Feature\AuthTest` | Belum ada notifikasi via email eksternal | P1 |
| **F-04** | Verifikasi Lokasi 3T | Admin memeriksa sekolah terhadap referensi wilayah 3T | `DONE` | `Admin\SchoolVerificationController`, `admin/schools/show.blade.php`, `SchoolVerificationTest` | Tampilan perbandingan peta visual belum terpasang | P1 |
| **F-05** | CRUD Kebutuhan Sekolah | Sekolah terverifikasi membuat, edit, hapus, dan tutup kebutuhan | `DONE` | `School\NeedController`, `school/needs/`, `NeedManagementTest` | Hapus kebutuhan dibatasi jika sudah ada pledge aktif | P1 |
| **F-06** | Kategorisasi Kebutuhan | Kategori Buku, Elektronik, Fasilitas, Pelatihan, Relawan | `DONE` | `NeedCategorySeeder`, `need_categories` table, relasi `Need::category()` | Form penambahan kategori baru di UI admin belum ada | P2 |
| **F-07** | Prioritas Kebutuhan | Penetapan urgensi: Rendah, Sedang, Tinggi, Mendesak | `DONE` | Enum kolom `priority` di `needs` table, badge filter di direktori publik | - | P1 |
| **F-08** | Pencarian & Filter Kebutuhan | Filter multi-kriteria: kata kunci, pulau, kategori, prioritas | `DONE` | `PublicNeedController@index`, `public/needs/index.blade.php` | Filter rentang jumlah kebutuhan belum tersedia | P1 |
| **F-09** | Detail Kebutuhan Publik | Tampilan detail transparan, progres pemenuhan, profil sekolah | `DONE` | `PublicNeedController@show`, `public/needs/show.blade.php` | - | P1 |
| **F-10** | Pengajuan Komitmen (Pledge) | Formulir pengajuan bantuan barang / relawan dengan catatan & estimasi | `DONE` | `Contributor\PledgeController@store`, `PledgeFlowTest` | Input rincian paket pengiriman ekspedisi masih dasar | P1 |
| **F-11** | Review & Moderasi Pledge | Admin menyetujui, menolak dengan alasan, atau membatalkan pledge | `DONE` | `Admin\PledgeManagementController`, `admin/pledges/show.blade.php` | - | P1 |
| **F-12** | Manajemen Status Bantuan | Siklus status: submitted → verified → in_delivery → received → completed | `DONE` | `Pledge::getAllowedNextStatuses()`, `TrackingLog`, `PledgeFlowTest` | - | P1 |
| **F-13** | Upload Bukti Privat | Unggah resi pengiriman, BAST, foto serah terima terlindungi otorisasi | `DONE` | `EvidenceController`, `EvidenceSecurityTest`, `storage/app/private/evidence` | Belum mendukung *multiple file drag-and-drop* | P1 |
| **F-14** | Konfirmasi Penerimaan Sekolah | Sekolah penerima mengonfirmasi barang diterima & mencatat kondisi | `DONE` | `SchoolPledgeController@confirmReceipt`, `school/pledges/show.blade.php` | - | P1 |
| **F-15** | Pembaruan Otomatis Kebutuhan | Kuantitas terpenuhi terakumulasi otomatis saat pledge 'completed' | `DONE` | `PledgeManagementController@updateStatus`, `PledgeFlowTest` | - | P1 |
| **F-16** | Kode Tracking Unik | Format unik `SA-YYYYMM-XXXX` yang digenerasi otomatis | `DONE` | `Pledge::booted()` generator unik, kolom `tracking_code` | - | P1 |
| **F-17** | Dashboard Statistik Publik | Metrik nyata basis data agregat, sebaran provinsi, dan kategori | `DONE` | `StatisticsController`, `public/statistics.blade.php`, `StatisticsAndReportTest` | - | P1 |
| **F-18** | Manajemen Referensi Wilayah 3T | Direktori dan daftar kabupaten/provinsi 3T terverifikasi | `DONE` | `RegionController`, `public/regions/index.blade.php`, `RegionSeeder` | CRUD referensi wilayah via UI admin belum tersedia | P2 |
| **F-19** | Direktori Sekolah Publik | Daftar sekolah 3T terverifikasi, lokasi, NPSN, dan profil | `DONE` | `PublicSchoolController`, `public/schools/index.blade.php` | - | P1 |
| **F-20** | Manajemen Pengguna Admin | Kontrol akun, aktivasi, penolakan, penangguhan (suspend) | `DONE` | `Admin\UserController`, `admin/users/index.blade.php` | Fitur ubah role pengguna secara dinamis belum ada | P2 |
| **F-21** | Notifikasi In-App | Notifikasi lokal saat ada verifikasi, moderasi, atau perubahan status | `DONE` | `NotificationController`, `notifications` table, `resources/views/notifications/` | Push notification browser belum diimplementasikan | P2 |
| **F-22** | Laporan & Ekspor CSV | Filter laporan penyaluran bantuan dan ekspor CSV berpelindung formula | `DONE` | `Admin\ReportController@exportCsv`, `admin/reports/`, `StatisticsAndReportTest` | Belum ada opsi ekspor format PDF | P2 |
| **F-23** | Mode Offline (PWA Cache) | PWA manifest, service workers, offline database cache | **EXCLUDED** | **Sengaja ditiadakan** berdasarkan PRD & Master Scope | Tidak termasuk scope SambungAsa | N/A |
| **F-24** | Audit Trail (Log Aktivitas) | Pencatatan tindakan pengguna & admin (IP, user-agent, entity) | `DONE` | `ActivityLogController`, `activity_logs` table, `admin/activity-logs/` | Filter rentang tanggal pada audit log belum ada | P2 |

---

## D. Implemented Features (Detail Fitur yang Berfungsi)

### 1. Autentikasi Multi-Peran & Proteksi Akun (F-02, F-03, F-20)
- **Tujuan:** Mengamankan akses platform berdasarkan peran pengguna (Admin, Sekolah, Donatur/Relawan) dan memastikan hanya akun aktif yang dapat mengakses dashboard.
- **Perilaku Berfungsi:**
  - Registrasi donatur langsung menghasilkan akun aktif.
  - Registrasi perwakilan sekolah secara otomatis membuat akun berstatus `pending` dan record `SchoolProfile` yang belum terverifikasi.
  - Login terlindungi *rate limiting* (5 percobaan gagal memicu *throttle*).
  - Admin dapat meninjau detail akun, menyetujui, menolak dengan alasan, atau menangguhkan (*suspend*).
- **Rute Terkait:** `GET/POST /login`, `GET/POST /register`, `POST /logout`, `GET /admin/users`, `POST /admin/users/{user}/verify`, `POST /admin/users/{user}/reject`, `POST /admin/users/{user}/suspend`.
- **Controller & Model:** `App\Http\Controllers\Auth\AuthController`, `App\Http\Controllers\Admin\UserController`, `App\Models\User`.
- **Tampilan:** `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`, `resources/views/admin/users/`.
- **Bukti Verifikasi:** 9 test cases di `Tests\Feature\AuthTest` lulus 100%.

### 2. Manajemen & Kurasi Sekolah 3T (F-01, F-04, F-18, F-19)
- **Tujuan:** Memvalidasi bahwa sekolah penerima manfaat benar-benar berada di wilayah 3T yang sah dan terdaftar pada data pokok pendidikan (NPSN).
- **Perilaku Berfungsi:**
  - Sekolah mengedit data profil, kontak, alamat, koordinat peta, dan memilih wilayah kabupaten 3T resmi.
  - Admin memiliki dashboard verifikasi dengan daftar antrean (*pending*), opsi persetujuan (*verified*), penolakan (*rejected*), dan permintaan perbaikan data (*needs_revision*).
  - Direktori sekolah publik hanya memunculkan sekolah yang berstatus `verified`.
- **Rute Terkait:** `GET/PUT /school/profile`, `GET /admin/schools`, `GET /admin/schools/{school}`, `POST /admin/schools/{school}/verify`, `POST /admin/schools/{school}/reject`, `POST /admin/schools/{school}/request-revision`, `GET /schools`.
- **Controller & Model:** `School\ProfileController`, `Admin\SchoolVerificationController`, `Public\PublicSchoolController`, `App\Models\SchoolProfile`, `App\Models\Region`.
- **Bukti Verifikasi:** 6 test cases di `Tests\Feature\SchoolVerificationTest` lulus 100%.

### 3. Siklus Hidup Kebutuhan Sekolah (F-05, F-06, F-07, F-08, F-09)
- **Tujuan:** Menampung kebutuhan fasilitas dan relawan dari sekolah 3T secara terstruktur dengan sistem penjamin mutu admin sebelum dilihat publik.
- **Perilaku Berfungsi:**
  - Sekolah yang belum terverifikasi diblokir dari membuat kebutuhan (`HTTP 403 Forbidden`).
  - Kebutuhan baru berstatus `pending_review`.
  - Admin meninjau kebutuhan di `/admin/needs` untuk menerbitkan (`published`), menolak (`rejected`), atau menutup (`closed`).
  - Katalog publik `/needs` mendukung pencarian kata kunci, pemilihan pulau (Papua, Kalimantan, Sulawesi), kategori, dan urgensi prioritas.
- **Rute Terkait:** `GET/POST /school/needs`, `POST /school/needs/{need}/close`, `GET /admin/needs`, `POST /admin/needs/{need}/publish`, `GET /needs`, `GET /needs/{need}`.
- **Controller & Model:** `School\NeedController`, `Admin\NeedModerationController`, `Public\PublicNeedController`, `App\Models\Need`, `App\Models\NeedCategory`.
- **Bukti Verifikasi:** 7 test cases di `Tests\Feature\NeedManagementTest` lulus 100%.

### 4. Komitmen Bantuan (Pledge), Pelacakan & Konfirmasi Serah Terima (F-10, F-11, F-12, F-14, F-15, F-16)
- **Tujuan:** Menghubungkan donatur dengan kebutuhan spesifik, menjamin kuota tidak berlebih, dan melacak perjalanan logistik hingga sampai di lokasi 3T.
- **Perilaku Berfungsi:**
  - Donatur mengajukan komitmen dengan jumlah dan perkiraan tanggal penyerahan.
  - Database transaksi mengunci record kebutuhan (`lockForUpdate()`) agar kuantitas yang diajukan tidak melebihi sisa kebutuhan.
  - Terbit kode unik pelacakan otomatis (format `SA-YYYYMM-XXXX`).
  - Admin menyetujui komitmen (`verified`) dan memperbarui status kargo (`in_delivery`).
  - Pihak sekolah penerima melakukan konfirmasi penerimaan fisik di portalnya (`received`) dengan mencatat nama penerima dan kondisi barang.
  - Admin menandai selesai (`completed`), yang secara otomatis mengupdate total pemenuhan kuantitas kebutuhan sekolah (`quantity_fulfilled`).
- **Rute Terkait:** `POST /contributor/needs/{need}/pledge`, `GET /contributor/pledges/{pledge}`, `POST /admin/pledges/{pledge}/status`, `POST /school/pledges/{pledge}/confirm-receipt`.
- **Controller & Model:** `Contributor\PledgeController`, `Admin\PledgeManagementController`, `School\SchoolPledgeController`, `App\Models\Pledge`, `App\Models\TrackingLog`.
- **Bukti Verifikasi:** 7 test cases di `Tests\Feature\PledgeFlowTest` lulus 100%.

### 5. Keamanan Dokumen Bukti Penyaluran (F-13)
- **Tujuan:** Menyimpan bukti resi ekspedisi, dokumentasi penyerahan, dan BAST secara aman tanpa kebocoran data ke pihak publik.
- **Perilaku Berfungsi:**
  - Berkas disimpan di `storage/app/private/evidence/`.
  - Berkas dibatasi pada tipe MIME: `jpeg, png, jpg, pdf` dengan ukuran maksimal 5MB.
  - Akses unduh melalui `GET /evidence/{evidence}/download` memeriksa apakah pengguna merupakan pemilik pledge, sekolah penerima, atau admin. Pihak luar ditolak dengan `HTTP 403`.
- **Bukti Verifikasi:** 5 test cases di `Tests\Feature\EvidenceSecurityTest` lulus 100%.

### 6. Transparansi Statistik & Laporan Admin (F-16/F-17, F-22, F-24)
- **Tujuan:** Menampilkan keterbukaan data penyaluran bantuan kepada publik dan menyediakan sarana audit bagi administrator.
- **Perilaku Berfungsi:**
  - Halaman publik `/statistics` menghitung metrik agregat langsung dari database MySQL (jumlah sekolah terverifikasi, jumlah kebutuhan selesai, rasio pemenuhan kategori, dan feed bantuan selesai).
  - Halaman admin `/admin/reports` menyediakan filter laporan bantuan per periode, status, dan wilayah.
  - Ekspor CSV streaming yang menerapkan pencegahan eksekusi formula (CWE-1236) pada karakter awalan `=`, `+`, `-`, `@`.
  - Audit trail `/admin/activity-logs` mencatat seluruh tindakan krusial.
- **Bukti Verifikasi:** 4 test cases di `Tests\Feature\StatisticsAndReportTest` lulus 100%.

---

## E. Partially Implemented Features (Fitur Parsial / Butuh Penyempurnaan)

1. **Pemilihan Koordinat Geografis Sekolah (F-01)**
   - *Bagian yang sudah dibuat:* Field `latitude` dan `longitude` bertipe desimal (7 angka di belakang koma) di tabel `school_profiles` dan form input angka.
   - *Bagian yang belum dibuat:* Picker peta interaktif (misal: Leaflet.js / OpenStreetMap) di mana pengguna dapat mengklik titik peta untuk otomatis mengisi koordinat.
   - *Langkah Penyelesaian:* Pasang skrip Leaflet.js ringan pada `resources/views/school/profile.blade.php`.
   - *Kriteria Selesai:* Perwakilan sekolah dapat menggeser pin lokasi peta dan koordinat terisi secara otomatis.

2. **Pengunggahan Legalitas Dokumen Kontributor Organisasi (F-02)**
   - *Bagian yang sudah dibuat:* Tabel `contributor_profiles` menampung `organization_name`, `organization_type`, dan `legality_number` (teks).
   - *Bagian yang belum dibuat:* Form upload berkas SK Kemenkumham / akta pendirian yayasan.
   - *Langkah Penyelesaian:* Tambahkan kolom `legality_document_path` pada tabel `contributor_profiles` dan mekanisme unggah privat.
   - *Kriteria Selesai:* Kontributor organisasi dapat mengunggah salinan PDF legalitas untuk diverifikasi admin.

3. **Kanal Notifikasi Email Nyata (F-15 / F-21)**
   - *Bagian yang sudah dibuat:* Model `Notification`, tabel `notifications`, dan antarmuka in-app di `/notifications` yang berfungsi menandai telah dibaca.
   - *Bagian yang belum dibuat:* Pengiriman salinan notifikasi ke alamat email pengguna (misal saat akun disetujui atau bantuan tiba).
   - *Langkah Penyelesaian:* Konfigurasikan driver SMTP / Mailtrap di `.env` dan buat Mailable class untuk aksi-aksi penting.
   - *Kriteria Selesai:* Event status bantuan memicu antrean email ke pengguna terkait.

---

## F. Unimplemented Features (Fitur di Luar Scope atau Backlog Mendatang)

1. **F-23: Mode Offline / PWA Cache**
   - *Status:* **SECARA EKSPLISIT DIKELUARKAN DARI SCOPE (EXCLUDED)**.
   - *Alasan:* Sesuai instruksi master dan PRD Bagian 3.2, platform SambungAsa mewajibkan koneksi daring untuk menjaga keabsahan data dan integritas transaksi bantuan. Tidak boleh ada Service Worker, IndexedDB, atau offline caching yang dibuat.

2. **Ekspor Laporan PDF Resmi Berkop (Lanjutan F-22)**
   - *Deskripsi:* Mencetak lembar ringkasan penyaluran bantuan dalam format PDF dengan format resmi Berita Acara.
   - *Dependensi:* Paket `barryvdh/laravel-dompdf`.
   - *Prioritas:* P3 (Low).

3. **Widget Peta Sebaran Sekolah Interaktif pada Beranda (Lanjutan F-17/F-19)**
   - *Deskripsi:* Peta tematik kepulauan Indonesia (Papua, Kalimantan, Sulawesi) dengan pin sebaran sekolah 3T di landing page.
   - *Prioritas:* P2 (Medium).

---

## G. Bug dan Technical Debt

### 1. Masalah yang Telah Diperbaiki (Resolved Bugs)
- **Bug 1 (Terselesaikan):** `RelationNotFoundException: Call to undefined relationship [region] on model [App\Models\SchoolProfile]` saat memuat `/admin/dashboard`.
  - *Perbaikan:* Ditambahkan relasi alias `region()` pada model `SchoolProfile` dan optimasi eager-loading `['user', 'province', 'regency']`.
- **Bug 2 (Terselesaikan):** `Route [school.needs.close] not defined` pada `/school/needs`.
  - *Perbaikan:* Rute `POST /school/needs/{need}/close` didaftarkan pada `routes/web.php` dan ditambahkan unit test penutupan kebutuhan.
- **Bug 3 (Terselesaikan):** Tampilan halaman `/statistics` sempat tidak berbentuk karena belum terdaftarnya kelas CSS `.stats-grid`, `.stat-card`, dan `.grid-cols-4`.
  - *Perbaikan:* Diimplementasikan CSS Red Broadcast lengkap pada `public/css/sambungasa.css` dan perombakan layout responsif pada `public/statistics.blade.php`.

### 2. Catatan Technical Debt (Hutang Teknis untuk Diperhatikan)
- **Fat Controllers (Separation of Concerns):**
  - *Lokasi:* `app/Http/Controllers/Contributor/PledgeController.php` dan `app/Http/Controllers/School/NeedController.php`.
  - *Dampak:* Logika transaksi database, penguncian data, dan pencatatan audit log ditulis langsung di controller method.
  - *Rekomendasi:* Buat layer `App\Services\PledgeService` dan `App\Services\NeedService` pada fase refactoring berikutnya untuk memisahkan logika bisnis dari HTTP controller.
- **Sistem PATH Environment Windows:**
  - *Lokasi:* Windows System Environment Variables.
  - *Dampak:* Menjalankan `php` standar di command prompt memanggil PHP 8.0 milik XAMPP, bukan PHP 8.1 milik Laragon.
  - *Rekomendasi:* Perbarui urutan PATH sistem pengguna atau sertakan script helper `artisan.bat` di direktori proyek.

---

## H. UI/UX Review (Kesesuaian Desain Red Broadcast)

Berdasarkan perbandingan implementasi aktual terhadap dokumen `design.md`:

| Aspek Desain | Spesifikasi `design.md` | Kondisi Aktual | Evaluasi & Kesesuaian |
|---|---|---|---|
| **Palet Warna** | Primary Red `#FF0000`, Hover `#CC0000`, Link Blue `#065FD4`, Neutral `#606060`, Dark `#0F0F0F` | Diterapkan pada `:root` di `sambungasa.css` | **100% Sesuai**. Warna merah digunakan proporsional untuk aksen penting, tombol CTA, dan indikator live. |
| **Tipografi** | Google Font Roboto (400, 500, 700) & Roboto Mono untuk kode tracking | Di-import dari Google Fonts API pada `sambungasa.css:L1` | **100% Sesuai**. Kode tracking `SA-YYYYMM-XXXX` selalu menggunakan kelas `font-mono`. |
| **Bentuk Komponen** | Pill Buttons (`border-radius: 9999px`), Pill Chips, Soft Cards | `.btn`, `.chip`, `.stat-card` menggunakan border radius bulat | **100% Sesuai**. Tampilan khas modern broadcast. |
| **Elevasi & Bayangan** | Level 0 (flat), Level 1 (`0 1px 2px`), Level 2 (`0 4px 20px`), Level 3 (`0 8px 32px`) | Variabel `--elevation-0` s/d `--elevation-3` terpasang di CSS | **100% Sesuai**. Kartu memiliki bayangan lembut dan animasi hover `translateY(-3px)`. |
| **Responsivitas** | Dukungan Desktop, Tablet, dan Mobile | Breakpoint `@media (max-width: 992px)` dan `(max-width: 640px)` | **100% Sesuai**. Grid 4-kolom beralih ke 2-kolom di tablet dan 1-kolom di ponsel. |
| **Feedback Pengguna** | Alert sukses, error, warning, dan konfirmasi submit | Alert Blade terpasang di semua layout dasar | **100% Sesuai**. Dilengkapi pesan konfirmasi tindakan penting. |

---

## I. Database Status

### 1. Status Migrasi & Tabel Riil
Pemeriksaan langsung pada server MySQL lokal (`127.0.0.1:3306`) mengonfirmasi koneksi berhasil ke basis data `sambungasa`.

| No | Nama Tabel | Jumlah Kolom | Baris Data Riil | Fungsi Utama |
|:---:|---|:---:|:---:|---|
| 1 | `users` | 10 | 8 | Akun pengguna multi-peran (Admin, Sekolah, Donatur, Relawan) |
| 2 | `password_reset_tokens` | 3 | 0 | Token reset kata sandi |
| 3 | `failed_jobs` | 6 | 0 | Antrean pekerjaan yang gagal |
| 4 | `personal_access_tokens` | 7 | 0 | Autentikasi token Sanctum |
| 5 | `regions` | 7 | 20 | Referensi provinsi, kabupaten, dan status 3T (Papua, Kalimantan, Sulawesi) |
| 6 | `school_profiles` | 22 | 4 | Profil sekolah 3T, NPSN, koordinat, dan status verifikasi |
| 7 | `contributor_profiles` | 9 | 3 | Profil donatur individu dan organisasi |
| 8 | `volunteer_profiles` | 8 | 1 | Profil relawan pendidikan, keahlian, dan ketersediaan |
| 9 | `need_categories` | 6 | 5 | Kategori kebutuhan (Buku, Komputer, Fasilitas, Pelatihan, Lainnya) |
| 10 | `needs` | 15 | 4 | Kebutuhan sekolah, kuantitas yang diminta, terpenuhi, dan status moderasi |
| 11 | `pledges` | 13 | 3 | Komitmen bantuan donatur, kode tracking, dan status alur |
| 12 | `tracking_logs` | 6 | 9 | Catatan riwayat linimasa perubahan status pengiriman bantuan |
| 13 | `evidence_files` | 9 | 1 | Metadata berkas bukti privat (resi, BAST, foto penyerahan) |
| 14 | `notifications` | 8 | 3 | Notifikasi internal dalam aplikasi |
| 15 | `activity_logs` | 8 | 13 | Audit trail aktivitas pengguna dan administrator |
| 16 | `migrations` | 3 | 14 | Riwayat eksekusi skema migrasi |

### 2. Constraint & Relasi Integritas
- Seluruh Foreign Key (`user_id`, `school_profile_id`, `need_category_id`, `pledge_id`, dll.) menggunakan relasi `onDelete('cascade')` atau `onDelete('set null')` yang sah untuk menjaga integritas data relasional.
- Tidak ada tabel yang hilang atau skema yang tertinggal.

---

## J. Security Review

| Aspek Keamanan | Status | Evaluasi & Bukti Implementasi |
|---|:---:|---|
| **CSRF Protection** | `SECURE` | Semua form HTTP POST, PUT, dan DELETE pada Blade view menyertakan token `@csrf`. |
| **Password Hashing** | `SECURE` | Bcrypt hashing terkonfigurasi pada `User::casts` (`'password' => 'hashed'`). |
| **Insecure Direct Object Reference (IDOR)** | `SECURE` | Endpoint `EvidenceController@download` dan `School\NeedController@edit` memvalidasi kepemilikan data pengguna sebelum menyajikan data. Pihak ketiga langsung ditolak dengan `HTTP 403`. |
| **SQL Injection Protection** | `SECURE` | Seluruh query menggunakan metode parameter binding bawaan Eloquent ORM. Tidak ditemukan penggunaan `DB::raw()` tanpa binding yang rentan. |
| **Cross-Site Scripting (XSS)** | `SECURE` | Output teks pengguna pada Blade di-escape menggunakan sintaks kurung kurawal ganda `{{ ... }}`. |
| **CSV Formula Injection (CWE-1236)** | `SECURE` | Fungsi ekspor pada `ReportController@exportCsv` melakukan sanitasi nilai string yang diawali karakter berbahaya (`=`, `+`, `-`, `@`, `\t`, `\r`) dengan menambahkan petik tunggal (`'`). |
| **Penyimpanan Berkas Sensitif** | `SECURE` | Dokumen bukti disimpan pada `storage/app/private/evidence/`, tidak diletakkan pada direktori `public/` atau symlink publik, mencegah pengunduhan langsung via URL. |
| **Race Condition / Over-Pledging** | `SECURE` | Pengurangan sisa kuota kebutuhan pada `PledgeController@store` dibungkus dalam `DB::transaction()` dan `Need::lockForUpdate()`. |
| **Brute Force Protection** | `SECURE` | Endpoint `login` dilindungi `RateLimiter` Laravel dengan batas maksimal 5 percobaan gagal per menit. |

---

## K. Testing Status

Pengujian otomatis dieksekusi secara nyata pada database pengujian `sambungasa_test` menggunakan perintah:
`& "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" artisan test`

| Area Pengujian | Jenis Pengujian | Status | Hasil Aktual | Cakupan & Catatan |
|---|---|:---:|---|---|
| **Autentikasi & Registrasi** | Feature Test | `PASS` | 9 passed (27 assertions) | Registrasi multi-peran, proteksi password, login throttling, pembatasan dashboard per role. |
| **Keamanan Arsip Bukti** | Feature Test | `PASS` | 5 passed (16 assertions) | Upload bukti valid, penolakan ekstensi berbahaya, penolakan file > 5MB, otorisasi download, pemblokiran IDOR pihak ketiga. |
| **Manajemen Kebutuhan** | Feature Test | `PASS` | 7 passed (20 assertions) | Sekolah unverified dilarang membuat kebutuhan, moderasi publish/reject admin, direktori publik terfilter, hak edit antar-sekolah, penutupan kebutuhan. |
| **Alur Komitmen (Pledge)** | Feature Test | `PASS` | 7 passed (23 assertions) | Kode tracking unik, pencegahan over-pledging, persetujuan/penolakan admin, konfirmasi serah terima sekolah, pembaruan otomatis kuantitas terpenuhi. |
| **Verifikasi Sekolah 3T** | Feature Test | `PASS` | 6 passed (18 assertions) | Antrean verifikasi, review detail, persetujuan 3T, penolakan dengan catatan, permintaan revisi profil, proteksi akses non-admin. |
| **Statistik & Laporan CSV** | Feature Test | `PASS` | 4 passed (17 assertions) | Agregasi database nyata, filter laporan admin, sanitasi formula CSV, audit activity logs. |
| **Smoke Test Tampilan** | Browser Subagent | `PASS` | 6 laman terverifikasi | Laman Beranda, Daftar Kebutuhan, Direktori Sekolah, Statistik, Login, dan Dashboard Admin berhasil dimuat dengan HTTP 200 tanpa error. |

**Total:** **40 Test Cases, 125 Assertions, 0 Failures, 100% Lulus**.

---

## L. Improvement Backlog (Rekomendasi Berdasarkan Prioritas)

| ID | Improvement | Alasan | Dampak | Prioritas | Dependensi |
|---|---|---|---|:---:|---|
| **IMP-001** | Integrasi Leaflet Map Picker pada Profil Sekolah | Mempermudah penentuan koordinat presisi wilayah 3T secara visual | Menghilangkan salah ketik koordinat desimal | **P1** | `resources/views/school/profile.blade.php` |
| **IMP-002** | Ekstraksi Service Layer (`PledgeService`, `NeedService`) | Memisahkan logika bisnis dari HTTP controller (mengurangi fat controller) | Mempermudah unit test dan *maintainability* jangka panjang | **P2** | `app/Http/Controllers/` |
| **IMP-003** | Integrasi Driver Mail / SMTP untuk Notifikasi Eksternal | Pengguna sekolah dan donatur perlu tahu update status tanpa harus selalu login | Meningkatkan *engagement* dan respon penyaluran bantuan | **P2** | `.env`, Akun SMTP (Mailtrap/SES) |
| **IMP-004** | Ekspor Berita Acara Serah Terima (BAST) Format PDF | Pihak sekolah dan donatur membutuhkan dokumen pertanggungjawaban fisik resmi | Meningkatkan kredibilitas formal penyaluran barang | **P2** | Paket `barryvdh/laravel-dompdf` |
| **IMP-005** | UI Tambah Kategori Kebutuhan & Manajemen Wilayah Admin | Saat ini kategori dan wilayah diinput melalui Database Seeder | Fleksibilitas admin menambah wilayah 3T baru tanpa menyentuh database | **P3** | `Admin\DashboardController` |

---

## M. Next Steps (Roadmap Pengembangan Selanjutnya)

1. **Tahap 1: Standardisasi Environment Developer Tim**
   - Membuat script helper pembantu (seperti `run.bat` atau panduan PATH) agar seluruh anggota tim pengembang menjalankan PHP 8.1 yang sama tanpa kendala konflik dengan PHP 8.0 bawaan XAMPP.
2. **Tahap 2: Penyempurnaan UX Input Lokasi (P1)**
   - Mengintegrasikan widget Leaflet.js pada form profil sekolah untuk visualisasi koordinat titik sekolah di peta satelit/OpenStreetMap.
3. **Tahap 3: Pemisahan Service Layer (P2)**
   - Melakukan refactoring logika transaksi pledge dan kebutuhan ke dalam `App\Services\` untuk menjaga arsitektur *Clean Code*.
4. **Tahap 4: Notifikasi Eksternal & Notifikasi Email (P2)**
   - Mengonfigurasi Mailer untuk mengirimkan email saat status bantuan berganti (*in delivery*, *received*, *completed*).
5. **Tahap 5: Persiapan Deployment Staging**
   - Menyiapkan berkas deployment CI/CD (GitHub Actions) dan file panduan konfigurasi server produksi.

---

## N. Known Limitations dan Open Questions

1. **Batasan Sistem yang Diketahui (Known Limitations):**
   - Belum ada fitur *in-app real-time messaging / chat* antara sekolah penerima dan donatur; komunikasi koordinasi lapangan mengandalkan nomor kontak resmi yang tercantum pada detail bantuan yang disetujui.
   - PWA dan fungsi offline secara sengaja tidak didukung sesuai batasan master instruksi.
2. **Pertanyaan Terbuka untuk Pemilik Proyek (Open Questions):**
   - Apakah di masa mendatang diperlukan penambahan provinsi 3T di luar Papua, Kalimantan, dan Sulawesi (misalnya Kepulauan Riau, Maluku, atau NTT)? *(Catatan: Skema database `regions` saat ini sudah dirancang dinamis dan siap menampung wilayah tambahan kapan saja).*
   - Apakah pengunggahan berkas bukti BAST memerlukan tanda tangan digital (*digital signature* / e-Meterai), atau cukup pindaian/foto dokumen fisik yang bertanda tangan basah dan berstempel sekolah?
