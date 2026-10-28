# SambungAsa — Product Requirements Document (PRD)

> **Dokumen kebutuhan produk** untuk pengembangan website SambungAsa.  
> Versi: 1.0  
> Tanggal: 28 Oktober 2026  
> Status: Draft acuan development  
> Teknologi yang ditetapkan: Laravel, PHP, MySQL  
> Nama database: `sambungasa`

---

## 1. Ringkasan Produk

### 1.1 Nama Produk

**SambungAsa** — platform web untuk menghubungkan kebutuhan sekolah di wilayah tertinggal, terdepan, dan terluar (3T) dengan donatur, relawan, dan organisasi yang ingin memberikan bantuan.

### 1.2 Latar Belakang

Kesenjangan akses terhadap fasilitas dan dukungan pendidikan masih menjadi tantangan bagi sebagian sekolah, khususnya di wilayah 3T. Informasi kebutuhan sekolah dapat tersebar melalui jalur informal sehingga sulit ditemukan, dibandingkan, diverifikasi, dan dipantau pemenuhannya.

SambungAsa diusulkan sebagai kanal digital terstruktur agar sekolah dapat mencatat kebutuhan dengan kategori, jumlah, prioritas, dan deskripsi yang jelas. Donatur, relawan, dan organisasi dapat menemukan kebutuhan yang relevan serta mengajukan komitmen bantuan. Proses verifikasi, pelacakan status, dan dokumentasi bukti penyaluran mendukung transparansi dan akuntabilitas.

SambungAsa mendukung tujuan **Sustainable Development Goals (SDGs) 10 — Berkurangnya Kesenjangan**, terutama melalui peningkatan visibilitas kebutuhan sekolah dan peluang pemenuhan bantuan pendidikan di wilayah 3T.

### 1.3 Visi Produk

Menyediakan platform yang memudahkan kebutuhan sekolah di wilayah 3T ditemukan, diverifikasi, ditindaklanjuti, dan didokumentasikan secara transparan.

### 1.4 Tujuan Produk

1. Menyediakan kanal terstruktur bagi perwakilan sekolah 3T untuk mencatat dan mengelola kebutuhan.
2. Menghubungkan sekolah dengan donatur, relawan, dan organisasi melalui pengajuan bantuan yang dapat diverifikasi.
3. Menyediakan pelacakan status dan dokumentasi bukti penyaluran untuk mendukung akuntabilitas.
4. Menyajikan statistik mengenai kebutuhan yang terdaftar dan progres pemenuhannya sebagai informasi pendukung.
5. Membantu admin memverifikasi akun, lokasi sekolah, kebutuhan, dan pengajuan bantuan.

### 1.5 Indikator Keberhasilan

Indikator berikut merupakan sasaran evaluasi produk; target angka dapat ditentukan bersama tim setelah pengujian awal.

- Sekolah yang telah diverifikasi dapat membuat dan mengelola kebutuhan.
- Pengunjung dapat menemukan kebutuhan berdasarkan kata kunci, wilayah, kategori, dan prioritas.
- Pengajuan bantuan memiliki status yang jelas dan riwayat perubahan yang dapat ditelusuri.
- Bukti penyaluran tersimpan dan terkait dengan pengajuan bantuan yang tepat.
- Dashboard menampilkan data kebutuhan dan pemenuhan berdasarkan data aktual.
- Hak akses tiap peran diterapkan dan diuji.
- Tampilan dapat digunakan pada desktop, tablet, dan ponsel.
- Tidak ada fitur PWA/offline dalam implementasi.

---

## 2. Target Pengguna dan Aktor

Sistem memiliki tiga kategori aktor utama sesuai proposal.

### 2.1 Perwakilan Sekolah 3T

**Kebutuhan utama:** menyampaikan kebutuhan sekolah dengan jelas dan memantau bantuan yang diajukan untuk sekolahnya.

Hak akses utama:
- Mendaftar dan melengkapi data akun.
- Mengelola profil sekolah dan lokasi.
- Mengajukan perubahan data untuk proses verifikasi.
- Membuat, melihat, mengubah, dan menghapus kebutuhan milik sekolahnya sesuai status dan aturan sistem.
- Melihat pengajuan bantuan yang terkait dengan kebutuhan sekolahnya.
- Melihat status dan riwayat penyaluran bantuan.
- Mengonfirmasi penerimaan bantuan jika alur final menetapkan konfirmasi dari sekolah.

### 2.2 Donatur / Relawan / Organisasi

**Kebutuhan utama:** menemukan kebutuhan yang sesuai dengan kapasitasnya dan mengajukan bantuan yang dapat dilacak.

