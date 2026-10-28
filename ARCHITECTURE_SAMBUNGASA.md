# SambungAsa — Architecture & Implementation Roadmap
> Dokumen teknis implementasi website SambungAsa menggunakan Laravel, PHP, dan MySQL.  
> Versi: 1.0 | Tanggal: 28 Oktober 2026  
> Sumber requirement utama: `Template Proposal PPL_V01 (1).docx`

---

## 1. Tujuan Dokumen

Dokumen ini menjadi panduan implementasi dari tahap setup hingga deployment akhir. Isi dokumen mencakup arsitektur, struktur proyek, rancangan database, modul, aturan bisnis, keamanan, strategi pengujian, serta pembagian pengerjaan per phase.

**Keputusan teknologi yang sudah ditetapkan:**
- Backend dan web framework: **PHP + Laravel**.
- Database: **MySQL**.
- Nama database: **`sambungasa`**.
- Jenis aplikasi: **responsive web application**, dapat digunakan melalui browser desktop, tablet, dan mobile.
- Arsitektur: **modular monolith** dalam satu aplikasi Laravel.
- Antarmuka: Blade, HTML5, CSS, dan JavaScript; gunakan komponen UI konsisten dan responsif.
- Bahasa antarmuka: Bahasa Indonesia.
- PWA, Service Worker, IndexedDB, offline cache, dan fitur akses offline **tidak dibuat**.

> Dokumen proposal awal memuat F-23 Mode Offline (PWA Cache). Atas keputusan implementasi terbaru, F-23 dikeluarkan dari scope. Jangan membuat manifest PWA, Service Worker, IndexedDB, antrean sinkronisasi offline, ataupun klaim akses tanpa internet. Nomor requirement lain tetap dipertahankan untuk memudahkan pelacakan terhadap proposal; karena itu F-23 memang sengaja tidak digunakan.

## 2. Gambaran Produk dan Batasan

SambungAsa adalah platform web untuk menghubungkan sekolah di wilayah tertinggal, terdepan, dan terluar (3T) dengan donatur, relawan, guru profesional, dan organisasi. Platform menangani bantuan **material** maupun **nonmaterial** (tenaga pengajar, pendampingan, pelatihan).

### 2.1 Aktor utama

1. **Publik / pengunjung** — melihat landing page, statistik publik, direktori sekolah, daftar kebutuhan, detail kebutuhan, dan referensi wilayah 3T.
2. **Perwakilan Sekolah 3T** — mengelola profil sekolah dan kebutuhan; melihat pengajuan serta tracking bantuan yang berkaitan dengan sekolahnya.
3. **Donatur / Relawan / Organisasi** — mengelola profil, melihat kebutuhan, mengajukan pledge, mengunggah bukti, dan memantau kontribusinya.
4. **Admin** — memverifikasi akun/sekolah/lokasi, memoderasi kebutuhan dan pledge, mengelola pengguna, status, laporan, serta log aktivitas.

### 2.2 Batasan bisnis

- SambungAsa bukan marketplace jual-beli dan tidak memproses pembayaran atau transaksi dana.
- Penyaluran barang dan pelaksanaan kegiatan terjadi di luar aplikasi; aplikasi mencatat komitmen, status, bukti, dan riwayat.
- Scope awal wilayah berfokus pada Papua, Kalimantan, dan Sulawesi sesuai proposal, tetapi struktur data wilayah harus dapat diperluas.
- Fitur pencocokan relawan otomatis, pengaturan transportasi/akomodasi, kelas daring, dan operasional penempatan relawan tidak termasuk MVP.
- Semua operasi yang mengubah data memerlukan koneksi internet aktif.
- Hanya data yang memang ditujukan untuk publik yang boleh terlihat tanpa login.

## 3. Arsitektur Sistem

### 3.1 Gaya arsitektur

Gunakan **Modular Monolith**. Semua modul berjalan dalam satu aplikasi Laravel dan satu deployment, tetapi kode dipisahkan menurut domain agar mudah dikerjakan tim serta dipelihara. Jangan memecah sistem menjadi microservices untuk MVP.

```text
Browser (Desktop / Tablet / Mobile)
                 |
              HTTPS
                 |
      Laravel Web Application
      ├── Routes & Middleware
      ├── Controllers
      ├── Form Requests (validation)
      ├── Policies / Authorization
      ├── Services (business rules)
      ├── Models / Eloquent ORM
      ├── Notifications
      ├── File Upload & Storage
      └── Reporting / CSV-Excel Export
                 |
       ┌─────────┴─────────┐
       |                   |
   MySQL Database      File Storage
   sambungasa          bukti/foto/dokumen
```

### 3.2 Pola request

1. Pengguna membuka halaman melalui browser.
2. Route mengarahkan request ke middleware yang sesuai (guest/auth/role/verified).
3. Controller menerima request dan menggunakan Form Request untuk validasi input.
4. Policy/Gate memeriksa hak akses atas resource.
5. Service menjalankan aturan bisnis, termasuk transaksi database bila beberapa data harus berubah bersama.
6. Model Eloquent berinteraksi dengan MySQL.
7. Controller mengembalikan Blade view atau redirect dengan status/pesan yang sesuai.
8. Aktivitas penting dicatat pada audit log dan notifikasi internal dibuat jika diperlukan.

Gunakan REST-style route naming dan HTTP method yang sesuai, tetapi MVP boleh menggunakan **Laravel Blade server-rendered pages** tanpa mewajibkan SPA atau REST API terpisah. Jika nantinya API dibutuhkan, buat di `routes/api.php` tanpa memindahkan aturan bisnis keluar dari service layer.

### 3.3 Stack yang direkomendasikan

| Lapisan | Pilihan |
|---|---|
| Bahasa backend | PHP versi yang didukung versi Laravel yang dipilih |
| Framework | Laravel versi stabil yang kompatibel dengan environment tim |
| Frontend | Blade, HTML5, CSS, JavaScript |
| Styling | Tailwind CSS atau CSS terstruktur; pilih satu dan konsisten |
| Database | MySQL, database `sambungasa` |
| ORM & migration | Laravel Eloquent dan migrations |
| Authentication | Laravel authentication starter kit yang kompatibel, atau auth Laravel yang disusun tim |
| Authorization | Middleware role + Policies/Gates |
| Validasi | Laravel Form Request |
| File | Laravel Filesystem; local private storage untuk bukti |
| Export | CSV bawaan/streaming; Excel bila library dipilih dan dipasang |
| Testing | PHPUnit atau Pest (pilih satu), feature tests, browser/manual tests |
| Version control | Git dan GitHub |
| UI/UX | Figma untuk wireframe dan prototype |
| Deployment | Linux hosting/VPS, Nginx atau Apache, PHP-FPM bila Nginx, MySQL, HTTPS |

**Catatan versi:** sebelum instalasi, sepakati versi PHP dan Laravel berdasarkan dukungan resmi dan hosting target. Jangan memaksakan versi PHP yang tidak didukung framework atau server produksi.

