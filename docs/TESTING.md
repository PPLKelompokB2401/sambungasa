# DOKUMENTASI PENGUJIAN DAN QUALITY ASSURANCE (QA) — SAMBUNGASA

**Tanggal Pengujian:** 28 Oktober 2026  
**Lingkungan:** PHP 8.1.10, Laravel 10.50.4, MySQL 8.0 (Database: `sambungasa_test`)  
**Status Pengujian:** **39 PASSED, 0 FAILED (121 Assertions)**  

---

## 1. RINGKASAN HASIL TEST SUITE

Pengujian otomatis dijalankan menggunakan PHPUnit / Laravel Feature Testing pada basis data MySQL terisolasi (`sambungasa_test`) dengan transactional rollback untuk mencegah polusi data.

```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                            0.01s  

   PASS  Tests\Feature\AuthTest
  ✓ registration screen can be rendered                                                                          0.26s  
  ✓ login screen can be rendered                                                                                 0.02s  
  ✓ contributor can register successfully                                                                        0.07s  
  ✓ school can register and sets status pending                                                                  0.05s  
  ✓ user can login with correct credentials                                                                      0.05s  
  ✓ user cannot login with invalid password                                                                      0.23s  
  ✓ unauthenticated user cannot access dashboard                                                                 0.04s  
  ✓ contributor cannot access admin dashboard                                                                    0.03s  
  ✓ school cannot access admin user management                                                                   0.03s  

   PASS  Tests\Feature\EvidenceSecurityTest
  ✓ contributor can upload valid evidence file                                                                   0.08s  
  ✓ dangerous file extensions are rejected                                                                       0.05s  
  ✓ oversized file exceeding 5mb is rejected                                                                     0.05s  
  ✓ authorized users can download evidence                                                                       0.06s  
  ✓ unauthorized third party is forbidden from downloading evidence                                              0.09s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                                0.52s  

   PASS  Tests\Feature\NeedManagementTest
  ✓ unverified school cannot create need                                                                         0.04s  
  ✓ verified school can create need with pending status                                                          0.03s  
  ✓ admin can publish pending need                                                                               0.04s  
  ✓ admin can reject need with reason                                                                            0.05s  
  ✓ public needs directory only lists published needs                                                            0.04s  
  ✓ school cannot edit another schools need                                                                      0.04s  

   PASS  Tests\Feature\PledgeFlowTest
  ✓ contributor can submit pledge with unique tracking code                                                      0.05s  
  ✓ over pledging exceeding remaining need is prevented                                                          0.33s  
  ✓ admin can approve pledge                                                                                     0.07s  
  ✓ admin can reject pledge with reason                                                                          0.05s  
  ✓ school can confirm receipt of assistance                                                                     0.06s  
  ✓ admin marking completed updates need fulfilled quantity                                                      0.05s  
  ✓ illegal status transition is blocked                                                                         0.03s  

   PASS  Tests\Feature\SchoolVerificationTest
  ✓ admin can view school verification list                                                                      0.05s  
  ✓ admin can view school detail for verification                                                                0.05s  
  ✓ admin can verify 3t school                                                                                   0.04s  
  ✓ admin can reject school with reason                                                                          0.05s  
  ✓ admin can request revision from school                                                                       0.04s  
  ✓ non admin cannot verify schools                                                                              0.04s  

   PASS  Tests\Feature\StatisticsAndReportTest
  ✓ public statistics calculates real database aggregations                                                      0.06s  
  ✓ admin reports page is protected and filterable                                                               0.10s  
  ✓ csv export generates file with formula injection sanitization                                                0.09s  
  ✓ admin can view activity logs                                                                                 0.06s  

  Tests:    39 passed (121 assertions)
  Duration: 3.35s
```

---

## 2. RINCIAN PENGUJIAN PER MODUL

### A. Autentikasi dan Manajemen Hak Akses (`AuthTest.php`)
- **F-02 Registrasi Akun:**
  - Registrasi Donatur (`contributor`) membuat relasi `contributor_profiles` dengan status aktif.
  - Registrasi Perwakilan Sekolah (`school`) mencatat profil awal `school_profiles` dengan status awal `pending` (memerlukan review admin).
- **F-03 Autentikasi & Sesi:**
  - Login berhasil dengan hash bcrypt yang valid dan regenerasi sesi.
  - Penolakan login kredensial salah dengan pesan kesalahan yang tepat dan pencatatan throttle rate limiter.
  - Logout memvalidasi dan menghancurkan sesi aktif.
- **Role-Based Access Control (RBAC):**
  - Tamu (`guest`) dialihkan ke form login jika mengakses halaman berproteksi.
  - Donatur dilarang mengakses `/admin/dashboard` (HTTP 403 Forbidden).
  - Sekolah dilarang mengakses halaman kelola pengguna admin (HTTP 403 Forbidden).

### B. Profil Sekolah & Verifikasi 3T (`SchoolVerificationTest.php`)
- **F-01 & F-04 Alur Verifikasi Sekolah:**
  - Admin dapat memfilter dan melihat antrean verifikasi sekolah 3T.
  - Verifikasi disetujui: kolom `verification_status` berubah menjadi `verified`, pencatatan `verified_by`, `verified_at`, pembuatan audit log (`verify_school_3t`), dan penerbitan notifikasi ke akun sekolah.
  - Verifikasi ditolak: admin wajib menyertakan alasan penolakan, status diubah menjadi `rejected`, notifikasi diterbitkan.
  - Permintaan revisi: admin dapat meminta perbaikan data kepada sekolah dengan status `needs_revision`.
  - Keamanan: Akun non-admin dilarang memicu endpoint verifikasi (HTTP 403).