Hak akses utama:
- Mendaftar dan melengkapi profil serta informasi identitas/kontak.
- Melihat direktori dan detail kebutuhan publik.
- Mencari dan memfilter kebutuhan.
- Mengajukan komitmen bantuan (*pledge*).
- Melihat status pengajuan miliknya.
- Mengunggah bukti penyaluran pada tahapan yang diizinkan.
- Melihat notifikasi dan riwayat kontribusi miliknya.

> Proposal menyebut kategori gabungan Donatur/Relawan/Organisasi. Detail field profil dapat berbeda berdasarkan jenis kontribusi, tetapi perbedaan hak akses harus ditetapkan melalui role/permission yang eksplisit.

### 2.3 Admin

**Kebutuhan utama:** menjaga validitas data dan memastikan proses bantuan berjalan sesuai aturan.

Hak akses utama:
- Memverifikasi data diri dan akun.
- Memverifikasi profil serta lokasi sekolah terhadap referensi wilayah 3T.
- Mengelola data referensi wilayah dan kategori kebutuhan.
- Memoderasi kebutuhan sebelum dipublikasikan.
- Memverifikasi atau menolak pengajuan bantuan.
- Memantau dan mengelola status bantuan sesuai alur.
- Mengelola pengguna sesuai kewenangan.
- Melihat dashboard dan menghasilkan laporan.
- Melihat log aktivitas untuk kebutuhan audit.

---

## 3. Ruang Lingkup Produk

### 3.1 Termasuk dalam MVP

MVP mencakup fitur fungsional F-01 sampai F-22 dan F-24 yang dijelaskan pada bagian 5. Fitur F-23 mengenai mode offline/PWA dikeluarkan dari scope berdasarkan keputusan implementasi terbaru.

Cakupan utama:
- Registrasi, autentikasi, dan manajemen profil.
- Verifikasi akun dan sekolah/lokasi 3T.
- Pengelolaan kebutuhan sekolah.
- Kategori dan prioritas kebutuhan.
- Direktori, pencarian, filter, dan detail kebutuhan.
- Pengajuan pledge dan verifikasi admin.
- Tracking status, bukti penyaluran, dan notifikasi.
- Statistik publik/dashboard dan laporan.
- Manajemen pengguna, wilayah, dan aktivitas admin.

### 3.2 Tidak Termasuk dalam MVP

Fitur berikut tidak termasuk dalam scope saat ini, kecuali disetujui sebagai perubahan kebutuhan:
- **PWA dan akses offline**, termasuk Service Worker, manifest PWA, IndexedDB, offline cache, dan sinkronisasi offline.
- Transaksi pembayaran atau transfer uang di dalam platform.
- Marketplace jual-beli.
- Learning Management System (LMS), kelas virtual, materi pembelajaran, atau komunikasi guru-siswa.
- Manajemen armada/logistik atau jasa pengiriman terintegrasi.
- Pencocokan donatur otomatis berbasis AI.
- Aplikasi native Android/iOS terpisah.
- Fitur lain di luar daftar kebutuhan MVP yang telah disepakati.

### 3.3 Cakupan Wilayah

Proposal menetapkan fokus awal wilayah 3T di **Papua, Kalimantan, dan Sulawesi**, sesuai daftar wilayah yang digunakan dalam proyek. Data wilayah harus dapat diperluas tanpa perubahan besar pada struktur aplikasi.

Daftar wilayah dan status 3T harus dikelola berdasarkan referensi yang disepakati tim. Jangan menganggap semua kabupaten/provinsi di pulau tersebut otomatis berstatus 3T; validasi dilakukan terhadap data referensi yang benar.

### 3.4 Prinsip Produk

- **Terstruktur:** kebutuhan memiliki kategori, prioritas, jumlah/unit bila relevan, dan deskripsi.
- **Terverifikasi:** akun, sekolah/lokasi, kebutuhan, dan pledge melewati pemeriksaan sesuai workflow.
- **Transparan:** status dan riwayat bantuan ditampilkan dengan jelas kepada pihak yang berhak melihatnya.
- **Akuntabel:** perubahan penting dan bukti penyaluran dicatat.
- **Responsif:** antarmuka dapat digunakan pada desktop dan perangkat mobile.
- **Aman:** data pribadi dan bukti tidak boleh terbuka tanpa otorisasi.
- **Bukan marketplace:** sistem mengelola komitmen dan dokumentasi bantuan; penyaluran dilakukan di luar platform.

---

## 4. Masalah Pengguna dan Solusi