### 3.4 Struktur direktori yang disarankan

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   ├── Auth/
│   │   ├── School/
│   │   ├── Donor/
│   │   └── Admin/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Policies/
├── Services/
│   ├── SchoolVerificationService.php
│   ├── NeedService.php
│   ├── PledgeService.php
│   ├── TrackingService.php
│   ├── NotificationService.php
│   ├── DashboardService.php
│   └── ReportExportService.php
└── Notifications/
resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── public/
│   ├── auth/
│   ├── school/
│   ├── donor/
│   └── admin/
├── css/
└── js/
routes/
├── web.php
└── api.php                 # opsional; tidak wajib untuk MVP
database/
├── migrations/
├── factories/
└── seeders/
storage/
└── app/
    └── private/
        └── evidence/
tests/
├── Feature/
└── Unit/
```

Struktur ini adalah pedoman; tim boleh menyesuaikan penamaan selama pemisahan modul, tanggung jawab, dan standar tetap konsisten.

## 4. Konfigurasi Awal Project

### 4.1 Buat project Laravel

Perintah berikut adalah contoh umum. Sesuaikan dengan versi PHP/Composer dan versi Laravel yang dipilih.

```bash
composer create-project laravel/laravel sambungasa
cd sambungasa
```

Siapkan `.env` untuk development:

```dotenv
APP_NAME=SambungAsa
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sambungasa
DB_USERNAME=root
DB_PASSWORD=
```

Buat database di MySQL:

```sql
CREATE DATABASE sambungasa
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Lalu jalankan:

```bash
php artisan key:generate
php artisan migrate
php artisan serve
```

> Jangan commit file `.env` atau kredensial ke Git. Untuk production, gunakan `APP_DEBUG=false`, password database yang kuat, dan secret yang berbeda dari development.

### 4.2 Aturan coding tim

- Gunakan PSR-12 dan Laravel conventions.
- Nama tabel plural `snake_case`; kolom `snake_case`; model singular `StudlyCase`.
- Gunakan migration untuk semua perubahan schema; hindari perubahan database manual yang tidak tercatat.
- Semua tanggal/waktu disimpan konsisten (disarankan UTC di server) dan ditampilkan sesuai timezone aplikasi.
- Gunakan foreign key, index, unique constraint, dan transaksi untuk menjaga integritas.
- Controller tipis; aturan bisnis kompleks berada di Service.
- Validasi input di server walaupun frontend juga melakukan validasi.
- Setiap fitur harus memiliki acceptance criteria dan test yang relevan.
- Gunakan pull request dan review sebelum merge ke branch utama.

## 5. Modul dan Requirement Fungsional

Nomor F mengikuti proposal agar backlog mudah ditelusuri. **F-23 dikecualikan** sesuai keputusan tidak menggunakan PWA/offline.

| ID | Fitur | Aktor | Hasil minimum yang harus tersedia |
|---|---|---|---|
| F-01 | Manajemen Profil Sekolah | Sekolah | Mengisi/mengubah nama sekolah, alamat, provinsi, kabupaten, kecamatan, dan koordinat yang diperlukan. |
| F-02 | Profil Donatur/Relawan/Organisasi | Donatur/Relawan/Organisasi | Mengelola identitas, kontak, jenis kontribusi; relawan dapat melengkapi keahlian, pendidikan, pengalaman, dan ketersediaan. |
| F-03 | Verifikasi Data Diri | Admin | Meninjau identitas, menyetujui/menolak dengan alasan, dan mencatat verifikator/waktu. |
| F-04 | Verifikasi Lokasi 3T | Admin | Memeriksa lokasi sekolah terhadap referensi wilayah 3T dan mencatat keputusan verifikasi. |
| F-05 | CRUD Kebutuhan | Sekolah | Membuat, melihat, mengubah, dan menghapus/menutup kebutuhan sesuai aturan kepemilikan. |
| F-06 | Kategorisasi Kebutuhan | Sekolah/Admin | Kategori awal: Buku/Alat Tulis, Elektronik/Komputer, Infrastruktur, Pelatihan, Lainnya. Kebutuhan nonmaterial tetap dapat ditandai sebagai bantuan nonmaterial. |
| F-07 | Prioritas Kebutuhan | Sekolah | Rendah, Sedang, Tinggi, Mendesak. |
| F-08 | Pencarian & Filter | Publik/Donatur/Relawan | Pencarian kata kunci dan filter wilayah, kategori, serta prioritas. |
| F-09 | Detail Kebutuhan | Publik | Menampilkan sekolah, deskripsi, kategori, prioritas, jumlah/kapasitas, progress, status, dan timeline publik yang relevan. |
| F-10 | Pengajuan Bantuan (Pledge) | Donatur/Relawan/Organisasi | Mengajukan jumlah/kapasitas kontribusi, catatan, estimasi penyaluran/pelaksanaan. |
| F-11 | Verifikasi Pledge | Admin | Memeriksa validitas dan kesesuaian; menyetujui atau menolak dengan alasan. |
| F-12 | Manajemen Status Bantuan | Admin | Status berurutan: DIAJUKAN → DIVERIFIKASI → DALAM PENGIRIMAN → DITERIMA → SELESAI; penolakan/pembatalan dicatat sebagai jalur khusus. |
| F-13 | Upload Bukti | Donatur/Relawan sesuai aturan status | Mengunggah foto/dokumen bukti penyaluran barang atau pelaksanaan kegiatan. |
| F-14 | Tracking Bantuan | Sekolah/Donatur | Melihat riwayat status, waktu perubahan, catatan, dan bukti yang diizinkan. |
| F-15 | Notifikasi Internal | Sekolah/Donatur/Admin | Notifikasi dalam aplikasi saat akun, kebutuhan, pledge, atau status bantuan berubah. |
| F-16 | Dashboard Statistik | Publik/Admin | Statistik kebutuhan, pemenuhan, dan gap per wilayah/kategori; publik hanya melihat agregat yang aman. |
| F-17 | Daftar Daerah 3T | Publik/Admin | Referensi wilayah 3T untuk informasi publik dan verifikasi sekolah. |
| F-18 | Direktori Sekolah | Publik | Daftar sekolah terdaftar, wilayah, dan kebutuhan aktif yang telah dipublikasikan. |
| F-19 | Kelola Pengguna | Admin | Melihat, memverifikasi, mengaktifkan/menonaktifkan akun dan melihat statusnya. |
| F-20 | Moderasi Kebutuhan | Admin | Meninjau, mengedit terbatas, menolak, menutup, atau menghapus kebutuhan tidak valid/duplikat dengan jejak audit. |
| F-21 | Kelola Pledge | Admin | Melihat, memvalidasi, menyetujui/menolak, serta mencatat alasan dan riwayat keputusan. |
| F-22 | Laporan & Ekspor | Admin | Mengekspor data kebutuhan, pledge, status, dan bantuan ke CSV; dukungan Excel dapat ditambahkan bila library disepakati. |
| F-24 | Log Aktivitas | Admin | Mencatat aktor, aksi, entitas, waktu, dan metadata yang aman untuk audit. |

### 5.1 Aturan akses inti

