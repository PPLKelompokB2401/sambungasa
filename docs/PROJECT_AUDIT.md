# SambungAsa — Project Audit & Requirements Traceability

> **Dokumen Audit Proyek & Analisis Kebutuhan**  
> Tanggal: 28 Oktober 2026  
> Versi: 1.0  
> Penanggung Jawab: Senior Full-Stack Laravel Developer / Software Architect

---

## 1. Kondisi Eksisting Proyek

| Aspek | Kondisi Saat Ini | Keterangan |
|---|---|---|
| Direktori Proyek | `c:\Users\Dale Carnegie\Downloads\SambungAsa` | Fresh workspace |
| Status Framework | Belum terinisialisasi | Belum ada file Laravel (`composer.json`, `artisan`, `app/`, dll.) |
| Version Control | Git belum diinisialisasi | `fatal: not a git repository` |
| Dokumentasi Tersedia | Lengkap | `ARCHITECTURE_SAMBUNGASA.md`, `PRD.md`, `red-broadcast-DESIGN.md`, `design.md` |
| Database Eksisting | MySQL aktif (port 3306) | Database `sambungasa` sudah tersedia di MySQL lokal (0 tabel) |

---

## 2. Versi Teknologi dan Lingkungan Sistem

| Komponen | Versi Terdeteksi | Rekomendasi & Konfigurasi |
|---|---|---|
| **PHP Runtime** | PHP 8.1.10 (`C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe`) & PHP 8.0.30 (`C:\xampp\php\php.exe`) | Gunakan **PHP 8.1.10** untuk menjalankan **Laravel 10.x** (LTS/stable). Prepend path PHP 8.1 ke environment. |
| **PHP Extensions** | `bcmath`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`, `mysqli`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip` | Semua ekstensi yang disyaratkan Laravel 10 aktif dan siap. |
| **Composer** | Composer 2.8.12 | Siap digunakan bersama PHP 8.1. |
| **Node.js & NPM** | Node.js v24.11.0, NPM 11.6.1 | Siap untuk kompilasi frontend assets jika diperlukan. |
| **Database Server** | MySQL 8.0.30 (running via `mysqld.exe` pada `127.0.0.1:3306`) | User: `root`, Password: ``, Database: `sambungasa` |
| **Git** | Git 2.51.0.windows.2 | Siap diinisialisasi untuk pelacakan versi. |

---

## 3. Fitur Eksisting vs Fitur yang Belum Ada

### 3.1 Fitur Eksisting
Saat ini proyek berada di tahap **Phase 1 (Setup & Audit)**. Belum ada kode aplikasi yang diimplementasikan.

### 3.2 Fitur yang Belum Ada (To Be Implemented)
Semua modul fungsional F-01 sampai F-22 dan F-24, mencakup:
1. Autentikasi multi-peran & verifikasi akun (Admin, Sekolah, Donatur/Relawan/Organisasi).
2. Profil sekolah & verifikasi lokasi 3T (wilayah Papua, Kalimantan, Sulawesi).
3. Manajemen kebutuhan sekolah (material & nonmaterial), kategori, prioritas, dan moderasi admin.
4. Direktori publik sekolah dan kebutuhan, pencarian kata kunci, serta filter multi-dimensi.
5. Pengajuan komitmen bantuan (*pledge*), validasi sisa kebutuhan, dan kode tracking unik.
6. Verifikasi pledge, manajemen transisi status berurutan, dan penanganan penolakan.
7. Upload bukti penyaluran ke private storage dengan pengamanan akses.
8. Riwayat tracking dan notifikasi internal dalam aplikasi.
9. Dashboard statistik publik & dashboard analitik admin berbasis database aktual.
10. Ekspor laporan CSV dengan proteksi formula injection.
11. Audit log aktivitas admin dan perubahan entitas penting (F-24).

---

## 4. Masalah Konfigurasi & Inkonsistensi Dokumentasi