| Masalah | Solusi SambungAsa | Hasil yang diharapkan |
|---|---|---|
| Informasi kebutuhan sekolah tersebar dan sulit ditemukan | Direktori kebutuhan sekolah dengan kategori, prioritas, wilayah, dan pencarian | Kebutuhan lebih mudah ditemukan |
| Informasi sekolah/lokasi belum tentu valid | Verifikasi akun, profil sekolah, dan lokasi 3T oleh admin | Data lebih dapat dipercaya |
| Sulit mengetahui apakah bantuan sudah ditindaklanjuti | Status pledge dan tracking log | Proses bantuan lebih mudah dipantau |
| Bukti penyaluran tidak terdokumentasi di satu tempat | Upload bukti yang terkait dengan pledge dan riwayat status | Dokumentasi lebih tertata |
| Sulit melihat kebutuhan yang belum terpenuhi | Dashboard statistik kebutuhan dan progres pemenuhan | Data pendukung untuk evaluasi |
| Pengelola sulit menelusuri perubahan | Log aktivitas untuk tindakan penting | Audit aktivitas lebih mudah |

---

## 5. Kebutuhan Fungsional

ID fitur dipertahankan untuk memudahkan penelusuran terhadap proposal. **F-23 sengaja tidak digunakan** karena fitur PWA/offline dikeluarkan dari scope.

| ID | Fitur | Aktor | Persyaratan produk |
|---|---|---|---|
| F-01 | Manajemen Profil Sekolah | Perwakilan Sekolah | Pengguna sekolah dapat mengisi dan memperbarui nama sekolah, alamat, provinsi, kabupaten, serta koordinat/lokasi yang diperlukan. |
| F-02 | Manajemen Profil Donatur/Relawan/Organisasi | Donatur/Relawan/Organisasi | Pengguna dapat melengkapi dan memperbarui profil, data identitas, serta informasi kontak yang diperlukan untuk verifikasi. |
| F-03 | Verifikasi Data Diri | Admin | Admin dapat meninjau, menyetujui, atau menolak data identitas pengguna sesuai jenis akun. Penolakan harus disertai alasan/catatan. |
| F-04 | Verifikasi Lokasi 3T | Admin | Admin dapat memeriksa lokasi sekolah terhadap data referensi wilayah 3T, lalu menyetujui atau menolak dengan catatan. |
| F-05 | CRUD Kebutuhan | Perwakilan Sekolah | Sekolah terverifikasi dapat membuat, membaca, mengubah, dan menghapus kebutuhan milik sekolahnya sesuai aturan status dan relasi pledge aktif. |
| F-06 | Kategorisasi Kebutuhan | Perwakilan Sekolah, Admin | Kebutuhan dikategorikan, minimal: Buku/Alat Tulis, Elektronik/Komputer, Infrastruktur, Pelatihan, dan Lainnya. Admin dapat mengelola kategori sesuai kebutuhan. |
| F-07 | Prioritas Kebutuhan | Perwakilan Sekolah | Setiap kebutuhan memiliki prioritas Rendah, Sedang, Tinggi, atau Mendesak. |
| F-08 | Pencarian dan Filter Kebutuhan | Publik, Donatur/Relawan/Organisasi | Pengguna dapat mencari kebutuhan menggunakan kata kunci serta filter wilayah, kategori, dan prioritas. |
| F-09 | Halaman Detail Kebutuhan | Publik | Halaman menampilkan informasi kebutuhan, profil sekolah yang layak dipublikasikan, lokasi umum, status pemenuhan, progres, dan timeline yang aman untuk publik. |
| F-10 | Pengajuan Bantuan (Pledge) | Donatur/Relawan/Organisasi | Pengguna terverifikasi dapat mengajukan komitmen bantuan dengan mengisi kebutuhan yang dituju, jumlah/kapasitas bila relevan, catatan, dan estimasi penyaluran. |
| F-11 | Verifikasi Pengajuan Bantuan | Admin | Admin dapat menyetujui atau menolak pledge dengan mempertimbangkan validitas pengaju dan kesesuaian bantuan. Penolakan disertai alasan. |
| F-12 | Manajemen Status Bantuan | Admin; pihak terkait sesuai hak akses | Sistem mengelola transisi status bantuan secara terurut dan mencatat setiap perubahan. Status utama: Diajukan → Diverifikasi → Dalam Pengiriman → Diterima → Selesai. Jalur ditolak/dibatalkan perlu ditangani secara eksplisit. |
| F-13 | Upload Bukti Penyaluran | Donatur/Relawan/Organisasi | Pengguna yang berwenang dapat mengunggah foto atau dokumen bukti sesuai tahap penyaluran. File divalidasi dan tidak boleh dapat diakses publik tanpa otorisasi. |
| F-14 | Tracking Bantuan | Sekolah, Donatur/Relawan/Organisasi, Admin | Pihak terkait dapat melihat status terkini, waktu perubahan, catatan, dan riwayat pengajuan bantuan sesuai hak akses. |
| F-15 | Notifikasi Internal | Pengguna terkait, Admin | Sistem menampilkan notifikasi ketika terjadi peristiwa penting, misalnya hasil verifikasi akun, hasil moderasi kebutuhan, perubahan status pledge, atau bukti/konfirmasi yang diperlukan. |
| F-16 | Kode Tracking | Pengguna terkait, Admin | Setiap pledge memiliki identitas/kode tracking unik agar mudah ditelusuri. Kode tidak boleh menggantikan pemeriksaan hak akses. |
| F-17 | Dashboard Statistik | Publik dan/atau Admin sesuai jenis data | Sistem menampilkan statistik kebutuhan terdaftar, kebutuhan terpenuhi, progres pemenuhan, dan ringkasan kesenjangan berdasarkan data aktual. Statistik publik hanya memakai data yang aman dipublikasikan. |
| F-18 | Manajemen Referensi Wilayah 3T | Admin | Admin dapat mengelola atau memperbarui referensi wilayah yang dipakai dalam verifikasi sekolah, sesuai sumber data yang disetujui. |
| F-19 | Direktori Sekolah | Publik | Pengunjung dapat melihat daftar sekolah yang telah disetujui untuk tampil publik dan membuka profil publiknya. |
| F-20 | Manajemen Pengguna | Admin | Admin dapat melihat dan mengelola akun sesuai kewenangan, status verifikasi, dan role. Aksi penting harus tercatat. |
| F-21 | Manajemen Pledge | Admin | Admin dapat meninjau, mencari, memfilter, dan memantau pledge serta statusnya untuk mendukung operasional bantuan. |
| F-22 | Laporan dan Ekspor | Admin | Admin dapat melihat ringkasan laporan dan mengekspor data yang diizinkan, dengan filter periode/status/wilayah jika relevan. Data pribadi harus dibatasi sesuai kebutuhan. |
| F-23 | Mode Offline (PWA Cache) | — | **Dikeluarkan dari scope. Jangan diimplementasikan.** Tidak membuat PWA manifest, Service Worker, IndexedDB, offline cache, atau antrean sinkronisasi offline. |
| F-24 | Log Aktivitas | Admin | Sistem mencatat aktivitas penting, seperti registrasi, verifikasi, perubahan status, pengelolaan kebutuhan, dan tindakan administratif. Log hanya dapat dilihat oleh pihak berwenang. |

