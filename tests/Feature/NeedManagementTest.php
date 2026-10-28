<?php

namespace Tests\Feature;

use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\Region;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NeedManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $verifiedSchoolUser;
    protected $verifiedSchool;
    protected $unverifiedSchoolUser;
    protected $unverifiedSchool;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $region = Region::where('is_3t', true)->first();
        $this->category = NeedCategory::first();

        // Verified School
        $this->verifiedSchoolUser = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $this->verifiedSchool = SchoolProfile::create([
            'user_id' => $this->verifiedSchoolUser->id,
            'school_name' => 'SD Negeri Terpencil Papua',
            'npsn' => '60101122',
            'region_id' => $region->id,
            'address' => 'Pedalaman Distrik Ilaga',
            'verification_status' => 'verified',
        ]);

        // Unverified School
        $this->unverifiedSchoolUser = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $this->unverifiedSchool = SchoolProfile::create([
            'user_id' => $this->unverifiedSchoolUser->id,
            'school_name' => 'SD Belum Verifikasi',
            'npsn' => '60109911',
            'region_id' => $region->id,
            'address' => 'Lokasi Pengajuan',
            'verification_status' => 'pending',
        ]);
    }

    public function test_unverified_school_cannot_create_need()
    {
        $response = $this->actingAs($this->unverifiedSchoolUser)->post('/school/needs', [
            'title' => 'Pengajuan Meja dan Kursi Belajar',
            'need_category_id' => $this->category->id,
            'assistance_type' => 'material',
            'priority' => 'tinggi',
            'quantity_required' => 20,
            'description' => 'Memerlukan meja dan kursi untuk ruang kelas satu yang rusak parah.',
        ]);

        $response->assertStatus(403);
    }

    public function test_verified_school_can_create_need_with_pending_status()
    {
        $response = $this->actingAs($this->verifiedSchoolUser)->post('/school/needs', [
            'title' => 'Buku Pelajaran Kurikulum Merdeka Kelas 1-6',
            'need_category_id' => $this->category->id,
            'assistance_type' => 'material',
            'priority' => 'mendesak',
            'quantity_required' => 100,
            'unit' => 'paket',
            'description' => 'Sekolah sangat membutuhkan paket buku bacaan dan kurikulum terbaru untuk 60 siswa.',
        ]);

        $response->assertRedirect('/school/needs');

        $this->assertDatabaseHas('needs', [
            'school_profile_id' => $this->verifiedSchool->id,
            'title' => 'Buku Pelajaran Kurikulum Merdeka Kelas 1-6',
            'status' => 'pending_review',
        ]);
    }

    public function test_admin_can_publish_pending_need()
    {
        $need = Need::create([
            'school_profile_id' => $this->verifiedSchool->id,
            'need_category_id' => $this->category->id,
            'title' => 'Papan Tulis Whiteboard untuk 3 Kelas',
            'description' => 'Papan tulis lama berbahan triplek sudah lapuk dan tidak dapat ditulisi spidol.',
            'assistance_type' => 'material',
            'priority' => 'sedang',
            'quantity_required' => 3,
            'unit' => 'buah',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/needs/{$need->id}/publish");

        $response->assertRedirect();
        $need->refresh();

        $this->assertEquals('published', $need->status);
        $this->assertEquals($this->admin->id, $need->reviewed_by);
        $this->assertNotNull($need->published_at);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->verifiedSchoolUser->id,
            'type' => 'need_published',
        ]);
    }

    public function test_admin_can_reject_need_with_reason()
    {
        $need = Need::create([
            'school_profile_id' => $this->verifiedSchool->id,
            'need_category_id' => $this->category->id,
            'title' => 'Pengajuan Laptop Gaming Pribadi Guru',
            'description' => 'Membutuhkan laptop berspesifikasi tinggi untuk keperluan hiburan guru.',
            'assistance_type' => 'material',
            'priority' => 'rendah',
            'quantity_required' => 1,
            'unit' => 'unit',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/needs/{$need->id}/reject", [
            'reason' => 'Pengajuan tidak relevan dengan kebutuhan fasilitas belajar murid.',
        ]);

        $response->assertRedirect();
        $need->refresh();

        $this->assertEquals('rejected', $need->status);
        $this->assertEquals('Pengajuan tidak relevan dengan kebutuhan fasilitas belajar murid.', $need->rejection_notes);
    }

    public function test_public_needs_directory_only_lists_published_needs()
    {
        $publishedNeed = Need::create([
            'school_profile_id' => $this->verifiedSchool->id,
            'need_category_id' => $this->category->id,
            'title' => 'Kebutuhan Terbit Publik',
            'description' => 'Deskripsi kebutuhan yang telah disetujui admin dan siap didanai donatur.',
            'assistance_type' => 'material',
            'priority' => 'tinggi',
            'quantity_required' => 50,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $pendingNeed = Need::create([
            'school_profile_id' => $this->verifiedSchool->id,
            'need_category_id' => $this->category->id,
            'title' => 'Kebutuhan Masih Tertunda Review',
            'description' => 'Deskripsi kebutuhan yang masih menunggu moderasi admin.',
            'assistance_type' => 'material',
            'priority' => 'sedang',
            'quantity_required' => 10,
            'status' => 'pending_review',
        ]);

        $response = $this->get('/needs');

        $response->assertStatus(200);
        $response->assertSee('Kebutuhan Terbit Publik');
        $response->assertDontSee('Kebutuhan Masih Tertunda Review');
    }

    public function test_school_cannot_edit_another_schools_need()
    {
        $anotherSchoolUser = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $anotherSchool = SchoolProfile::create([
            'user_id' => $anotherSchoolUser->id,
            'school_name' => 'SMP Lain',
            'npsn' => '60108877',
            'region_id' => $this->verifiedSchool->region_id,
            'address' => 'Jalan Lain',
            'verification_status' => 'verified',
        ]);

        $need = Need::create([
            'school_profile_id' => $anotherSchool->id,
            'need_category_id' => $this->category->id,
            'title' => 'Kebutuhan Sekolah Lain',
            'description' => 'Deskripsi kebutuhan milik sekolah lain.',
            'assistance_type' => 'material',
            'priority' => 'tinggi',
            'quantity_required' => 10,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->verifiedSchoolUser)->get("/school/needs/{$need->id}/edit");
        $response->assertStatus(403);
    }
}