### 4.1 Masalah Konfigurasi
1. **PHP CLI Default**: `PATH` lingkungan Windows memprioritaskan PHP 8.0.30 (`C:\xampp\php`). Karena Laravel 10 memerlukan PHP >= 8.1.0, eksekusi artisan dan composer harus diarahkan menggunakan PHP 8.1.10 yang berlokasi di `C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe`.
2. **Git Repository**: Direktori belum memiliki `.git`. Perlu diinisialisasi dengan konfigurasi `.gitignore` standar Laravel agar file cache, log, dan `.env` tidak ter-commit.

### 4.2 Inkonsistensi Dokumentasi & Resolusi
1. **Nama Dokumen Desain**:
   - *Instruksi Master*: Meminta membaca `design.md`.
   - *Fakta Workspace*: File bernama `red-broadcast-DESIGN.md`.
   - *Resolusi*: Telah dibuat salinan `design.md` yang identik dengan `red-broadcast-DESIGN.md`. Kedua file dipertahankan agar kompatibel dengan seluruh referensi.
2. **Kontekstualisasi Desain Red Broadcast**:
   - *Dokumen*: Menggunakan tema "Red Broadcast" dengan referensi elemen video streaming.
   - *Resolusi*: Mengadopsi seluruh token visual resmi `red-broadcast-DESIGN.md` (Primary Red `#FF0000`, Primary Hover `#CC0000`, Link Blue `#065FD4`, Neutral `#606060`, Background `#FFFFFF`, Surface `#F2F2F2`, Roboto font, pill buttons `rounded-full`, chip filter bar, elevation levels 0–3) dan menerapkannya secara elegan pada domain platform bantuan pendidikan 3T SambungAsa.
3. **Pengecualian Fitur F-23 (PWA / Mode Offline)**:
   - *Proposal Awal*: Sempat memuat F-23 Mode Offline (PWA Cache).
   - *Resolusi*: Sesuai instruksi master, `ARCHITECTURE_SAMBUNGASA.md`, dan `PRD.md`, F-23 **dikeluarkan secara permanen dari scope**. Tidak ada Service Worker, PWA manifest, IndexedDB, atau offline cache yang dibuat.

---

## 5. Potensi Risiko Implementasi & Strategi Mitigasi

| Risiko | Dampak | Strategi Mitigasi |
|---|---|---|
| *Race Condition* / *Over-pledging* pada kebutuhan | Kuantitas bantuan melebihi kebutuhan yang diminta | Gunakan `DB::transaction()` dan locking (`lockForUpdate()`) saat memeriksa sisa kebutuhan dan membuat pledge. |
| Akses ilegal ke dokumen bukti (*IDOR*) | Data pribadi/bukti sensitif terekspos ke publik | Simpan bukti di `storage/app/private/evidence/` dan layani hanya lewat controller dengan otorisasi Policy. |
| Kebocoran data pribadi pada statistik & direktori publik | Pelanggaran privasi sekolah/donatur | Hanya agregat anonim dan data yang disetujui publik yang dimunculkan pada endpoint publik. |
| *CSV Formula Injection* pada ekspor laporan | Potensi eksekusi script pada aplikasi spreadsheet pengguna | Lakukan sanitasi nilai teks pada generator CSV jika diawali karakter `=`, `+`, `-`, `@`. |
| Inkonsistensi status transisi pledge | Status melompat tidak sesuai workflow bisnis | Pusat validasi transisi status di `TrackingService` dengan state machine yang ketat. |

---

## 6. Matriks Traceability Kebutuhan Fungsional (F-01 s/d F-24)

