# SambungAsa — Menghubungkan Sekolah 3T dengan Bantuan Pendidikan

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![Tests](https://img.shields.io/badge/Tests-40%20Passed%20(125%20assertions)-brightgreen?style=flat)]()
[![Design](https://img.shields.io/badge/Design-Red%20Broadcast-red?style=flat)]()

**SambungAsa** adalah aplikasi web responsif yang menghubungkan sekolah-sekolah di wilayah **3T (Tertinggal, Terdepan, dan Terluar)** di Indonesia — khususnya Papua, Kalimantan, dan Sulawesi — dengan donatur, relawan pengajar, dan organisasi bantuan pendidikan secara langsung, transparan, dan akuntabel.

> ⚠️ **Catatan Penting Ruang Lingkup:**
> SambungAsa **bukan marketplace** dan **tidak memproses transaksi keuangan / uang tunai**. Platform ini memfasilitasi komitmen bantuan fisik dan misi relawan melalui Berita Acara Serah Terima (BAST) dan pelacakan digital.

---

## 🚀 Fitur Utama

- **F-01 Wilayah 3T Terintegrasi:** Data referensi resmi provinsi dan kabupaten 3T (Papua, Kalimantan, Sulawesi).
- **F-02 Registrasi Multi-Peran:** Pendaftaran khusus Sekolah 3T, Donatur Pribadi, Relawan Pendidikan, dan Organisasi / Korporasi.
- **F-03 Autentikasi & RBAC:** Proteksi rute berbasis peran (Admin, Sekolah, Donatur/Relawan) dan pembatasan status akun aktif.
- **F-04 Verifikasi Sekolah 3T:** Kurasi dan validasi keabsahan data sekolah (NPSN, koordinat geografis 3T, SK pendirian).
- **F-05 Pengajuan Kebutuhan Sekolah:** Formulir kebutuhan material (buku, komputer, fasilitas) dan non-material (relawan pengajar, pelatihan).
- **F-06 Moderasi Kebutuhan:** Validasi kebutuhan oleh administrator sebelum dipublikasikan ke direktori publik.
- **F-07 Penutupan & Update Kebutuhan:** Kontrol kuota kebutuhan dan penutupan penerimaan bantuan jika sudah terpenuhi.
- **F-08 Katalog Kebutuhan Publik:** Pencarian terfilter berdasarkan pulau, provinsi, kategori, prioritas, dan kata kunci.
- **F-09 Direktori Sekolah 3T:** Profil profil sekolah penerima manfaat dan riwayat kebutuhan yang diajukan.
- **F-10 Komitmen Bantuan (Pledge):** Form pengajuan komitmen barang/relawan dengan estimasi waktu dan catatan kargo.
- **F-11 Pencegahan Over-Pledging:** Penguncian transaksi database (*pessimistic locking*) agar bantuan tidak melebihi kuota.
- **F-12 Pelacakan Status & Kode Unik:** Kode tracking unik `SA-YYYYMM-XXXX` dengan riwayat log perjalanan.
- **F-13 Bukti Penyaluran Privat & Aman:** Unggah bukti resi, foto serah terima, dan dokumen BAST yang dilindungi otorisasi IDOR.
- **F-14 Konfirmasi Penerimaan Sekolah:** Validasi penerimaan fisik oleh pihak sekolah dengan catatan kondisi barang.
- **F-15 Pembaruan Otomatis Kebutuhan:** Update kuantitas terpenuhi secara otomatis ketika penyaluran diselesaikan.
- **F-16 & F-17 Transparansi & Statistik Dampak:** Metrik capaian nyata dari basis data (sekolah terverifikasi, persentase pemenuhan kategori, sebaran wilayah).
- **F-19 & F-20 Manajemen Akun & Moderasi Admin:** Kontrol pengguna, suspend/aktivasi akun, dan antrean kurasi.
- **F-21 Notifikasi In-App:** Pembaruan status secara langsung bagi donatur dan sekolah di dashboard.
- **F-22 Laporan & Ekspor CSV:** Unduh rekapitulasi penyaluran bantuan terformat CSV dengan sanitasi anti-formula injection.
- **F-24 Audit Trail (Activity Logs):** Pencatatan menyeluruh setiap aksi penting pengguna dan administrator.

---

## 🎨 Sistem Desain: Red Broadcast

Aplikasi ini menggunakan sistem desain murni CSS **Red Broadcast** (`public/css/sambungasa.css`):
- **Warna Identitas:** Broadcast Red (`#FF0000`), Dark `#0F0F0F`, Link Blue `#065FD4`, Gray 600 `#606060`.
- **Tipografi:** Google Font Roboto (400, 500, 700) & Roboto Mono untuk kode tracking.
- **Komponen:** Tombol kapsul (*pill buttons* `border-radius: 9999px`), chip bar, elevasi bertingkat, dan KPI card grid responsif.

---

## 🛠️ Prasyarat & Lingkungan

- **PHP:** >= 8.1
- **Composer:** >= 2.0
- **MySQL / MariaDB:** >= 8.0 (atau 10.4+)
- **Laravel Framework:** 10.x

---

## 📦 Instalasi & Menjalankan Proyek

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/PPLKelompokB2401/sambungasa.git
   cd sambungasa
   ```

2. **Instal Dependensi:**
   ```bash
   composer install
   ```

3. **Konfigurasi Lingkungan (.env):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi koneksi database MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sambungasa
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migrasi & Seeder Data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Tautkan Storage:**
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Server Lokal:**
   ```bash
   php artisan serve --port=8000
   ```
   Buka `http://127.0.0.1:8000` di peramban.

---

## 👥 Akun Demo untuk Pengujian

Semua akun demo menggunakan kata sandi: `password`

| Peran (Role) | Email | Keterangan |
| :--- | :--- | :--- |
| **Administrator** | `admin@sambungasa.id` | Akses penuh dashboard admin, moderasi, dan laporan |
| **Sekolah 3T (Terverifikasi)** | `sdn.waisilip@sambungasa.sch.id` | SD Inpres Waisilip, Kabupaten Puncak, Papua |
| **Sekolah 3T (Pending)** | `smpn.krayan@sambungasa.sch.id` | SMPN 1 Krayan, Nunukan, Kalimantan Utara |
| **Donatur (Individu)** | `budi.donatur@example.org` | Memiliki komitmen bantuan aktif dan riwayat tracking |
| **Relawan (Volunteer)** | `siti.relawan@example.org` | Memiliki riwayat komitmen pelatihan guru relawan |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Jalankan test suite menggunakan PHPUnit:

```bash
php artisan test
```

**Hasil Pengujian:**
- Total Tes: **40 passed**
- Total Asersi: **125 assertions**
- Lingkup Uji:
  - `AuthTest`: Registrasi multi-peran, autentikasi, dan kontrol akses peran.
  - `NeedManagementTest`: Validasi verifikasi sekolah, pembuatan kebutuhan, moderasi admin, dan penutupan.
  - `PledgeFlowTest`: Pengajuan bantuan, kode tracking unik, pencegahan over-pledging, dan konfirmasi serah terima.
  - `SchoolVerificationTest`: Verifikasi sekolah 3T, penolakan, dan permintaan revisi.
  - `EvidenceSecurityTest`: Validasi berkas bukti privat, pembatasan ekstensi, dan perlindungan IDOR.
  - `StatisticsAndReportTest`: Agregasi metrik riil database, laporan admin, dan ekspor CSV aman.

---

## 📄 Lisensi & Tim Pengembang

Dikembangkan oleh **PPL Kelompok B2401** untuk mendukung pendidikan di kawasan 3T Indonesia.
Lisensi kode terbuka di bawah [MIT License](LICENSE).