### 5.1 Aturan Umum Kebutuhan Fungsional

- Aksi tulis yang membutuhkan akun harus memerlukan autentikasi.
- Hak akses diperiksa di backend; menyembunyikan tombol di UI saja tidak cukup.
- Sekolah hanya boleh mengelola profil dan kebutuhan yang dimilikinya.
- Donatur/relawan hanya boleh mengelola profil, pledge, dan bukti miliknya, kecuali admin dengan kewenangan yang sesuai.
- Kebutuhan yang menunggu moderasi tidak boleh tampil sebagai kebutuhan publik.
- Pengajuan pledge tidak langsung dihitung sebagai bantuan terpenuhi.
- Perubahan status harus melalui transisi yang sah dan menyimpan waktu, pelaku, serta catatan yang diperlukan.
- Perubahan atau penghapusan kebutuhan yang memiliki pledge aktif harus dibatasi agar riwayat tidak rusak.
- Data statistik harus dihitung dari sumber data aktual, bukan angka hard-coded.
- File bukti dan data identitas tidak boleh dipublikasikan tanpa dasar dan izin yang sesuai.

---

## 6. Kebutuhan Non-Fungsional

| ID | Aspek | Persyaratan |
|---|---|---|
| NF-01 | Performa koneksi terbatas | Halaman ditargetkan dapat dimuat dalam kurang dari 5 detik pada koneksi 3G dalam kondisi pengujian yang ditetapkan. Gunakan gambar terkompresi, aset ringan, pagination, dan lazy loading jika relevan. Target harus divalidasi lewat pengujian. |
| NF-02 | PWA/offline | Tidak diterapkan. Aplikasi membutuhkan koneksi internet untuk mengambil dan menyimpan data. Optimasi performa koneksi rendah tetap dilakukan tanpa membangun fitur offline. |
| NF-03 | Keamanan | Password di-hash menggunakan mekanisme aman Laravel; gunakan HTTPS di production; validasi input; proteksi terhadap SQL injection, XSS, CSRF, dan akses tanpa izin. |
| NF-04 | Keandalan | Kesalahan aplikasi harus ditangani dengan pesan yang jelas. Operasi penting dan perubahan status harus menjaga konsistensi data. Target uptime 99% dapat menjadi sasaran deployment jika infrastruktur mendukung, bukan jaminan sebelum diukur. |
| NF-05 | Skalabilitas | Data wilayah, kategori, dan kebutuhan harus dapat ditambah tanpa perubahan besar pada kode. |
| NF-06 | Kompatibilitas browser | Mendukung versi terkini browser modern utama, minimal Chrome, Firefox, Safari, dan Edge sesuai hasil pengujian. |
| NF-07 | Responsif | UI dapat digunakan pada desktop, tablet, dan ponsel; teks, form, tabel, navigasi, dan kontrol utama tetap terbaca serta dapat dioperasikan. |
| NF-08 | Privasi dan kepatuhan | Pengumpulan dan pemrosesan data pribadi mengikuti prinsip minimisasi data dan ketentuan perlindungan data pribadi yang berlaku. |
| NF-09 | Usability | Gunakan Bahasa Indonesia, label yang jelas, validasi form yang mudah dipahami, serta alur yang sederhana bagi pengguna dengan literasi digital beragam. |
| NF-10 | Maintainability | Struktur Laravel modular, konvensi penamaan konsisten, perubahan schema melalui migration, serta kode dan fitur penting memiliki dokumentasi dan pengujian yang relevan. |

