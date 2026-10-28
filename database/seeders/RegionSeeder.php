<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PAPUA
        $papua = Region::create([
            'code' => 'ID-PA',
            'name' => 'Papua',
            'level' => 'province',
            'island' => 'Papua',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah fokus 3T Indonesia Timur',
        ]);

        Region::create([
            'parent_id' => $papua->id,
            'code' => 'ID-PA-JPR',
            'name' => 'Kabupaten Jayapura',
            'level' => 'regency',
            'island' => 'Papua',
            'is_3t' => false,
            'is_active' => true,
        ]);

        Region::create([
            'parent_id' => $papua->id,
            'code' => 'ID-PA-PUN',
            'name' => 'Kabupaten Puncak',
            'level' => 'regency',
            'island' => 'Papua',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Tertinggal & Terdepan',
        ]);

        Region::create([
            'parent_id' => $papua->id,
            'code' => 'ID-PA-ASMT',
            'name' => 'Kabupaten Asmat',
            'level' => 'regency',
            'island' => 'Papua',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Terpencil & Tertinggal',
        ]);

        Region::create([
            'parent_id' => $papua->id,
            'code' => 'ID-PA-YHM',
            'name' => 'Kabupaten Yahukimo',
            'level' => 'regency',
            'island' => 'Papua',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Pegunungan',
        ]);

        // PAPUA BARAT
        $papuaBarat = Region::create([
            'code' => 'ID-PB',
            'name' => 'Papua Barat',
            'level' => 'province',
            'island' => 'Papua',
            'is_3t' => true,
            'is_active' => true,
        ]);

        Region::create([
            'parent_id' => $papuaBarat->id,
            'code' => 'ID-PB-TB',
            'name' => 'Kabupaten Teluk Bintuni',
            'level' => 'regency',
            'island' => 'Papua',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Pesisir',
        ]);

        Region::create([
            'parent_id' => $papuaBarat->id,
            'code' => 'ID-PB-MNK',
            'name' => 'Kabupaten Manokwari',
            'level' => 'regency',
            'island' => 'Papua',
            'is_3t' => false,
            'is_active' => true,
        ]);

        // 2. KALIMANTAN
        $kalbar = Region::create([
            'code' => 'ID-KB',
            'name' => 'Kalimantan Barat',
            'level' => 'province',
            'island' => 'Kalimantan',
            'is_3t' => true,
            'is_active' => true,
        ]);

        Region::create([
            'parent_id' => $kalbar->id,
            'code' => 'ID-KB-SKD',
            'name' => 'Kabupaten Sanggau',
            'level' => 'regency',
            'island' => 'Kalimantan',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Perbatasan Entikong (Terdepan)',
        ]);

        Region::create([
            'parent_id' => $kalbar->id,
            'code' => 'ID-KB-KAP',
            'name' => 'Kabupaten Kapuas Hulu',
            'level' => 'regency',
            'island' => 'Kalimantan',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Perbatasan Badau (Terdepan)',
        ]);

        $kaltara = Region::create([
            'code' => 'ID-KU',
            'name' => 'Kalimantan Utara',
            'level' => 'province',
            'island' => 'Kalimantan',
            'is_3t' => true,
            'is_active' => true,
        ]);

        Region::create([
            'parent_id' => $kaltara->id,
            'code' => 'ID-KU-NNK',
            'name' => 'Kabupaten Nunukan',
            'level' => 'regency',
            'island' => 'Kalimantan',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Perbatasan Pulau Sebatik (Terdepan/Terluar)',
        ]);

        Region::create([
            'parent_id' => $kaltara->id,
            'code' => 'ID-KU-MLN',
            'name' => 'Kabupaten Malinau',
            'level' => 'regency',
            'island' => 'Kalimantan',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Pedalaman',
        ]);

        // 3. SULAWESI
        $sulut = Region::create([
            'code' => 'ID-SA',
            'name' => 'Sulawesi Utara',
            'level' => 'province',
            'island' => 'Sulawesi',
            'is_3t' => true,
            'is_active' => true,
        ]);

        Region::create([
            'parent_id' => $sulut->id,
            'code' => 'ID-SA-KTL',
            'name' => 'Kabupaten Kepulauan Talaud',
            'level' => 'regency',
            'island' => 'Sulawesi',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Kepulauan Terluar Indonesia Utara (Pulau Miangas)',
        ]);

        Region::create([
            'parent_id' => $sulut->id,
            'code' => 'ID-SA-SGI',
            'name' => 'Kabupaten Kepulauan Sangihe',
            'level' => 'regency',
            'island' => 'Sulawesi',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Kepulauan Terluar',
        ]);

        $sulteng = Region::create([
            'code' => 'ID-ST',
            'name' => 'Sulawesi Tengah',
            'level' => 'province',
            'island' => 'Sulawesi',
            'is_3t' => true,
            'is_active' => true,
        ]);

        Region::create([
            'parent_id' => $sulteng->id,
            'code' => 'ID-ST-DGL',
            'name' => 'Kabupaten Donggala',
            'level' => 'regency',
            'island' => 'Sulawesi',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Tertinggal',
        ]);

        Region::create([
            'parent_id' => $sulteng->id,
            'code' => 'ID-ST-TOJO',
            'name' => 'Kabupaten Tojo Una-Una',
            'level' => 'regency',
            'island' => 'Sulawesi',
            'is_3t' => true,
            'is_active' => true,
            'notes' => 'Wilayah 3T Kepulauan Togean',
        ]);
    }
}