### C. Manajemen Kebutuhan Sekolah & Moderasi (`NeedManagementTest.php`)
- **F-05 & F-07 Pembatasan Sekolah Belum Terverifikasi:**
  - Sekolah dengan status belum terverifikasi ditolak saat mencoba membuat kebutuhan (HTTP 403).
- **F-06 Moderasi Administrator:**
  - Kebutuhan baru yang diajukan sekolah berstatus `pending_review`.
  - Admin menyetujui kebutuhan: status menjadi `published`, kebutuhan diterbitkan ke direktori publik, notifikasi dikirimkan ke sekolah.
  - Admin menolak kebutuhan: status menjadi `rejected` dengan catatan alasan penolakan.
- **F-08 Direktori Publik:**
  - Direktori publik `/needs` secara ketat hanya menampilkan kebutuhan yang berstatus `published`. Kebutuhan berstatus `pending_review` atau `draft` tidak bocor ke publik.
- **Isolasi Data Sekolah:**
  - Sekolah tidak dapat mengubah atau menghapus data kebutuhan milik sekolah lain (HTTP 403).

### D. Komitmen Bantuan (Pledge) & Pelacakan (`PledgeFlowTest.php`)
- **F-10 Pengajuan Komitmen Bantuan:**
  - Donatur mengajukan komitmen dengan kuantitas dan catatan rencana pengiriman.
  - Sistem menghasilkan **Kode Tracking Unik** dengan format `SA-YYYYMM-XXXX`.
  - Pencatatan log pelacakan awal (`tracking_logs`) dan notifikasi ke admin.
- **Pencegahan Over-Pledging (Integritas Konkurensi):**
  - Menggunakan transaksi basis data (`DB::transaction`) dan *pessimistic locking* (`lockForUpdate`).
  - Pengajuan komitmen yang melebihi sisa kebutuhan ditolak.
- **F-11 Moderasi Bantuan oleh Admin:**
  - Admin menyetujui komitmen (`verified`) atau menolak dengan alasan (`rejected`).
- **F-14 Konfirmasi Penerimaan oleh Sekolah:**
  - Sekolah penerima mengonfirmasi penerimaan fisik barang dengan mengisi nama penerima/penanggung jawab dan catatan kondisi barang. Status beralih menjadi `received`.
- **F-15 Transisi Status Selesai & Pemenuhan Kebutuhan:**
  - Ketika admin memverifikasi penyelesaian bantuan (`completed`), kuantitas kebutuhan terpenuhi (`quantity_fulfilled`) diperbarui secara otomatis.
  - Jika seluruh kuantitas terpenuhi 100%, status kebutuhan beralih menjadi `fulfilled`.
- **Validasi State Machine:**
  - Transisi status ilegal (contoh: langsung melompat dari `submitted` ke `completed`) ditolak oleh sistem.

### E. Keamanan Berkas Bukti & Penyimpanan Privat (`EvidenceSecurityTest.php`)
- **F-13 Bukti Penyaluran:**
  - Unggah berkas valid (PDF/PNG/JPG di bawah 5MB) disimpan di direktori privat `storage/app/private/evidence/` (tidak dapat diakses melalui URL publik langsung).
  - Berkas berbahaya (contoh: file `.php`) ditolak oleh validasi server.
  - Berkas melebihi 5MB ditolak.
- **Otorisasi Unduh (RBAC):**
  - Hanya donatur pemilik pledge, sekolah penerima, atau admin yang diizinkan mengunduh berkas.
  - Pengguna pihak ketiga yang tidak berhak menerima respon HTTP 403 Forbidden.

### F. Statistik & Laporan Transparansi (`StatisticsAndReportTest.php`)
- **F-16 Statistik Publik Nyata:**
  - Halaman `/statistics` menghitung metrik agregat langsung dari basis data (tanpa angka statis/palsu).
- **F-22 Laporan & Ekspor CSV:**
  - Filter multi-kriteria (status, jenis bantuan, kategori, provinsi, rentang tanggal).
  - Ekspor CSV dilengkapi pencegahan **CSV Formula Injection** (CWE-1236): karakter berbahaya pembuka (`=`, `+`, `-`, `@`) dinetralisir dengan awalan tanda petik tunggal (`'`).
- **F-24 Audit Trail & Log Aktivitas:**
  - Log aktivitas mencatat aksi, pengguna, alamat IP, dan waktu.
  - Akses log dibatasi khusus untuk Administrator.

---

## 3. PENGUJIAN VISUAL & UI/UX (SMOKE TEST BROWSER)

Pengujian antarmuka pengguna dilakukan secara otomatis menggunakan agen browser terotomasi dan direkam:
- **Hero & Branding:** Implementasi palet *Red Broadcast* (`#FF0000`, Roboto Font, badge 3T).
- **Responsivitas:** Uji rendering pada resolusi desktop dan mobile viewport.
- **Komponen Form & State:** Filter pill chip, alert notifikasi, modal dialog, formulir input dengan penanda field wajib.
- **Bukti Rekaman:** `sambungasa_smoke_test_1793152409466.webp` tersimpan di direktori artefak.

---

## 4. CARA MENJALANKAN PENGUJIAN

Untuk menjalankan seluruh test suite otomatis:

```bash
# Menggunakan binary PHP 8.1
& "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" artisan test
```

Untuk menjalankan test suite tertentu:

```bash
& "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" artisan test --filter=PledgeFlowTest
& "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" artisan test --filter=EvidenceSecurityTest
```