---

## 7. Alur Pengguna Utama

### 7.1 Registrasi dan Verifikasi Akun

1. Pengunjung memilih jenis akun: sekolah atau donatur/relawan/organisasi.
2. Pengguna mengisi informasi registrasi yang diwajibkan.
3. Sistem memvalidasi input dan memastikan email/username unik sesuai rancangan autentikasi.
4. Sistem menyimpan password dalam bentuk hash dan membuat akun dengan status menunggu verifikasi jika verifikasi admin diwajibkan.
5. Admin memeriksa data pengguna.
6. Admin menyetujui atau menolak pendaftaran; penolakan disertai alasan.
7. Sistem memberi notifikasi kepada pengguna dan mencatat aktivitas.
8. Pengguna aktif dapat masuk dan mengakses fitur sesuai role.

**Alur alternatif:** jika email/username sudah digunakan atau input tidak valid, sistem menampilkan pesan dan meminta perbaikan. Jika verifikasi ditolak, pengguna melihat alasan dan dapat memperbarui data sesuai kebijakan.

### 7.2 Profil Sekolah dan Verifikasi Lokasi 3T

1. Perwakilan sekolah yang memiliki akun aktif melengkapi profil sekolah.
2. Sistem menyimpan profil sebagai belum terverifikasi.
3. Admin membandingkan lokasi sekolah dengan referensi wilayah 3T yang disepakati.
4. Admin menyetujui atau menolak profil dengan catatan.
5. Sistem mencatat hasil verifikasi dan memberi notifikasi.
6. Sekolah yang memenuhi syarat dapat mengajukan kebutuhan.

### 7.3 Pengajuan dan Publikasi Kebutuhan

1. Sekolah terverifikasi membuat kebutuhan dengan judul, deskripsi, kategori, prioritas, jumlah/unit bila relevan, dan informasi pendukung.
2. Sistem memvalidasi data dan menyimpan kebutuhan pada status awal yang ditetapkan.
3. Admin memeriksa kebutuhan.
4. Jika disetujui, kebutuhan dipublikasikan dan muncul pada direktori/pencarian.
5. Jika ditolak, kebutuhan tetap tidak tampil publik dan sekolah menerima catatan untuk revisi.
6. Sekolah dapat mengubah atau menutup kebutuhan sesuai aturan status dan pledge aktif.

### 7.4 Pencarian dan Pengajuan Bantuan

1. Pengunjung melihat direktori kebutuhan.
2. Pengguna mencari atau memfilter berdasarkan kata kunci, wilayah, kategori, atau prioritas.
3. Pengguna membuka detail kebutuhan untuk memahami kebutuhan dan statusnya.
4. Donatur/relawan/organisasi masuk ke akun yang memenuhi syarat.
5. Pengguna mengisi pengajuan bantuan.
6. Sistem memvalidasi bahwa kebutuhan masih menerima bantuan dan jumlah/kapasitas yang diajukan sesuai aturan.
7. Sistem membuat pledge dengan status `submitted` dan kode tracking unik.
8. Admin menerima pengajuan untuk ditinjau.

### 7.5 Verifikasi Pledge

1. Admin meninjau identitas pengaju dan kesesuaian bantuan.
2. Admin menyetujui atau menolak pledge.
3. Sistem menyimpan pelaku, waktu, status, dan catatan keputusan.
4. Sistem memberi notifikasi kepada pengaju dan pihak sekolah yang terkait.
5. Pledge yang disetujui dapat berlanjut ke proses penyaluran.

### 7.6 Tracking dan Penyelesaian Bantuan