- Pengunjung tanpa login hanya dapat melihat halaman publik dan data yang sudah dipublikasikan.
- Akun sekolah hanya boleh mengubah profil dan kebutuhan milik sekolah yang terhubung dengan akunnya.
- Sekolah baru boleh menerbitkan kebutuhan setelah akun dan lokasi sekolah dinyatakan terverifikasi sesuai aturan sistem.
- Donatur hanya boleh mengubah profil, melihat pledge miliknya, dan mengunggah bukti pada pledge yang menjadi tanggung jawabnya.
- Admin hanya melakukan tindakan administratif sesuai permission; tindakan sensitif wajib tercatat.
- Akun nonaktif/ditolak tidak boleh melakukan aksi yang membutuhkan status aktif.
- Jangan mengandalkan penyembunyian tombol di UI; otorisasi wajib diperiksa di backend.

### 5.2 Kebutuhan nonfungsional yang dipertahankan

- **Performa:** target awal halaman publik dimuat kurang dari 5 detik pada koneksi 3G; kompres gambar, gunakan pagination, query efisien, dan lazy loading gambar bila sesuai.
- **Keamanan:** password hashing bawaan Laravel, HTTPS, CSRF protection, validasi server-side, authorization, perlindungan SQL injection melalui Eloquent/query binding, dan output escaping untuk XSS.
- **Keandalan:** transaksi database untuk operasi multi-tabel, backup berkala, logging error, serta prosedur pemulihan.
- **Skalabilitas:** wilayah dan kategori dikelola sebagai data referensi, tidak di-hard-code di banyak tempat.
- **Kompatibilitas:** browser modern; responsif di desktop, tablet, dan mobile.
- **Privasi:** kumpulkan data seperlunya, batasi akses identitas/kontak, dokumentasikan tujuan pemrosesan, dan sediakan kebijakan privasi.
- **Usability:** Bahasa Indonesia, alur ringkas, pesan error jelas, empty state, loading state, dan konfirmasi tindakan destruktif.
- **Maintainability:** modular, migration, seeders, dokumentasi, test, code review.
- **Koneksi:** aplikasi memerlukan internet untuk membuka data terbaru dan menyimpan perubahan; tidak ada jaminan fungsi offline.

## 6. Rancangan Database MySQL

Rancangan berikut adalah baseline implementasi yang perlu dicocokkan dengan ERD final tim. Nama tabel dapat disesuaikan sebelum migration pertama dibuat, tetapi perubahan harus disepakati dan didokumentasikan.

### 6.1 Entitas utama

| Tabel | Fungsi | Kolom inti yang disarankan |
|---|---|---|
| `users` | Akun dan autentikasi | `id`, `name`, `email` unique, `password`, `role`, `status`, `email_verified_at`, timestamps |
| `school_profiles` | Profil sekolah | `id`, `user_id` FK unique, `school_name`, `address`, `province_id`, `regency_id`, `district`, `latitude`, `longitude`, `verification_status`, `verified_by`, `verified_at`, `verification_note` |
| `contributor_profiles` | Profil donatur/relawan/organisasi | `id`, `user_id` FK unique, `contributor_type`, `organization_name` nullable, `phone`, `identity_reference` bila memang diperlukan, `verification_status`, `verified_by`, `verified_at` |
| `volunteer_profiles` | Detail relawan | `id`, `contributor_profile_id` FK unique, `education`, `expertise`, `experience`, `availability`, `bio` |
| `regions` | Referensi provinsi/kabupaten/wilayah 3T | `id`, `parent_id` nullable, `code` unique, `name`, `level`, `is_3t`, `is_active`, source/version metadata |
| `need_categories` | Kategori kebutuhan | `id`, `name` unique, `assistance_type` (`material`/`non_material`), `is_active` |
| `needs` | Kebutuhan sekolah | `id`, `school_profile_id` FK, `need_category_id` FK, `title`, `description`, `assistance_type`, `quantity_required` nullable, `quantity_fulfilled` default 0, `unit` nullable, `priority`, `status`, `published_at`, timestamps |
| `pledges` | Komitmen bantuan | `id`, `need_id` FK, `contributor_profile_id` FK, `quantity` nullable, `note`, `estimated_delivery_at` nullable, `status`, `tracking_code` unique, `reviewed_by`, `reviewed_at`, `rejection_reason`, timestamps |
| `tracking_logs` | Riwayat perubahan status | `id`, `pledge_id` FK, `old_status` nullable, `new_status`, `note` nullable, `created_by` FK users, `created_at` |
| `evidence_files` | Metadata bukti | `id`, `pledge_id` FK, `uploaded_by` FK users, `file_path`, `original_name`, `mime_type`, `file_size`, `evidence_type`, `caption` nullable, timestamps |
| `notifications` | Notifikasi internal | Gunakan tabel notifications Laravel atau tabel khusus yang konsisten; recipient, type, payload aman, read_at, timestamps |
| `activity_logs` | Audit aktivitas | `id`, `user_id` nullable, `action`, `entity_type`, `entity_id`, `description`/metadata JSON yang aman, `ip_address` bila dibutuhkan, `created_at` |
| `need_reviews` (opsional disarankan) | Moderasi kebutuhan | `id`, `need_id`, `admin_id`, `decision`, `note`, timestamps |
| `password_reset_tokens` | Reset password | Ikuti migration Laravel |
| `sessions` (opsional) | Session database | Gunakan jika session driver database dipilih |

**Penting:** jangan menyimpan password plaintext. Jangan menyimpan file bukti sebagai BLOB di MySQL; simpan file di storage dan metadata/path di database. Informasi identitas sensitif harus diminimalkan dan aksesnya dibatasi.

### 6.2 Relasi inti

```text
users 1 ── 0..1 school_profiles
users 1 ── 0..1 contributor_profiles
contributor_profiles 1 ── 0..1 volunteer_profiles
regions 1 ── * regions                  (parent-child wilayah)
regions 1 ── * school_profiles          (provinsi/kabupaten sesuai desain final)
school_profiles 1 ── * needs
need_categories 1 ── * needs
needs 1 ── * pledges
contributor_profiles 1 ── * pledges
pledges 1 ── * tracking_logs
pledges 1 ── * evidence_files
users 1 ── * activity_logs
users 1 ── * notifications
```

### 6.3 Catatan model data

