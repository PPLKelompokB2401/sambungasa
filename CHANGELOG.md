# CHANGELOG — SAMBUNGASA

Semua perubahan, penambahan fitur, pembaruan skema, dan catatan arsitektur dicatat dalam dokumen ini.

---

## [1.0.0] - 2026-10-28

### Added (Fitur Baru)
- **Fondasi Aplikasi & Konfigurasi:**
  - Inisialisasi proyek Laravel 10 dengan PHP 8.1 runtime dan basis data MySQL `sambungasa`.
  - Inisialisasi repositori Git dan file konfigurasi lingkungan `.env` serta `.env.testing`.
  - Dokumentasi arsitektur lengkap: `docs/PROJECT_AUDIT.md`, `docs/DATABASE_SCHEMA.md`, `docs/IMPLEMENTATION_PLAN.md`, dan `docs/TESTING.md`.
- **Sistem Desain Red Broadcast:**
  - Implementasi CSS murni pada `public/css/sambungasa.css` mengikuti token warna (#FF0000, #CC0000, #065FD4, #606060), tipografi Roboto & Roboto Mono, pill button (`border-radius: 9999px`), chip pills, dan elevasi berlapis.
  - Layout dasar Blade: `layouts/app.blade.php` (portal publik), `layouts/guest.blade.php` (autentikasi), dan `layouts/dashboard.blade.php` (portal terproteksi).
  - Halaman error ramah pengguna: `errors/403.blade.php`, `errors/404.blade.php`, dan `errors/500.blade.php`.
- **Modul Autentikasi & RBAC (F-02, F-03, F-19, F-20):**
  - Registrasi multi-peran untuk Sekolah 3T, Donatur, Relawan, dan Organisasi.
  - Validasi ketat, hashing kata sandi aman (Bcrypt), dan perlindungan brute-force via RateLimiter.
  - Middleware otorisasi peran `RoleMiddleware` dan pengecekan status akun aktif `EnsureAccountIsActive`.
  - Manajemen Pengguna oleh Admin (`/admin/users`) untuk verifikasi, penolakan, penangguhan (suspend), dan aktivasi akun.
- **Modul Sekolah 3T & Verifikasi Lokasi (F-01, F-04, F-17/F-18):**
  - Model dan migrasi data referensi wilayah 3T (`regions`) mencakup provinsi dan kabupaten sasaran (Papua, Kalimantan, Sulawesi).
  - Formulir kelola profil sekolah (`/school/profile`) dengan penautan wilayah 3T resmi dan NPSN.
  - Antrean verifikasi sekolah 3T bagi Admin (`/admin/schools`) dengan alur Setujui (`verified`), Tolak (`rejected`), dan Minta Revisi (`needs_revision`).
  - Direktori referensi wilayah 3T publik (`/regions`).
- **Modul Manajemen Kebutuhan Sekolah & Moderasi (F-05, F-06, F-07, F-08, F-20):**
  - Pembatasan ketat: hanya sekolah yang telah terverifikasi 3T yang dapat mengajukan kebutuhan.
  - CRUD kebutuhan sekolah dengan kategori material (buku, komputer, seragam, infrastruktur) dan non-material (pelatihan, relawan pengajar).
  - Antrean moderasi admin (`/admin/needs`) untuk menerbitkan (`published`), menolak dengan catatan (`rejected`), atau menutup kebutuhan (`closed`).
  - Direktori kebutuhan publik (`/needs`) dengan filter kategori, prioritas, pencarian teks, dan proteksi kebutuhan belum terbit.
- **Modul Komitmen Bantuan (Pledge) & Pelacakan (F-10, F-11, F-12, F-14, F-15):**
  - Pengajuan komitmen bantuan oleh donatur/mitra dengan catatan dan estimasi waktu penyaluran.
  - Generator otomatis **Kode Tracking Unik** format `SA-YYYYMM-XXXX`.
  - Pencegahan *over-pledging* dengan transaksi basis data dan *pessimistic locking* (`lockForUpdate`).
  - Review komitmen bantuan oleh admin (`/admin/pledges`).
  - Konfirmasi penerimaan bantuan fisik/relawan oleh pihak sekolah dengan pencatatan nama penanggung jawab dan kondisi barang (F-14).
  - Pembaruan otomatis kuantitas terpenuhi (`quantity_fulfilled`) dan status pemenuhan kebutuhan (`fulfilled`) saat bantuan diselesaikan (F-15).
- **Modul Bukti Penyaluran & Keamanan Arsip Privat (F-13):**
  - Pengunggahan berkas bukti (resi kargo, foto serah terima, BAST, laporan kegiatan).
  - Validasi tipe berkas (JPEG, PNG, PDF) dan batas ukuran maksimum (5MB).
  - Penyimpanan privat pada `storage/app/private/evidence/` untuk mencegah kebocoran dokumen sensitif ke publik.
  - Otorisasi pengunduhan berkas melalui endpoint `EvidenceController@download` khusus pemilik pledge, sekolah penerima, dan admin.
- **Modul Notifikasi Internal & Audit Trail (F-21, F-24):**
  - Notifikasi in-app untuk pembaruan status verifikasi, moderasi kebutuhan, dan perkembangan bantuan (`/notifications`).
  - Fitur tandai telah dibaca per notifikasi dan tandai semua telah dibaca.
  - Pencatatan seluruh aksi penting ke tabel `activity_logs` (pengguna, tindakan, entitas subjek, IP address, user-agent).
  - Portal audit trail untuk administrator (`/admin/activity-logs`) dengan filter multi-kriteria.
- **Modul Statistik & Ekspor Laporan CSV (F-16/F-17, F-22):**
  - Laman transparansi publik (`/statistics`) yang menghitung metrik agregat nyata dari basis data (sekolah terverifikasi, kebutuhan terpenuhi, distribusi per wilayah dan kategori).
  - Dashboard eksekutif administrator (`/admin/dashboard`) dengan rekap metrik dan antrean verifikasi/moderasi.
  - Laporan penyaluran bantuan (`/admin/reports`) dengan ekspor berkas CSV.
  - Sanitasi pencegahan **CSV Formula Injection (CWE-1236)** untuk karakter pembuka berbahaya (`=`, `+`, `-`, `@`).

### Security & Integrity (Keamanan & Integritas Data)
- Penerapan proteksi CSRF pada semua form POST/PUT/DELETE.
- Hashing kata sandi menggunakan Bcrypt cost 10 (cost 4 pada lingkungan pengujian).
- Mencegah *Insecure Direct Object Reference (IDOR)* melalui verifikasi kepemilikan data profil sekolah, komitmen bantuan, dan berkas arsip.
- Pencegahan serangan Race-Condition pada pengurangan kuota bantuan melalui database lock.

### Excluded by Scope (Batasan Ruang Lingkup)
- **F-23 PWA / Offline Functionality:** Secara eksplisit **DITIADAKAN** sesuai instruksi master. Tidak ada service worker, manifest PWA, IndexedDB, atau offline caching.
- SambungAsa tidak memproses transaksi uang atau *payment gateway*; sistem memfasilitasi komitmen barang fisik dan relawan pendidikan.

---

## [1.0.1] - 2026-10-28

### Fixed (Perbaikan Bug)
- **Model Relationship & View Accessor Resolusi:**
  - Menambahkan relasi alias `region()` pada `SchoolProfile` yang mengarah ke `regency_id` (`Region::class`) untuk mencegah `RelationNotFoundException`.
  - Mengoptimalkan eager-loading `['user', 'province', 'regency']` pada `Admin\DashboardController`.
  - Menambahkan method pembantu publik `getStatusLabel()` pada `Pledge` yang membungkus aksesor `getStatusLabelAttribute()` untuk kompatibilitas pemanggilan method di template Blade.
  - Memperbarui `admin/dashboard.blade.php` dan `public/statistics.blade.php` agar dapat membaca relasi `regency`, `province`, dan `contributorProfile->user->name` secara aman dengan fallback yang kokoh.
  - Menambahkan registrasi rute `POST /school/needs/{need}/close` (`school.needs.close`) pada [routes/web.php](file:///C:/Users/Dale%20Carnegie/Downloads/SambungAsa/routes/web.php#L95) untuk aksi penutupan kebutuhan sekolah.