| ID | Fitur | Aktor | Status | Rencana Fase |
|---|---|---|---|---|
| **F-01** | Manajemen Profil Sekolah | Sekolah | `Not Started` | Phase 5 |
| **F-02** | Profil Donatur / Relawan / Organisasi | Donatur/Relawan/Organisasi | `Not Started` | Phase 4 |
| **F-03** | Verifikasi Data Diri | Admin | `Not Started` | Phase 4 |
| **F-04** | Verifikasi Lokasi 3T | Admin | `Not Started` | Phase 5 |
| **F-05** | CRUD Kebutuhan Sekolah | Sekolah | `Not Started` | Phase 6 |
| **F-06** | Kategorisasi Kebutuhan | Sekolah, Admin | `Not Started` | Phase 6 |
| **F-07** | Prioritas Kebutuhan | Sekolah | `Not Started` | Phase 6 |
| **F-08** | Pencarian & Filter Kebutuhan | Publik, Donatur | `Not Started` | Phase 7 |
| **F-09** | Detail Kebutuhan Publik | Publik | `Not Started` | Phase 7 |
| **F-10** | Pengajuan Bantuan (*Pledge*) | Donatur/Relawan/Organisasi | `Not Started` | Phase 8 |
| **F-11** | Verifikasi *Pledge* | Admin | `Not Started` | Phase 8 |
| **F-12** | Manajemen Status Bantuan | Admin, Pihak Terkait | `Not Started` | Phase 9 |
| **F-13** | Upload Bukti Penyaluran | Donatur/Relawan | `Not Started` | Phase 9 |
| **F-14** | Tracking Bantuan & Riwayat | Sekolah, Donatur, Admin | `Not Started` | Phase 9 |
| **F-15** | Notifikasi Internal | Sekolah, Donatur, Admin | `Not Started` | Phase 9 |
| **F-16** | Kode Tracking Unik | Pengguna Terkait, Admin | `Not Started` | Phase 8 |
| **F-17** | Dashboard Statistik Publik & Admin | Publik, Admin | `Not Started` | Phase 10 |
| **F-18** | Manajemen Referensi Wilayah 3T | Admin, Publik | `Not Started` | Phase 5 |
| **F-19** | Direktori Sekolah Publik | Publik | `Not Started` | Phase 7 |
| **F-20** | Manajemen Pengguna & Moderasi | Admin | `Not Started` | Phase 4 |
| **F-21** | Manajemen *Pledge* Terpusat | Admin | `Not Started` | Phase 8 |
| **F-22** | Laporan & Ekspor CSV/Data | Admin | `Not Started` | Phase 10 |
| **F-23** | Mode Offline (PWA Cache) | — | **`Out of Scope`** | *Dikecualikan permanen* |
| **F-24** | Log Aktivitas (*Audit Trail*) | Admin | `Not Started` | Phase 11 |

---

## 7. Rekomendasi Urutan Pengembangan

1. **Phase 2 — Project Setup, Git & Database Schema Validation**:
   - Inisialisasi Git dan template `.gitignore`.
   - Setup project Laravel 10 dengan PHP 8.1.
   - Buat `docs/DATABASE_SCHEMA.md` dengan ERD Mermaid.
   - Buat file migrasi dan seeder untuk seluruh entitas utama.
2. **Phase 3 — UI Design Foundation & Layouts**:
   - Implementasikan design system token Red Broadcast di `resources/css/app.css` / CSS tokens.
   - Siapkan layout publik, auth, dan dashboard terintegrasi (navigasi, responsif, footer, flash message, badges).
3. **Phase 4 — Autentikasi, Role Authorization & Profil Pengguna** (F-02, F-03, F-19, F-20).
4. **Phase 5 — Profil Sekolah & Wilayah 3T** (F-01, F-04, F-17/F-18 wilayah 3T).
5. **Phase 6 — Manajemen Kebutuhan & Moderasi** (F-05, F-06, F-07, moderasi admin).
6. **Phase 7 — Landing Page & Direktori Publik** (F-08, F-09, F-18 direktori sekolah, F-19).
7. **Phase 8 — Pengajuan Bantuan (Pledge) & Verifikasi** (F-10, F-11, F-16, F-21).
8. **Phase 9 — Tracking Bantuan, Upload Bukti & Notifikasi Internal** (F-12, F-13, F-14, F-15).
9. **Phase 10 — Dashboard Statistik Aktual & Ekspor Laporan** (F-16/F-17 statistik, F-22 laporan).
10. **Phase 11 — Audit Log Aktivitas & Security Hardening** (F-24, IDOR/CSRF/XSS, validasi).
11. **Phase 12 — Testing Komprehensif & Final Polish** (Automated tests PHPUnit, browser check, UAT verification).