- `users.role`: gunakan enum terkontrol atau role/permission table jika tim membutuhkan fleksibilitas. Nilai awal misalnya `admin`, `school`, `contributor`.
- `users.status`: minimal `pending`, `active`, `rejected`, `suspended`.
- Profil contributor membedakan `donor`, `volunteer`, dan `organization`; satu akun boleh mempunyai lebih dari satu jenis kontribusi hanya jika disepakati sebagai aturan produk.
- `needs.assistance_type` membedakan material dan nonmaterial. Untuk nonmaterial, kuantitas dapat merepresentasikan jumlah relawan, sesi, atau kapasitas; `unit` harus menjelaskan nilainya.
- Untuk pledge material, `quantity` wajib positif. Untuk pledge nonmaterial, gunakan `quantity`/kapasitas atau detail komitmen yang disepakati. Hindari validasi quantity yang tidak sesuai jenis kebutuhan.
- `quantity_fulfilled` harus dihitung dari kontribusi yang benar-benar diterima/selesai sesuai definisi bisnis, bukan dari semua pledge yang diajukan.
- Jika satu pledge harus mendukung beberapa kebutuhan, ubah desain menjadi `pledges` + `pledge_items`. Untuk MVP sederhana, satu pledge mengacu pada satu need; ini lebih mudah dilacak dan menghindari ambiguitas jumlah per kebutuhan. Pengajuan ke beberapa kebutuhan dilakukan sebagai beberapa pledge.
- Gunakan soft deletes secara selektif untuk kebutuhan atau profil bila diperlukan; data historis tracking dan audit tidak boleh hilang hanya karena suatu kebutuhan ditutup.
- Tambahkan index pada kolom pencarian/filter: `needs.status`, `needs.priority`, `needs.need_category_id`, `school_profiles.regency_id`, `pledges.status`, dan foreign key terkait.
- Gunakan constraint dan transaksi untuk mencegah total pledge yang disetujui/diterima melampaui kebutuhan yang tersedia.

### 6.4 Status dan transisi

**Status akun:** `pending` → `active` atau `rejected`; akun aktif dapat menjadi `suspended` oleh admin.

**Status profil sekolah:** `pending` → `verified` atau `rejected`; revisi dapat kembali ke `pending`.

**Status kebutuhan (usulan implementasi):** `draft` → `pending_review` → `published` → `fulfilled` atau `closed`; kebutuhan yang ditolak menjadi `rejected`. Hanya kebutuhan `published` yang muncul di direktori publik.

**Status pledge utama sesuai proposal:**
`submitted` (DIAJUKAN) → `verified` (DIVERIFIKASI) → `in_delivery` (DALAM PENGIRIMAN) → `received` (DITERIMA) → `completed` (SELESAI).

Jalur tambahan: `rejected` dan `cancelled` dicatat secara eksplisit. Jangan mengizinkan lompatan status tanpa aturan. Misalnya, pledge tidak dapat masuk `in_delivery` sebelum `verified`. Setiap perubahan status membuat `tracking_logs`, mengirim notifikasi yang sesuai, dan mencatat aktivitas dalam transaksi yang konsisten.

### 6.5 Aturan progress pemenuhan

- Untuk bantuan material, progress dapat dihitung dari kuantitas yang diterima/selesai dibandingkan kuantitas kebutuhan, dibatasi maksimum 100%.
- Untuk bantuan nonmaterial, definisikan metrik progress yang masuk akal (misalnya jumlah kapasitas/komitmen selesai) dan jelaskan di UI.
- Pledge `submitted` atau ditolak tidak dihitung sebagai bantuan terpenuhi.
- Rumus umum material: `min(quantity_fulfilled / quantity_required, 1) * 100`.
- Jika kebutuhan tidak berbasis kuantitas, gunakan status/kapasitas yang eksplisit dan jangan menampilkan persentase palsu.
- Perubahan jumlah kebutuhan setelah ada pledge aktif harus dibatasi atau melalui proses admin agar tidak merusak konsistensi.

## 7. Alur Bisnis End-to-End

### 7.1 Registrasi dan verifikasi

1. Pengguna memilih jenis akun dan mengisi data registrasi.
2. Server memvalidasi format, keunikan email, dan password.
3. Password di-hash menggunakan mekanisme Laravel.
4. Akun disimpan dengan status `pending` bila verifikasi admin diwajibkan.
5. Admin memeriksa identitas dan menyetujui/menolak dengan catatan.
6. Sistem mengirim notifikasi dan menulis activity log.
7. Pengguna aktif dapat melanjutkan sesuai role.

### 7.2 Profil dan verifikasi sekolah

1. Perwakilan sekolah melengkapi profil dan lokasi.
2. Sistem menyimpan profil berstatus menunggu verifikasi.
3. Admin memeriksa data lokasi terhadap tabel `regions` dan status 3T.
4. Admin menyetujui atau menolak dengan alasan.
5. Sekolah terverifikasi dapat mengajukan kebutuhan.

### 7.3 Kebutuhan sekolah

1. Sekolah mengisi judul, deskripsi, kategori, jenis bantuan, jumlah/unit bila relevan, dan prioritas.
2. Backend memvalidasi input serta hak kepemilikan.
3. Kebutuhan disimpan sebagai draft/pending review sesuai workflow yang dipilih.
4. Admin memoderasi kebutuhan.
5. Setelah dipublikasikan, kebutuhan tampil di direktori dan pencarian publik.
6. Edit/hapus dibatasi bila pledge aktif telah ada; gunakan close/arsip bila perlu mempertahankan riwayat.

### 7.4 Pengajuan pledge

1. Donatur/relawan membuka detail kebutuhan aktif.
2. Sistem memeriksa login, status akun, dan ketersediaan kebutuhan.
3. Pengguna mengisi kontribusi, jumlah/kapasitas, catatan, serta estimasi penyaluran/pelaksanaan.
4. Backend memvalidasi agar jumlah tidak melebihi sisa yang dapat dikomitmenkan.
5. Sistem membuat pledge `submitted`, tracking log awal, dan notifikasi admin.
6. Admin menyetujui atau menolak dengan alasan.
7. Pledge yang disetujui dapat berlanjut ke proses penyaluran/pelaksanaan.

### 7.5 Tracking, bukti, dan penyelesaian

1. Donatur/relawan mengunggah bukti sesuai tahap.
2. Backend memeriksa tipe MIME, ukuran file, ekstensi, dan hak akses.
3. File disimpan di storage privat dan metadata masuk `evidence_files`.
4. Admin atau pihak berwenang mengubah status sesuai aturan.
5. Setiap perubahan menghasilkan tracking log, notifikasi internal, dan activity log.
6. Sekolah/donatur melihat timeline yang sesuai dengan hak akses.
7. Saat status selesai dan kontribusi terpenuhi, progress kebutuhan diperbarui.

### 7.6 Dashboard dan laporan

1. Dashboard membaca agregat dari tabel kebutuhan, pledge, sekolah, dan wilayah.
2. Statistik publik hanya menampilkan agregat yang tidak membocorkan data pribadi.
3. Admin dapat memfilter laporan berdasarkan tanggal, wilayah, kategori, dan status.
4. Ekspor menggunakan query yang sama dengan filter pada layar.
5. Data ekspor harus dibatasi sesuai hak akses dan tidak menyertakan password/token atau data rahasia.

## 8. Peta Halaman dan Route

Nama route berikut merupakan usulan penamaan konsisten, bukan daftar final yang harus disalin tanpa review.

### 8.1 Halaman publik
- `GET /` — landing page
- `GET /needs` — direktori kebutuhan + search/filter
- `GET /needs/{need}` — detail kebutuhan
- `GET /schools` — direktori sekolah
- `GET /schools/{school}` — profil publik sekolah
- `GET /statistics` — dashboard statistik publik
- `GET /regions/3t` — daftar/referensi wilayah 3T
- `GET /login`, `GET /register` — autentikasi

