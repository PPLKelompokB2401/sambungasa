<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Region;
use App\Models\SchoolProfile;
use App\Models\ContributorProfile;
use App\Models\VolunteerProfile;
use App\Models\NeedCategory;
use App\Models\Need;
use App\Models\Pledge;
use App\Models\TrackingLog;
use App\Models\EvidenceFile;
use App\Models\Notification;
use App\Models\ActivityLog;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN USER
        $admin = User::create([
            'name' => 'Administrator SambungAsa',
            'email' => 'admin@sambungasa.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'system_init',
            'entity_type' => 'User',
            'entity_id' => $admin->id,
            'description' => 'Akun Administrator Utama SambungAsa dibuat.',
            'ip_address' => '127.0.0.1',
        ]);

        // Get regions
        $provPapua = Region::where('code', 'ID-PA')->first();
        $regPuncak = Region::where('code', 'ID-PA-PUN')->first();
        $provKaltara = Region::where('code', 'ID-KU')->first();
        $regNunukan = Region::where('code', 'ID-KU-NNK')->first();
        $provSulut = Region::where('code', 'ID-SA')->first();
        $regTalaud = Region::where('code', 'ID-SA-KTL')->first();
        $regDonggala = Region::where('code', 'ID-ST-DGL')->first();

        // 2. SCHOOL USER 1: SD Inpres Waisilip (Papua) - Verified
        $userSchool1 = User::create([
            'name' => 'Korneles Kogoya (Kepala Sekolah)',
            'email' => 'sdn.waisilip@sambungasa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'school',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $school1 = SchoolProfile::create([
            'user_id' => $userSchool1->id,
            'school_name' => 'SD Inpres Waisilip',
            'npsn' => '60302144',
            'address' => 'Kampung Waisilip, Distrik Ilaga',
            'province_id' => $provPapua?->id,
            'regency_id' => $regPuncak?->id,
            'district' => 'Ilaga',
            'postal_code' => '98965',
            'phone' => '081240123456',
            'principal_name' => 'Korneles Kogoya, S.Pd.',
            'student_count' => 120,
            'teacher_count' => 6,
            'latitude' => -3.7381200,
            'longitude' => 137.6482100,
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => 'Terverifikasi resmi berdasarkan data Dapodik dan koordinat wilayah 3T Puncak Papua.',
        ]);

        // 3. SCHOOL USER 2: SMP Negeri 2 Sebatik Tengah (Kalimantan Utara) - Verified
        $userSchool2 = User::create([
            'name' => 'Rahmadi, M.Pd.',
            'email' => 'smpn.sebatik@sambungasa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'school',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $school2 = SchoolProfile::create([
            'user_id' => $userSchool2->id,
            'school_name' => 'SMP Negeri 2 Sebatik Tengah',
            'npsn' => '30401822',
            'address' => 'Jl. Poros Perbatasan Patok 3, Desa Aji Kuning',
            'province_id' => $provKaltara?->id,
            'regency_id' => $regNunukan?->id,
            'district' => 'Sebatik Tengah',
            'postal_code' => '77482',
            'phone' => '085247891234',
            'principal_name' => 'Rahmadi, M.Pd.',
            'student_count' => 185,
            'teacher_count' => 12,
            'latitude' => 4.1524300,
            'longitude' => 117.6521900,
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => 'Terverifikasi sekolah perbatasan Indonesia-Malaysia di Pulau Sebatik.',
        ]);

        // 4. SCHOOL USER 3: SD Negeri Miangas (Sulawesi Utara) - Verified
        $userSchool3 = User::create([
            'name' => 'Maria Pontoh',
            'email' => 'sdn.miangas@sambungasa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'school',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $school3 = SchoolProfile::create([
            'user_id' => $userSchool3->id,
            'school_name' => 'SD Negeri Pulau Miangas',
            'npsn' => '40103981',
            'address' => 'Desa Miangas, Pulau Miangas Terluar',
            'province_id' => $provSulut?->id,
            'regency_id' => $regTalaud?->id,
            'district' => 'Khusus Miangas',
            'postal_code' => '95886',
            'phone' => '081343219876',
            'principal_name' => 'Maria Pontoh, S.Pd.',
            'student_count' => 74,
            'teacher_count' => 5,
            'latitude' => 5.5562100,
            'longitude' => 126.5824100,
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => 'Terverifikasi pulau terluar paling utara NKRI.',
        ]);

        // 5. SCHOOL USER 4: Belum Terverifikasi (Pending Review)
        $userSchool4 = User::create([
            'name' => 'Hendra Syamsul',
            'email' => 'sdn.donggala@sambungasa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'school',
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);

        SchoolProfile::create([
            'user_id' => $userSchool4->id,
            'school_name' => 'SD Kecil Dampelas',
            'npsn' => '40201944',
            'address' => 'Dusun 3 Pongger, Dampelas',
            'province_id' => $regDonggala?->parent_id,
            'regency_id' => $regDonggala?->id,
            'district' => 'Dampelas',
            'postal_code' => '94354',
            'phone' => '082198765432',
            'principal_name' => 'Hendra Syamsul, S.Pd.',
            'student_count' => 45,
            'teacher_count' => 3,
            'verification_status' => 'pending',
        ]);

        // 6. CONTRIBUTOR 1: Organisasi / Yayasan (Verified)
        $userOrg = User::create([
            'name' => 'Siti Nurhaliza (Yayasan Lentera Asa)',
            'email' => 'yayasan.lentera@sambungasa.id',
            'password' => Hash::make('password123'),
            'role' => 'contributor',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $contributor1 = ContributorProfile::create([
            'user_id' => $userOrg->id,
            'contributor_type' => 'organization',
            'organization_name' => 'Yayasan Lentera Asa Indonesia',
            'pic_name' => 'Siti Nurhaliza',
            'phone' => '081122334455',
            'address' => 'Jl. Senopati No. 45, Jakarta Selatan',
            'identity_number' => 'AHU-0012984.AH.01.04.2021',
            'website' => 'https://lenteraasa.org',
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => 'Legalitas yayasan SK Kemenkumham terkonfirmasi valid.',
        ]);

        // 7. CONTRIBUTOR 2: Relawan Pengajar Guru (Verified)
        $userVolunteer = User::create([
            'name' => 'Budi Santoso, M.Pd.',
            'email' => 'budi.santoso@sambungasa.id',
            'password' => Hash::make('password123'),
            'role' => 'contributor',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $contributor2 = ContributorProfile::create([
            'user_id' => $userVolunteer->id,
            'contributor_type' => 'volunteer',
            'phone' => '081399887766',
            'address' => 'Jl. Kaliurang Km 5, Sleman, DI Yogyakarta',
            'identity_number' => '3404011204900002',
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => 'Identitas KTP dan sertifikasi pendidik terverifikasi.',
        ]);

        VolunteerProfile::create([
            'contributor_profile_id' => $contributor2->id,
            'education' => 'S2 Pendidikan Matematika Universitas Negeri Yogyakarta',
            'expertise' => 'Guru Matematika, Pelatihan Literasi Numerasi Dasar, Manajemen Kelas 3T',
            'experience_years' => 7,
            'availability' => 'flexible',
            'bio' => 'Berpengalaman mengajar di wilayah pelosok dan berkomitmen mendampingi guru lokal menguasai metode ajar kreatif.',
        ]);

        // 8. CONTRIBUTOR 3: Donatur Individu (Active)
        $userDonor = User::create([
            'name' => 'dr. Amanda Wijaya',
            'email' => 'amanda.wijaya@sambungasa.id',
            'password' => Hash::make('password123'),
            'role' => 'contributor',
            'status' => 'active',
            'email_verified_at' => now(),
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $contributor3 = ContributorProfile::create([
            'user_id' => $userDonor->id,
            'contributor_type' => 'donor',
            'phone' => '081234567890',
            'address' => 'Jl. Dharmawangsa No. 12, Surabaya',
            'identity_number' => '3578016509920001',
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        // Categories
        $catBuku = NeedCategory::where('slug', 'buku-alat-tulis')->first();
        $catElektronik = NeedCategory::where('slug', 'elektronik-komputer')->first();
        $catInfra = NeedCategory::where('slug', 'infrastruktur-bangunan')->first();
        $catPelatihan = NeedCategory::where('slug', 'pelatihan-guru-relawan')->first();

        // 9. NEEDS
        // Need 1: Buku Cerita & Modul Belajar (SD Inpres Waisilip) - Published
        $need1 = Need::create([
            'school_profile_id' => $school1->id,
            'need_category_id' => $catBuku->id,
            'title' => 'Pengadaan 100 Buku Cerita Bergambar & Modul Literasi Awal',
            'description' => 'Siswa kelas 1-3 di Kampung Waisilip sangat membutuhkan buku bacaan bergambar dan modul belajar membaca yang menarik. Saat ini sekolah hanya memiliki 8 buku cerita usang yang dipakai bergantian untuk 60 murid.',
            'assistance_type' => 'material',
            'priority' => 'tinggi',
            'quantity_required' => 100,
            'quantity_fulfilled' => 40,
            'unit' => 'eksemplar',
            'status' => 'published',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(10),
            'published_at' => now()->subDays(10),
            'target_date' => now()->addDays(30),
        ]);

        // Need 2: Laptop & Genset Mini untuk ANBK (SMPN 2 Sebatik) - Published
        $need2 = Need::create([
            'school_profile_id' => $school2->id,
            'need_category_id' => $catElektronik->id,
            'title' => 'Bantuan 6 Unit Laptop Bekas Layak Pakai untuk Simulasi ANBK',
            'description' => 'Siswa perbatasan Sebatik harus menyeberang pulau untuk mengikuti simulasi Asesmen Nasional Berbasis Komputer (ANBK) karena ketiadaan laptop di sekolah. Kami membutuhkan 6 unit laptop dengan spesifikasi minimal Core i3.',
            'assistance_type' => 'material',
            'priority' => 'mendesak',
            'quantity_required' => 6,
            'quantity_fulfilled' => 0,
            'unit' => 'unit',
            'status' => 'published',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(5),
            'published_at' => now()->subDays(5),
            'target_date' => now()->addDays(20),
        ]);

        // Need 3: Relawan Guru Pelatihan Numerasi (SD Negeri Miangas) - Published
        $need3 = Need::create([
            'school_profile_id' => $school3->id,
            'need_category_id' => $catPelatihan->id,
            'title' => 'Pendampingan Guru Relawan untuk Penguatan Numerasi Kelas Dasar',
            'description' => 'Guru-guru di Pulau Miangas membutuhkan pelatihan metode ajar numerasi kontekstual berbasis alat peraga sederhana agar siswa kelas 1-4 antusias belajar berhitung.',
            'assistance_type' => 'non_material',
            'priority' => 'sedang',
            'quantity_required' => 2,
            'quantity_fulfilled' => 0,
            'unit' => 'orang relawan',
            'status' => 'published',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(3),
            'published_at' => now()->subDays(3),
            'target_date' => now()->addDays(45),
        ]);

        // Need 4: Renovasi Atap Ruang Kelas Bocor (SD Inpres Waisilip) - Pending Review
        Need::create([
            'school_profile_id' => $school1->id,
            'need_category_id' => $catInfra->id,
            'title' => 'Penggantian 30 Lembar Seng & Rangka Kayu Kelas 2',
            'description' => 'Saat hujan lebat di dataran tinggi Ilaga, ruang kelas 2 tidak dapat digunakan karena atap bocor parah. Kami membutuhkan bantuan material seng gelombang dan paku seng.',
            'assistance_type' => 'material',
            'priority' => 'mendesak',
            'quantity_required' => 30,
            'quantity_fulfilled' => 0,
            'unit' => 'lembar seng',
            'status' => 'pending_review',
        ]);

        // 10. PLEDGES
        // Pledge 1: Yayasan Lentera menyalurkan 40 buku ke Need 1 (Completed)
        $pledge1 = Pledge::create([
            'need_id' => $need1->id,
            'contributor_profile_id' => $contributor1->id,
            'tracking_code' => 'SA-202610-1001',
            'quantity' => 40,
            'notes' => 'Paket 40 buku ensiklopedia anak bergambar dan cerita rakyat nusantara.',
            'estimated_delivery_at' => now()->subDays(2),
            'status' => 'completed',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(8),
            'completed_at' => now()->subDays(1),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge1->id,
            'old_status' => null,
            'new_status' => 'submitted',
            'notes' => 'Pledge komitmen bantuan 40 buku diajukan oleh Yayasan Lentera Asa.',
            'created_by' => $userOrg->id,
            'created_at' => now()->subDays(9),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge1->id,
            'old_status' => 'submitted',
            'new_status' => 'verified',
            'notes' => 'Pledge disetujui admin untuk pengiriman logistik udara ke Timika lalu Ilaga.',
            'created_by' => $admin->id,
            'created_at' => now()->subDays(8),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge1->id,
            'old_status' => 'verified',
            'new_status' => 'in_delivery',
            'notes' => 'Paket buku telah diberangkatkan dari Jakarta via kargo udara Timika.',
            'location' => 'Bandara Mozes Kilangin, Timika',
            'created_by' => $userOrg->id,
            'created_at' => now()->subDays(5),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge1->id,
            'old_status' => 'in_delivery',
            'new_status' => 'received',
            'notes' => 'Paket buku telah diterima langsung oleh Bapak Korneles Kogoya di SD Inpres Waisilip.',
            'location' => 'Kampung Waisilip, Distrik Ilaga',
            'created_by' => $userSchool1->id,
            'created_at' => now()->subDays(2),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge1->id,
            'old_status' => 'received',
            'new_status' => 'completed',
            'notes' => 'Serah terima diverifikasi selesai dengan BAST dan foto dokumentasi lengkap.',
            'created_by' => $admin->id,
            'created_at' => now()->subDays(1),
        ]);

        EvidenceFile::create([
            'pledge_id' => $pledge1->id,
            'uploaded_by' => $userSchool1->id,
            'file_path' => 'evidence/sample_bast_waisilip.pdf',
            'original_name' => 'BAST_Penerimaan_Buku_SD_Waisilip.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 245120,
            'evidence_type' => 'handover_bast',
            'caption' => 'Berita Acara Serah Terima 40 Eksemplar Buku Cerita SD Inpres Waisilip.',
        ]);

        // Pledge 2: dr. Amanda mengajukan bantuan 20 buku ke Need 1 (in_delivery)
        $pledge2 = Pledge::create([
            'need_id' => $need1->id,
            'contributor_profile_id' => $contributor3->id,
            'tracking_code' => 'SA-202610-1002',
            'quantity' => 20,
            'notes' => 'Paket buku bacaan sains anak dan alat tulis pensil warna.',
            'estimated_delivery_at' => now()->addDays(5),
            'status' => 'in_delivery',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(2),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge2->id,
            'old_status' => null,
            'new_status' => 'submitted',
            'notes' => 'Pengajuan pledge 20 buku dari dr. Amanda Wijaya.',
            'created_by' => $userDonor->id,
            'created_at' => now()->subDays(3),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge2->id,
            'old_status' => 'submitted',
            'new_status' => 'verified',
            'notes' => 'Pledge disetujui admin.',
            'created_by' => $admin->id,
            'created_at' => now()->subDays(2),
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge2->id,
            'old_status' => 'verified',
            'new_status' => 'in_delivery',
            'notes' => 'Paket sedang transit di ekspedisi kargo Jayapura.',
            'location' => 'Sentani, Jayapura',
            'created_by' => $userDonor->id,
            'created_at' => now()->subDays(1),
        ]);

        // Pledge 3: Budi Santoso mengajukan komitmen 1 relawan pengajar ke Need 3 (Submitted)
        $pledge3 = Pledge::create([
            'need_id' => $need3->id,
            'contributor_profile_id' => $contributor2->id,
            'tracking_code' => 'SA-202610-1003',
            'quantity' => 1,
            'notes' => 'Siap mengadakan program bimbingan numerasi intensif 2 minggu di Miangas.',
            'estimated_delivery_at' => now()->addDays(30),
            'status' => 'submitted',
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge3->id,
            'old_status' => null,
            'new_status' => 'submitted',
            'notes' => 'Pengajuan relawan guru diajukan oleh Budi Santoso, M.Pd.',
            'created_by' => $userVolunteer->id,
            'created_at' => now(),
        ]);

        // 11. NOTIFICATIONS
        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Pengajuan Kebutuhan Baru Menunggu Moderasi',
            'message' => 'SD Inpres Waisilip mengajukan kebutuhan "Penggantian 30 Lembar Seng & Rangka Kayu".',
            'type' => 'need_review',
            'link' => '/admin/needs',
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Pledge Baru Menunggu Verifikasi',
            'message' => 'Budi Santoso mengajukan komitmen relawan untuk kebutuhan SD Negeri Pulau Miangas.',
            'type' => 'pledge_review',
            'link' => '/admin/pledges',
        ]);

        Notification::create([
            'user_id' => $userSchool1->id,
            'title' => 'Bantuan Telah Diselesaikan',
            'message' => 'Komitmen bantuan SA-202610-1001 dari Yayasan Lentera Asa telah berstatus Selesai.',
            'type' => 'pledge_completed',
            'link' => '/school/pledges',
        ]);
    }
}
