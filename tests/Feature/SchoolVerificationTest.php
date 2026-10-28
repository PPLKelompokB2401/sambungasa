<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $schoolUser;
    protected $schoolProfile;
    protected $region;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->region = Region::where('is_3t', true)->first();

        $this->schoolUser = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $this->schoolProfile = SchoolProfile::create([
            'user_id' => $this->schoolUser->id,
            'school_name' => 'SMP Negeri 1 Sentani 3T',
            'npsn' => '60105544',
            'region_id' => $this->region->id,
            'address' => 'Jl. Pendidikan No. 1, Papua',
            'verification_status' => 'pending',
        ]);
    }

    public function test_admin_can_view_school_verification_list()
    {
        $response = $this->actingAs($this->admin)->get('/admin/schools');

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Sekolah 3T');
        $response->assertSee('SMP Negeri 1 Sentani 3T');
    }

    public function test_admin_can_view_school_detail_for_verification()
    {
        $response = $this->actingAs($this->admin)->get("/admin/schools/{$this->schoolProfile->id}");

        $response->assertStatus(200);
        $response->assertSee('SMP Negeri 1 Sentani 3T');
        $response->assertSee($this->schoolProfile->npsn);
    }

    public function test_admin_can_verify_3t_school()
    {
        $response = $this->actingAs($this->admin)->post("/admin/schools/{$this->schoolProfile->id}/verify", [
            'notes' => 'Telah dicocokkan dengan data Dapodik dan Kepmen 3T.',
        ]);

        $response->assertRedirect();
        $this->schoolProfile->refresh();

        $this->assertEquals('verified', $this->schoolProfile->verification_status);
        $this->assertEquals($this->admin->id, $this->schoolProfile->verified_by);
        $this->assertNotNull($this->schoolProfile->verified_at);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'verify_school_3t',
            'entity_id' => $this->schoolProfile->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->schoolUser->id,
            'type' => 'school_verified',
        ]);
    }

    public function test_admin_can_reject_school_with_reason()
    {
        $response = $this->actingAs($this->admin)->post("/admin/schools/{$this->schoolProfile->id}/reject", [
            'reason' => 'NPSN tidak ditemukan pada database referensi Kemendikbud.',
        ]);

        $response->assertRedirect();
        $this->schoolProfile->refresh();

        $this->assertEquals('rejected', $this->schoolProfile->verification_status);
        $this->assertEquals('NPSN tidak ditemukan pada database referensi Kemendikbud.', $this->schoolProfile->verification_notes);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->schoolUser->id,
            'type' => 'school_rejected',
        ]);
    }

    public function test_admin_can_request_revision_from_school()
    {
        $response = $this->actingAs($this->admin)->post("/admin/schools/{$this->schoolProfile->id}/request-revision", [
            'notes' => 'Mohon unggah surat keputusan pendirian sekolah resmi.',
        ]);

        $response->assertRedirect();
        $this->schoolProfile->refresh();

        $this->assertEquals('needs_revision', $this->schoolProfile->verification_status);
        $this->assertEquals('Mohon unggah surat keputusan pendirian sekolah resmi.', $this->schoolProfile->verification_notes);
    }

    public function test_non_admin_cannot_verify_schools()
    {
        $response = $this->actingAs($this->schoolUser)->post("/admin/schools/{$this->schoolProfile->id}/verify", [
            'notes' => 'Attempt self verify',
        ]);

        $response->assertStatus(403);
    }
}