### 8.2 Perwakilan sekolah
- `GET /school/dashboard`
- `GET|PUT /school/profile`
- `GET|POST /school/needs`
- `GET|PUT|DELETE /school/needs/{need}` — DELETE hanya bila aturan mengizinkan; selain itu tutup kebutuhan
- `GET /school/pledges` — pledge yang terkait dengan kebutuhan sekolah
- `GET /school/pledges/{pledge}` — tracking dan bukti yang diizinkan
- `GET /school/notifications`

### 8.3 Donatur/relawan/organisasi
- `GET /contributor/dashboard`
- `GET|PUT /contributor/profile`
- `GET /contributor/pledges`
- `GET|POST /contributor/needs/{need}/pledges`
- `GET /contributor/pledges/{pledge}`
- `POST /contributor/pledges/{pledge}/evidence`
- `GET /contributor/notifications`

### 8.4 Admin
- `GET /admin/dashboard`
- `GET /admin/users`, `GET /admin/users/{user}`
- `POST /admin/users/{user}/verify`, `POST /admin/users/{user}/reject`, `POST /admin/users/{user}/suspend`
- `GET /admin/schools/verification`
- `POST /admin/schools/{school}/verify`, `POST /admin/schools/{school}/reject`
- `GET /admin/needs`, `GET /admin/needs/{need}`
- `POST /admin/needs/{need}/publish`, `POST /admin/needs/{need}/reject`, `POST /admin/needs/{need}/close`
- `GET /admin/pledges`, `GET /admin/pledges/{pledge}`
- `POST /admin/pledges/{pledge}/approve`, `POST /admin/pledges/{pledge}/reject`, `POST /admin/pledges/{pledge}/status`
- `GET /admin/reports`, `GET /admin/reports/export`
- `GET /admin/activity-logs`

Semua route harus diberi middleware autentikasi dan role yang benar. Gunakan route model binding dengan Policy untuk pemeriksaan kepemilikan; jangan mempercayai ID dari browser.

## 9. Phase Implementasi dari Awal sampai Selesai

Urutan di bawah adalah roadmap implementasi yang dapat dijalankan sebagai sprint mingguan. Timeline proposal awal digunakan sebagai referensi, tetapi phase ini disusun berdasarkan dependensi teknis. Jika proyek sudah memiliki sebagian pekerjaan, tandai item yang selesai setelah diperiksa, jangan mengulang tanpa kebutuhan.

### Phase 0 — Finalisasi Requirement & Setup (2–3 hari)

**Tujuan:** menyepakati scope dan menyiapkan repository serta lingkungan development.

Pekerjaan:
- [ ] Kunci keputusan: Laravel + PHP + MySQL `sambungasa`.
- [ ] Keluarkan PWA/offline dari backlog; tandai F-23 sebagai *out of scope/removed*.
- [ ] Konfirmasi dukungan bantuan material dan nonmaterial.
- [ ] Tetapkan role, status akun, status sekolah, status kebutuhan, dan status pledge.
- [ ] Finalisasi acceptance criteria per F-01–F-22 dan F-24.
- [ ] Buat repository GitHub, branch strategy, `.gitignore`, README awal.
- [ ] Tentukan versi PHP/Laravel, dependency, style framework, dan strategi test.
- [ ] Buat file `.env.example` tanpa secret.
- [ ] Buat task board dan pemilik tiap modul.

**Output:** requirement baseline, backlog yang bersih, repository, aplikasi Laravel berjalan lokal.

**Definition of Done:** setiap anggota dapat menjalankan project, database lokal terkoneksi, dan scope PWA/offline tidak ada pada implementasi.

### Phase 1 — Desain Sistem & UI/UX (3–5 hari)

**Tujuan:** membuat desain yang cukup matang sebelum fitur utama dikerjakan.

Pekerjaan:
- [ ] Finalisasi arsitektur modular monolith.
- [ ] Finalisasi ERD, relasi, constraint, dan data referensi.
- [ ] Finalisasi class diagram dan pembagian tanggung jawab kelas.
- [ ] Buat use case dan alur registrasi, verifikasi, kebutuhan, pledge, tracking.
- [ ] Buat wireframe landing page, direktori, detail kebutuhan, dashboard sekolah, dashboard contributor, dashboard admin.
- [ ] Tentukan design system: warna, typography, spacing, button, form, table, badge status, alert, modal, pagination.
- [ ] Review accessibility dasar: kontras, label form, keyboard navigation, error message.
- [ ] Buat data dictionary dan definisi metrik dashboard.

**Output:** ERD, class diagram, use case/process flow, UI mockup, data dictionary.

**Definition of Done:** semua tabel dan status memiliki definisi; PO menyetujui alur utama dan tampilan inti.

### Phase 2 — Fondasi Laravel, Database & Layout (3–5 hari)

**Tujuan:** menyiapkan fondasi yang digunakan semua modul.

Pekerjaan:
- [ ] Konfigurasi koneksi database `sambungasa`.
- [ ] Buat migrations dasar, foreign keys, indexes, dan timestamps.
- [ ] Buat model Eloquent serta relasi.
- [ ] Buat seeder role, kategori, status, wilayah contoh, dan admin development.
- [ ] Buat layout Blade utama, navbar, footer, sidebar dashboard, flash message, pagination.
- [ ] Buat exception/error handling dan halaman 403/404/419/500.
- [ ] Buat helper untuk status badge dan format tanggal/angka bila diperlukan.
- [ ] Siapkan logging dan konfigurasi upload file.
- [ ] Siapkan pipeline test dasar dan instruksi setup pada README.

**Output:** skeleton aplikasi, schema awal, layout bersama, seed data.

**Definition of Done:** `php artisan migrate:fresh --seed` berhasil di environment development dan layout dasar konsisten.

### Phase 3 — Authentication, Role & Profil Pengguna (1 sprint)

**Requirement:** autentikasi dan F-02, F-03, F-19; fondasi F-01.

Pekerjaan:
- [ ] Registrasi, login, logout, validasi email, dan reset password sesuai kebutuhan.
- [ ] Hash password menggunakan mekanisme Laravel.
- [ ] Role: admin, school, contributor.
- [ ] Status akun: pending, active, rejected, suspended.
- [ ] Middleware role dan Policy/Gate.
- [ ] Form profil contributor dan data relawan.
- [ ] Halaman admin untuk meninjau identitas dan mengaktifkan/menolak akun.
- [ ] Catatan alasan penolakan dan notifikasi internal.
- [ ] Audit log untuk registrasi, verifikasi, dan perubahan status akun.
- [ ] Test akses lintas role dan akses akun nonaktif.

**Output:** autentikasi berfungsi dan akses dibatasi berdasarkan role/status.

**Acceptance criteria:**
- Email unik dan password tidak pernah disimpan plaintext.
- Pengguna pending/nonaktif tidak bisa mengakses aksi yang dibatasi.
- Contributor tidak dapat mengakses halaman admin.
- Keputusan verifikasi menyimpan admin, waktu, status, dan alasan bila ditolak.