1. Pledge yang disetujui masuk ke tahapan penyaluran.
2. Pihak yang berwenang memperbarui status sesuai transisi yang sah.
3. Pengguna mengunggah bukti pada tahapan yang ditentukan.
4. Sistem menyimpan metadata file, catatan, pelaku, waktu, dan status terkait.
5. Pihak sekolah atau admin melakukan konfirmasi penerimaan sesuai workflow final.
6. Pledge ditandai selesai setelah persyaratan penyelesaian terpenuhi.
7. Dashboard dan statistik diperbarui dari data terbaru.

### 7.7 Pelaporan dan Audit

1. Admin membuka dashboard atau halaman laporan.
2. Admin memilih filter yang tersedia, seperti periode, status, atau wilayah.
3. Sistem menyajikan data berdasarkan record aktual.
4. Admin mengekspor data yang diizinkan.
5. Aktivitas ekspor dan tindakan administratif penting dicatat jika sesuai kebijakan audit.

---

## 8. Status dan Aturan Bisnis

### 8.1 Status Akun

Status minimum yang disarankan:
- `pending` — menunggu pemeriksaan.
- `active` — disetujui dan dapat menggunakan fitur sesuai role.
- `rejected` — ditolak dengan catatan.
- `suspended` — dinonaktifkan sementara oleh admin bila diperlukan.

Nama status dapat disesuaikan dengan implementasi, tetapi makna dan transisinya harus konsisten.

### 8.2 Status Verifikasi Sekolah

- `pending` — menunggu pemeriksaan lokasi/data.
- `verified` — lolos verifikasi.
- `rejected` — ditolak dengan alasan.
- `needs_revision` — perlu perbaikan data, jika workflow ini dipakai.

Sekolah hanya boleh memublikasikan kebutuhan setelah akun dan profil sekolah memenuhi syarat verifikasi yang telah ditetapkan.

### 8.3 Status Kebutuhan

Status minimum yang disarankan:
- `draft` — belum diajukan untuk moderasi.
- `pending_review` — menunggu moderasi.
- `published` — dapat dilihat publik dan menerima pledge jika masih terbuka.
- `rejected` — ditolak dengan catatan.
- `fulfilled` — kebutuhan terpenuhi sesuai aturan perhitungan.
- `closed` — ditutup oleh pihak berwenang.
- `archived` — disimpan untuk riwayat dan tidak lagi aktif.

Daftar final status kebutuhan perlu diselaraskan dengan arsitektur dan implementasi database.

### 8.4 Status Pledge

Alur utama:
`submitted` → `verified` → `in_delivery` → `received` → `completed`

Jalur alternatif:
- `rejected` — ditolak saat verifikasi atau proses yang sesuai.
- `cancelled` — dibatalkan sesuai kebijakan.

Aturan:
- Pledge tidak boleh melompati tahapan tanpa aturan yang eksplisit.
- Pledge yang `submitted`, `rejected`, atau `cancelled` tidak dihitung sebagai bantuan selesai.
- Setiap transisi status harus mencatat waktu, pelaku, dan catatan yang diperlukan.
- Setiap transisi yang relevan harus membuat tracking log dan notifikasi.
- Status `completed` hanya boleh diberikan setelah syarat penyelesaian terpenuhi.

### 8.5 Penghitungan Progres

Untuk kebutuhan berbasis kuantitas, rumus awal:

`progress = min(quantity_fulfilled / quantity_required, 1) × 100%`

Ketentuan:
- Hanya bantuan yang memenuhi definisi terpenuhi yang dihitung.
- Jumlah yang diajukan tetapi belum disalurkan tidak dianggap terpenuhi.
- Kebutuhan tanpa ukuran kuantitas harus menggunakan indikator status/kapasitas yang jelas, bukan persentase yang menyesatkan.
- Perubahan jumlah kebutuhan saat terdapat pledge aktif perlu dibatasi atau melalui workflow yang menjaga konsistensi.

---

## 9. Persyaratan Data dan Privasi

### 9.1 Prinsip Data

- Kumpulkan hanya data yang dibutuhkan untuk menjalankan fitur dan verifikasi.
- Pisahkan data publik dari data internal/verifikasi.
- Tampilkan lokasi sekolah secara proporsional dengan kebutuhan publikasi dan keamanan.
- Jangan menampilkan dokumen identitas atau bukti yang mengandung data pribadi secara publik secara default.
- Batasi akses terhadap data berdasarkan role dan kepemilikan record.
- Tentukan kebijakan retensi, penghapusan, dan koreksi data bersama pemilik produk.

### 9.2 Data Inti yang Dikelola

Secara konseptual, produk memerlukan data:
- Akun dan role pengguna.
- Profil sekolah.
- Profil donatur/relawan/organisasi.
- Referensi wilayah dan status 3T.
- Kategori kebutuhan.
- Kebutuhan sekolah.
- Pledge/pengajuan bantuan.
- Riwayat tracking/status.
- File bukti penyaluran.
- Notifikasi.
- Log aktivitas.

