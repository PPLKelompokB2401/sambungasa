<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NeedCategory;

class NeedCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Buku & Alat Tulis',
                'slug' => 'buku-alat-tulis',
                'assistance_type' => 'material',
                'description' => 'Buku teks pelajaran kurikulum nasional, buku bacaan bergambar, modul ajar, buku tulis, dan perlengkapan alat tulis siswa.',
                'icon' => 'book-open',
                'is_active' => true,
            ],
            [
                'name' => 'Elektronik & Komputer',
                'slug' => 'elektronik-komputer',
                'assistance_type' => 'material',
                'description' => 'Laptop/komputer untuk asesmen nasional (ANBK), proyektor kelas, router solar, genset kecil, dan perangkat digital pendukung pembelajaran.',
                'icon' => 'laptop',
                'is_active' => true,
            ],
            [
                'name' => 'Infrastruktur & Bangunan',
                'slug' => 'infrastruktur-bangunan',
                'assistance_type' => 'material',
                'description' => 'Perbaikan atap bocor, meja kursi kelas, fasilitas MCK sanitasi sehat, panel surya mini, dan perpustakaan sederhana.',
                'icon' => 'home',
                'is_active' => true,
            ],
            [
                'name' => 'Pelatihan & Guru Relawan',
                'slug' => 'pelatihan-guru-relawan',
                'assistance_type' => 'non_material',
                'description' => 'Tenaga guru relawan, bimbingan literasi numerasi, pelatihan metode mengajar kreatif untuk guru lokal, dan pendampingan psikososial.',
                'icon' => 'users',
                'is_active' => true,
            ],
            [
                'name' => 'Lainnya',
                'slug' => 'lainnya',
                'assistance_type' => 'material',
                'description' => 'Seragam sekolah, sepatu, tas sekolah, perlengkapan olahraga, dan kebutuhan khusus non-kategori.',
                'icon' => 'gift',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            NeedCategory::create($cat);
        }
    }
}