### Phase 4 — Profil Sekolah & Referensi Wilayah 3T (1 sprint)

**Requirement:** F-01, F-04, F-17.

Pekerjaan:
- [ ] CRUD profil sekolah untuk akun sekolah.
- [ ] Data lokasi: alamat, provinsi, kabupaten, kecamatan, koordinat bila dipakai.
- [ ] Import/seed daftar wilayah yang valid dari sumber yang disepakati tim.
- [ ] Relasi hierarki wilayah dan penanda `is_3t`.
- [ ] Antrean verifikasi sekolah untuk admin.
- [ ] Approve/reject profil dengan catatan.
- [ ] Validasi agar hanya sekolah terverifikasi yang dapat membuat/mempublikasikan kebutuhan.
- [ ] Tampilkan daftar wilayah 3T untuk publik.
- [ ] Test kepemilikan profil dan kasus lokasi tidak valid.

**Output:** profil sekolah dan proses verifikasi lokasi.

**Acceptance criteria:** admin dapat memverifikasi lokasi; sekolah belum terverifikasi tidak dapat mempublikasikan kebutuhan.

### Phase 5 — Manajemen Kebutuhan & Moderasi (1 sprint)

**Requirement:** F-05, F-06, F-07, F-20.

Pekerjaan:
- [ ] CRUD kebutuhan milik sekolah sendiri.
- [ ] Kategori material/nonmaterial dan kategori yang disepakati proposal.
- [ ] Prioritas rendah/sedang/tinggi/mendesak.
- [ ] Field jumlah dan satuan untuk kebutuhan berbasis kuantitas.
- [ ] Status draft/pending review/published/rejected/fulfilled/closed.
- [ ] Antrean moderasi kebutuhan admin.
- [ ] Validasi edit/hapus ketika pledge aktif telah ada.
- [ ] Pencatatan alasan penolakan/moderasi.
- [ ] Test validasi quantity, kategori, prioritas, kepemilikan, dan status.

**Output:** sekolah dapat mengelola kebutuhan; admin dapat memoderasi.

**Acceptance criteria:** hanya kebutuhan published tampil untuk publik; pengguna sekolah tidak dapat mengubah kebutuhan milik sekolah lain.

### Phase 6 — Landing Page, Direktori, Search & Detail (1 sprint)

**Requirement:** F-08, F-09, F-18; dasar F-16.

Pekerjaan:
- [ ] Landing page SambungAsa yang menjelaskan tujuan platform dan CTA.
- [ ] Direktori sekolah dan kebutuhan aktif.
- [ ] Pencarian kata kunci.
- [ ] Filter wilayah, kategori, prioritas, dan jenis bantuan.
- [ ] Pagination dan empty state.
- [ ] Halaman detail kebutuhan dengan profil sekolah, status, progress, dan timeline publik.
- [ ] Halaman profil sekolah publik dengan pembatasan data pribadi.
- [ ] Tampilan responsif untuk desktop/tablet/mobile.
- [ ] Optimasi query eager loading, indeks, ukuran gambar, dan pagination.

**Output:** pengguna publik dapat menemukan sekolah dan kebutuhan yang relevan.

**Acceptance criteria:** kebutuhan draft/rejected/closed tidak tampil sebagai kebutuhan aktif; filter dapat digabung dan pagination tetap menjaga filter.

### Phase 7 — Pledge & Verifikasi Pengajuan (1 sprint)

**Requirement:** F-10, F-11, F-21.

Pekerjaan:
- [ ] Form pengajuan pledge dari halaman detail kebutuhan.
- [ ] Simpan jumlah/kapasitas, catatan, dan estimasi penyaluran/pelaksanaan.
- [ ] Buat tracking code yang unik dan tidak mudah ditebak.
- [ ] Status awal `submitted`.
- [ ] Validasi ketersediaan kebutuhan dan jumlah tersisa.
- [ ] Halaman daftar/detail pledge untuk contributor dan admin.
- [ ] Approve/reject pledge oleh admin dengan alasan.
- [ ] Batasi pengajuan oleh akun belum aktif.
- [ ] Gunakan database transaction/locking bila dua pledge bersamaan berpotensi melebihi kebutuhan.
- [ ] Buat tracking log pertama dan notifikasi admin.

**Output:** pengajuan komitmen bantuan dan verifikasi berjalan.

**Acceptance criteria:** pledge tidak dapat dibuat untuk kebutuhan tidak aktif; jumlah tidak dapat melampaui sisa yang tersedia; penolakan memiliki alasan; keputusan tersimpan dalam audit log.

### Phase 8 — Status Tracking, Bukti & Notifikasi (1 sprint)

**Requirement:** F-12, F-13, F-14, F-15.

Pekerjaan:
- [ ] Implementasikan transisi status yang valid.
- [ ] Tampilkan timeline tracking untuk sekolah dan contributor terkait.
- [ ] Upload bukti penyaluran barang atau dokumentasi kegiatan relawan.
- [ ] Validasi file: ukuran, MIME, ekstensi yang diizinkan, nama file acak.
- [ ] Simpan bukti di private storage; akses unduh lewat route terotorisasi.
- [ ] Buat notifikasi internal dan penanda sudah dibaca.
- [ ] Update progress kebutuhan ketika kontribusi mencapai status yang disepakati untuk dihitung.
- [ ] Catat setiap perubahan status dan upload bukti pada activity log.
- [ ] Test akses file oleh pengguna yang tidak berkepentingan.

**Output:** tracking end-to-end, dokumentasi, dan notifikasi.

**Acceptance criteria:** transisi status ilegal ditolak; setiap transisi valid menghasilkan tracking log; bukti tidak dapat diakses oleh pengguna yang tidak berhak.

### Phase 9 — Dashboard Statistik & Ekspor (1 sprint)

**Requirement:** F-16, F-22.

Pekerjaan:
- [ ] Dashboard publik berisi statistik agregat kebutuhan dan pemenuhan.
- [ ] Dashboard admin berisi total pengguna, sekolah, kebutuhan, pledge aktif, dan ringkasan status.
- [ ] Grafik/filter berdasarkan wilayah, kategori, dan status bila sesuai.
- [ ] Definisikan gap analysis secara tertulis dan konsisten.
- [ ] Laporan kebutuhan dan pledge dengan filter tanggal/wilayah/status.
- [ ] Ekspor CSV; ekspor Excel jika library sudah dipilih dan diuji.
- [ ] Batasi ekspor hanya untuk admin yang berwenang.
- [ ] Cegah formula injection pada CSV (misalnya nilai yang diawali `=`, `+`, `-`, `@`).
- [ ] Test kesesuaian angka dashboard dengan data sumber.

**Output:** dashboard dan laporan yang dapat dipertanggungjawabkan.

**Acceptance criteria:** angka agregat sesuai database; statistik publik tidak membeberkan identitas/kontak pribadi; ekspor mengikuti filter yang dipilih.

### Phase 10 — Activity Log, Keamanan & Hardening (3–5 hari)