Rincian kolom, tipe data, foreign key, index, dan constraint ditetapkan dalam `DATABASE_SCHEMA.md` dan migration Laravel.

---

## 10. Kebutuhan Antarmuka dan Pengalaman Pengguna

Dokumen `design.md` menjadi acuan visual untuk warna, tipografi, layout, komponen, navigasi, dan perilaku responsif.

Halaman/area yang perlu tercakup dalam desain:
1. Landing page publik.
2. Direktori kebutuhan.
3. Detail kebutuhan.
4. Direktori/profil publik sekolah.
5. Registrasi dan login.
6. Status verifikasi dan halaman profil.
7. Dashboard sekolah.
8. Formulir kebutuhan dan daftar kebutuhan milik sekolah.
9. Dashboard donatur/relawan/organisasi.
10. Form pengajuan pledge dan daftar pledge.
11. Detail tracking dan upload bukti.
12. Dashboard admin.
13. Antrean verifikasi akun/sekolah/kebutuhan/pledge.
14. Manajemen pengguna, kategori, dan referensi wilayah.
15. Statistik, laporan, dan log aktivitas.
16. Halaman error, akses ditolak, data kosong, loading, dan validasi form.

Persyaratan UX:
- Gunakan Bahasa Indonesia.
- Status harus memiliki label teks, bukan hanya warna.
- Form menampilkan field wajib, aturan input, dan pesan error yang spesifik.
- Tindakan penting seperti menolak, menghapus, menutup kebutuhan, atau mengubah status harus memiliki konfirmasi yang sesuai.
- Tabel/list harus tetap dapat digunakan pada layar kecil.
- Aksesibilitas dasar: label form, fokus keyboard, kontras yang cukup, dan teks alternatif untuk gambar bermakna.
- Jangan merancang UI untuk mode offline/PWA.

---

## 11. Kriteria Penerimaan Produk

Produk dianggap memenuhi baseline MVP jika kriteria berikut terpenuhi.

### 11.1 Akun dan Hak Akses
- Pengguna dapat mendaftar dan masuk menggunakan mekanisme autentikasi yang ditetapkan.
- Email/username duplikat ditolak dengan pesan yang jelas.
- Password tidak disimpan dalam bentuk teks biasa.
- Halaman dan aksi terlindungi sesuai role.
- Pengguna tidak dapat mengakses atau mengubah data milik pengguna lain tanpa izin.

### 11.2 Verifikasi
- Admin dapat meninjau akun dan profil sekolah.
- Keputusan persetujuan/penolakan tercatat.
- Penolakan menyertakan alasan atau catatan.
- Sekolah yang belum memenuhi syarat tidak dapat menerbitkan kebutuhan.

### 11.3 Kebutuhan dan Direktori
- Sekolah yang memenuhi syarat dapat membuat dan mengelola kebutuhan.
- Kebutuhan yang belum disetujui tidak muncul di direktori publik.
- Pencarian dan filter menghasilkan data yang sesuai.
- Detail kebutuhan menampilkan informasi serta status yang akurat.
- Penghapusan/perubahan tidak merusak riwayat pledge aktif.

### 11.4 Pledge dan Tracking
- Pengguna yang memenuhi syarat dapat mengajukan pledge.
- Pledge memiliki kode tracking unik.
- Admin dapat memutuskan pledge.
- Sistem menolak transisi status yang tidak sah.
- Riwayat status, bukti, pelaku, dan waktu tersimpan.
- Status penyelesaian memperbarui statistik sesuai aturan bisnis.

### 11.5 Statistik dan Laporan
- Statistik dihitung dari database aktual.
- Filter laporan bekerja sesuai opsi yang disediakan.
- Ekspor hanya menyertakan kolom yang diizinkan.
- Data pribadi yang tidak diperlukan tidak disertakan dalam laporan publik.

### 11.6 Keamanan, Responsivitas, dan Dokumentasi
- Validasi dilakukan di server.
- File upload memvalidasi tipe, ukuran, dan hak akses.
- Halaman utama dapat digunakan di desktop dan mobile.
- Tidak terdapat implementasi PWA/offline.
- Fitur utama memiliki pengujian yang relevan.
- Setup project dan variabel environment terdokumentasi.

---