**Requirement:** F-24 dan semua NF keamanan.

Pekerjaan:
- [ ] Lengkapi log untuk registrasi, login penting, verifikasi, moderasi, pledge, perubahan status, upload bukti, dan ekspor.
- [ ] Halaman pencarian log untuk admin dengan pagination/filter.
- [ ] Pastikan log tidak menyimpan password, token, atau isi data sensitif berlebihan.
- [ ] Review CSRF, XSS, SQL injection, mass assignment, IDOR, dan file upload.
- [ ] Pastikan Policy diterapkan pada setiap resource privat.
- [ ] Rate limit login dan endpoint sensitif.
- [ ] Periksa validasi server-side, session/cookie settings, dan HTTPS production.
- [ ] Tambahkan halaman kebijakan privasi dan ketentuan penggunaan sesuai keputusan tim.
- [ ] Verifikasi tidak ada service worker, manifest PWA, IndexedDB, atau fitur offline.

**Output:** aplikasi dengan audit trail dan baseline keamanan yang layak.

**Acceptance criteria:** tindakan penting dapat ditelusuri; akses ilegal ditolak; secret tidak ada di repository; tidak ada fitur offline/PWA.

### Phase 11 — Testing, UAT, Bug Fixing & Deployment (1 sprint)

**Tujuan:** memverifikasi semua fitur sebelum rilis.

Pekerjaan:
- [ ] Unit test untuk aturan bisnis (status, quantity, progress, permission).
- [ ] Feature test untuk alur sekolah, contributor, dan admin.
- [ ] Test integrasi database dan upload.
- [ ] Uji semua acceptance criteria F-01–F-22 dan F-24.
- [ ] Uji browser modern dan layout mobile.
- [ ] Uji koneksi lambat, ukuran gambar, pagination, dan query berat.
- [ ] UAT oleh Product Owner/stakeholder.
- [ ] Perbaiki bug kritis/tinggi dan lakukan regression test.
- [ ] Siapkan environment production, database, storage permission, HTTPS, backup, logging, dan monitoring.
- [ ] Jalankan migration production setelah backup dan rencana rollback disiapkan.
- [ ] Jalankan `php artisan config:cache`, `route:cache`, dan `view:cache` jika kompatibel dengan konfigurasi aplikasi.
- [ ] Pastikan `APP_DEBUG=false`.
- [ ] Dokumentasikan deployment, konfigurasi, cara backup/restore, dan akun admin pertama.
- [ ] Demo final dan serah terima.

**Output:** website production, laporan testing, dokumentasi, dan bukti acceptance.

**Definition of Done final:** seluruh requirement dalam scope lolos acceptance test; tidak ada blocker/bug kritis terbuka; backup dan restore diuji; HTTPS aktif; README dan dokumentasi penggunaan tersedia.

## 10. Matriks Traceability Requirement ke Phase

| Requirement | Phase utama |
|---|---|
| F-01 Profil sekolah | 4 |
| F-02 Profil contributor/relawan | 3 |
| F-03 Verifikasi data diri | 3 |
| F-04 Verifikasi lokasi 3T | 4 |
| F-05 CRUD kebutuhan | 5 |
| F-06 Kategori kebutuhan | 5 |
| F-07 Prioritas kebutuhan | 5 |
| F-08 Pencarian/filter | 6 |
| F-09 Detail kebutuhan | 6 |
| F-10 Pledge | 7 |
| F-11 Verifikasi pledge | 7 |
| F-12 Status bantuan | 8 |
| F-13 Bukti penyaluran/kegiatan | 8 |
| F-14 Tracking | 8 |
| F-15 Notifikasi internal | 8 |
| F-16 Dashboard statistik | 9 |
| F-17 Daftar wilayah 3T | 4 |
| F-18 Direktori sekolah | 6 |
| F-19 Kelola pengguna | 3 |
| F-20 Moderasi kebutuhan | 5 |
| F-21 Kelola pledge | 7 |
| F-22 Laporan/ekspor | 9 |
| F-23 Mode offline/PWA | **Dihapus dari scope; tidak diimplementasikan** |
| F-24 Log aktivitas | 3, 4, 5, 7, 8, 10 |
| NF performa, keamanan, responsif, privasi, maintainability | 2–11, diuji ulang di 11 |

## 11. Strategi Pengujian

### 11.1 Jenis test

- **Unit test:** transisi status, perhitungan progress, validasi sisa kebutuhan, service rules.
- **Feature test:** login, role access, CRUD, verifikasi, pledge, tracking, upload, notifikasi, dashboard, export.
- **Authorization test:** school A tidak bisa mengubah data school B; contributor A tidak bisa membaca pledge contributor B; non-admin tidak dapat membuka admin route.
- **Validation test:** field kosong, tipe data salah, quantity nol/negatif, jumlah berlebih, file terlalu besar/jenis terlarang, duplikasi email.
- **Regression test:** jalankan test relevan setelah perubahan model, migration, status, dan permission.
- **UAT:** skenario dari sudut pandang sekolah, donatur/relawan, dan admin.
- **Performance checks:** query count, pagination, gambar, dan respons halaman publik pada koneksi terbatas.
- **Security checks:** IDOR, XSS, CSRF, upload file, rate limiting, secret exposure, serta ekspor CSV.

### 11.2 Skenario end-to-end wajib

1. Sekolah mendaftar → admin memverifikasi akun → sekolah mengisi profil → admin memverifikasi lokasi.
2. Sekolah membuat kebutuhan → admin memoderasi → kebutuhan muncul di direktori publik.
3. Contributor aktif mengajukan pledge → admin menyetujui → tracking code dan log dibuat.
4. Contributor mengunggah bukti → status diperbarui secara valid → sekolah dapat melihat tracking.
5. Pledge selesai → progress kebutuhan dihitung dengan benar → dashboard berubah.
6. Admin mengekspor laporan → hasil CSV sesuai filter dan tidak mengandung data rahasia.
7. Pengguna tanpa hak mencoba membuka URL resource orang lain → sistem menolak.
8. Admin melakukan verifikasi/penolakan → alasan dan activity log tersedia.
9. Browser offline → aplikasi tidak menjanjikan operasi offline; perubahan memerlukan koneksi aktif.

## 12. Keamanan dan Privasi

Checklist wajib:
- [ ] Password menggunakan hashing Laravel.
- [ ] HTTPS aktif pada production.
- [ ] CSRF protection untuk form state-changing.
- [ ] Validasi semua input di server.
- [ ] Eloquent/query binding; hindari raw SQL yang menggabungkan input pengguna.
- [ ] Output Blade di-escape; hindari `{!! !!}` untuk input pengguna kecuali sanitasi khusus sudah diterapkan.
- [ ] Authorization dengan middleware dan Policy, bukan hanya menyembunyikan tombol.
- [ ] Proteksi mass assignment melalui `$fillable`/`$guarded` yang tepat.
- [ ] File upload membatasi MIME, ukuran, ekstensi, dan nama file.
- [ ] File bukti privat dan hanya dapat diakses lewat controller yang memeriksa hak akses.
- [ ] Rate limit login dan aksi sensitif.
- [ ] Audit log tidak memuat password, session token, atau data sensitif berlebihan.
- [ ] Secret hanya di environment variable; `.env` tidak masuk Git.
- [ ] Backup database dan file dengan akses terbatas.
- [ ] Kebijakan retensi/penghapusan data ditetapkan.
- [ ] Penggunaan data pribadi mengikuti kebijakan yang disetujui tim dan ketentuan perlindungan data yang berlaku.

## 13. Deployment Checklist

### Sebelum deploy
- [ ] Tes lulus dan UAT disetujui.
- [ ] Production environment memakai versi PHP/Laravel yang didukung.
- [ ] Buat database MySQL production bernama `sambungasa` dan user database dengan hak minimum.
- [ ] Isi environment variables tanpa membagikan secret.
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS.
- [ ] Folder `storage` dan `bootstrap/cache` memiliki permission yang tepat.
- [ ] Storage bukti dikonfigurasi privat.
- [ ] HTTPS/SSL aktif.
- [ ] Backup awal database dan file tersedia.

### Saat deploy
- [ ] Pasang dependency production.
- [ ] Jalankan migration dengan rencana rollback.
- [ ] Buat cache konfigurasi/route/view bila aman untuk aplikasi.
- [ ] Jalankan seeder hanya untuk data referensi yang memang diperlukan; jangan seed data dummy ke production.
- [ ] Buat akun admin awal melalui prosedur aman.
- [ ] Verifikasi halaman publik, login per role, upload bukti, notifikasi, laporan, dan permission.

### Sesudah deploy
- [ ] Tes smoke test.
- [ ] Periksa error log dan log web server.
- [ ] Uji backup/restore di lingkungan aman.
- [ ] Dokumentasikan URL production, prosedur update, backup, restore, dan kontak penanggung jawab.
- [ ] Pastikan tidak ada PWA manifest/service worker/offline code yang ikut ter-deploy.

## 14. Git Workflow dan Kolaborasi Tim

Alur yang disarankan:
1. Buat issue/task untuk setiap fitur atau bug.
2. Buat branch pendek, misalnya `feature/F-05-manajemen-kebutuhan`.
3. Commit dengan pesan deskriptif dan referensi issue.
4. Push branch dan buat Pull Request.
5. Reviewer memeriksa kode, migration, authorization, test, dan kesesuaian requirement.
6. Jalankan test sebelum merge.
7. Merge ke branch integrasi/main sesuai aturan tim.
8. Catat keputusan schema/API/status di dokumentasi.

Hindari dua anggota mengubah migration yang sama secara bersamaan tanpa koordinasi. Perubahan database harus disepakati dengan penanggung jawab database dan disertai instruksi migrasi.

## 15. Struktur Dokumentasi yang Harus Disimpan di Repository

```text
docs/
├── ARCHITECTURE.md          # dokumen ini
├── REQUIREMENTS.md          # F-01–F-22 dan F-24 + acceptance criteria
├── ERD.md                   # ERD dan data dictionary
├── CLASS_DIAGRAM.md         # tanggung jawab model/service/controller
├── BUSINESS_FLOWS.md        # alur registrasi, kebutuhan, pledge, tracking
├── TEST_PLAN.md             # skenario dan hasil testing
├── DEPLOYMENT.md            # setup, deploy, backup, restore
└── USER_GUIDE.md            # panduan sekolah, contributor, dan admin
```

## 16. Risiko dan Keputusan yang Perlu Dikonfirmasi

1. **Definisi satu pledge:** roadmap menggunakan satu pledge untuk satu kebutuhan. Jika proposal/PO mewajibkan satu formulir untuk banyak kebutuhan, desain database harus memakai `pledge_items`.
2. **Verifikasi kebutuhan:** proposal menyinggung moderasi sebelum publikasi; roadmap menggunakan status `pending_review` agar kebutuhan tidak otomatis tampil. PO harus menyetujui workflow final.
3. **Bantuan nonmaterial:** sepakati cara menghitung progress dan status pelaksanaan (misalnya kapasitas relawan, kegiatan terjadwal, kegiatan selesai). Jangan memaksakan quantity barang ke semua kontribusi.
4. **Sumber data wilayah 3T:** tentukan sumber resmi, tanggal pembaruan, format kode wilayah, dan siapa yang bertanggung jawab memperbarui.
5. **Notifikasi:** MVP menggunakan notifikasi internal. Email/WhatsApp tidak otomatis termasuk scope kecuali disepakati.
6. **Ekspor Excel:** CSV wajib sebagai baseline; format XLSX memerlukan pemilihan library dan pengujian tambahan.
7. **Penyimpanan bukti:** tentukan batas ukuran, jenis file, retensi, backup, dan kebutuhan object storage berdasarkan hosting.
8. **Peta/koordinat:** proposal menyebut koordinat/lokasi; peta interaktif eksternal bukan keharusan kecuali PO memasukkannya ke backlog.
9. **Perubahan status:** tetapkan role yang boleh memajukan setiap tahap dan apakah sekolah perlu mengonfirmasi penerimaan sebelum status `received/completed`.
10. **Scope offline:** sudah diputuskan **tidak ada PWA/offline**. Semua dokumen, backlog, test, dan demo harus konsisten dengan keputusan ini.

## 17. Definition of Done per Fitur

Sebuah fitur dianggap selesai bila:
- [ ] Requirement dan acceptance criteria terpenuhi.
- [ ] Validasi server-side tersedia.
- [ ] Role/permission dan kepemilikan resource diuji.
- [ ] UI memiliki loading/empty/error/success state yang sesuai.
- [ ] Perubahan schema memakai migration dan data referensi memakai seeder bila relevan.
- [ ] Test otomatis atau skenario uji terdokumentasi tersedia.
- [ ] Log/notifikasi dibuat bila tindakan tersebut termasuk proses audit.
- [ ] Tidak ada secret atau data sensitif pada log/response.
- [ ] Kode telah direview dan merge.
- [ ] Dokumentasi singkat diperbarui.

---

## Ringkasan Final Scope

**Dibangun:** website SambungAsa responsif dengan Laravel + PHP + MySQL (`sambungasa`), role sekolah/contributor/admin, verifikasi akun dan wilayah 3T, manajemen kebutuhan material/nonmaterial, direktori dan filter, pledge, verifikasi, tracking, upload bukti, notifikasi internal, dashboard statistik, ekspor CSV/Excel, dan activity log.

**Tidak dibangun:** PWA, Service Worker, IndexedDB, offline cache, mode read-only offline, sinkronisasi offline, transaksi pembayaran, marketplace, kelas daring, pencocokan relawan otomatis, dan pengelolaan transportasi/akomodasi relawan.

**Urutan utama:** Requirement & Setup → Design → Foundation & Database → Auth/Profile → School & 3T → Needs & Moderation → Public Directory → Pledge → Tracking/Evidence/Notifications → Dashboard/Export → Audit/Security → Testing/UAT/Deployment.