## 12. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data lokasi 3T tidak konsisten | Sekolah salah diverifikasi | Tetapkan sumber referensi wilayah, format kode, dan proses pembaruan data |
| Pledge melebihi sisa kebutuhan | Data pemenuhan tidak akurat | Validasi sisa kuantitas di backend dan gunakan transaksi database untuk operasi kritis |
| Status berubah tanpa bukti/catatan | Akuntabilitas berkurang | Gunakan state transition terpusat, tracking log, dan otorisasi |
| Bukti atau identitas terekspos | Risiko privasi | Simpan file sensitif di storage privat dan layani lewat route berotorisasi |
| Data statistik tidak sesuai kenyataan | Keputusan keliru | Definisikan rumus dan sumber data; uji perhitungan dengan data contoh |
| Koneksi pengguna lambat | Pengalaman penggunaan buruk | Optimasi ukuran aset, pagination, query, dan gambar; jangan mengandalkan fitur offline |
| Scope berkembang di luar kapasitas tim | Keterlambatan | Gunakan daftar MVP dan proses persetujuan perubahan |
| Dokumentasi berbeda dari kode | Tim bingung saat maintenance | Perbarui PRD, arsitektur, schema, dan changelog saat keputusan penting berubah |

---

## 13. Dependensi dan Keputusan yang Harus Ditetapkan

Sebelum atau selama implementasi, tim perlu menetapkan hal-hal berikut dan memperbarui dokumen terkait:

1. Sumber resmi/rujukan data wilayah 3T dan cakupan wilayah awal.
2. Apakah verifikasi admin wajib untuk semua akun atau hanya untuk aksi tertentu.
3. Field identitas dan dokumen apa yang benar-benar diperlukan untuk setiap tipe akun.
4. Apakah sekolah atau admin yang menjadi pihak final untuk mengonfirmasi penerimaan bantuan.
5. Aturan kuantitas, satuan, dan alokasi beberapa pledge terhadap satu kebutuhan.
6. Batas ukuran dan tipe file bukti yang diperbolehkan.
7. Channel notifikasi pada MVP. Baseline dokumen ini mengasumsikan notifikasi internal; email dapat ditambahkan jika disetujui dan dikonfigurasi.
8. Format ekspor laporan dan siapa yang berhak mengaksesnya.
9. Batas retensi data dan kebijakan penghapusan akun.
10. Target performa yang akan digunakan dalam pengujian dan kondisi uji yang konsisten.

Keputusan yang belum ditetapkan tidak boleh diubah diam-diam menjadi asumsi permanen. Catat keputusan final di dokumen arsitektur atau dokumentasi teknis terkait.

---

## 14. Keterkaitan dengan Dokumen Proyek

| Dokumen | Tanggung jawab |
|---|---|
| `PRD.md` | Tujuan produk, pengguna, scope, fitur, aturan bisnis tingkat produk, dan kriteria penerimaan |
| `ARCHITECTURE_SAMBUNGASA.md` | Arsitektur teknis, struktur Laravel, modul, rute, keamanan implementasi, dan roadmap |
| `design.md` | Visual UI/UX, design system, layout, komponen, dan responsivitas |
| `DATABASE_SCHEMA.md` | Tabel, kolom, tipe data, relasi, index, constraint, dan rancangan ERD |
| `USER_FLOWS.md` | Alur pengguna dan skenario alternatif per proses |
| `IMPLEMENTATION_PLAN.md` | Urutan fase/tugas, dependensi, dan definisi selesai per fase |
| `TESTING.md` | Strategi pengujian, skenario uji, dan hasil uji |
| `CHANGELOG.md` | Riwayat perubahan versi dan keputusan yang berdampak |

---

## 15. Definition of Done — MVP

Satu fitur dianggap selesai ketika:
- Persyaratan dan acceptance criteria terpenuhi.
- Implementasi mengikuti arsitektur dan standar coding yang disepakati.
- Validasi input dan otorisasi backend tersedia.
- Kondisi sukses, gagal, dan data kosong ditangani.
- Pengujian relevan dijalankan dan lulus.
- Tampilan sesuai `design.md` dan responsif.
- Tidak ada data sensitif yang terekspos.
- Dokumentasi terkait diperbarui jika ada perubahan alur, schema, atau aturan bisnis.

MVP SambungAsa dinyatakan siap untuk UAT/deployment setelah fitur yang disepakati selesai, alur utama lolos pengujian end-to-end, isu kritis ditangani, dan konfigurasi deployment aman.

---

## 16. Catatan Versi

### Versi 1.0 — 28 Oktober 2026
- Menyusun PRD berdasarkan proposal pengembangan SambungAsa dan rancangan arsitektur proyek.
- Menetapkan Laravel, PHP, dan MySQL dengan nama database `sambungasa`.
- Mempertahankan ID fitur untuk traceability terhadap proposal.
- Mengeluarkan F-23 (PWA/offline) dari scope sesuai keputusan implementasi terbaru.
- Menegaskan bahwa SambungAsa bukan marketplace dan tidak memproses pembayaran langsung.
