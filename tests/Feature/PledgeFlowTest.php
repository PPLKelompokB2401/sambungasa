<?php

namespace Tests\Feature;

use App\Models\ContributorProfile;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PledgeFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $contributorUser;
    protected $contributorProfile;
    protected $schoolUser;
    protected $schoolProfile;
    protected $need;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $region = Region::where('is_3t', true)->first();
        $category = NeedCategory::first();

        // School & Profile
        $this->schoolUser = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $this->schoolProfile = SchoolProfile::create([
            'user_id' => $this->schoolUser->id,
            'school_name' => 'SD Negeri Puncak Papua',
            'npsn' => '60107766',
            'region_id' => $region->id,
            'address' => 'Distrik Sinak',
            'verification_status' => 'verified',
        ]);

        // Contributor & Profile
        $this->contributorUser = User::factory()->create([
            'role' => 'contributor',
            'status' => 'active',
        ]);

        $this->contributorProfile = ContributorProfile::create([
            'user_id' => $this->contributorUser->id,
            'contributor_type' => 'donor',
            'verification_status' => 'verified',
        ]);

        // Published Need
        $this->need = Need::create([
            'school_profile_id' => $this->schoolProfile->id,
            'need_category_id' => $category->id,
            'title' => 'Genset Listrik untuk Kelas',
            'description' => 'Genset 2000 watt untuk penerangan ruang kelas dan operasional laptop pengajar.',
            'assistance_type' => 'material',
            'priority' => 'mendesak',
            'quantity_required' => 2,
            'quantity_fulfilled' => 0,
            'unit' => 'unit',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function test_contributor_can_submit_pledge_with_unique_tracking_code()
    {
        $response = $this->actingAs($this->contributorUser)->post("/contributor/needs/{$this->need->id}/pledge", [
            'quantity' => 1,
            'notes' => 'Akan kami kirimkan genset Honda 2000W via kargo udara Timika.',
            'estimated_delivery_at' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertRedirect();

        $pledge = Pledge::where('need_id', $this->need->id)
            ->where('contributor_profile_id', $this->contributorProfile->id)
            ->first();

        $this->assertNotNull($pledge);
        $this->assertEquals('submitted', $pledge->status);
        $this->assertEquals(1, $pledge->quantity);
        $this->assertMatchesRegularExpression('/^SA-\d{6}-[A-Z0-9]{4}$/', $pledge->tracking_code);

        // Tracking log created
        $this->assertDatabaseHas('tracking_logs', [
            'pledge_id' => $pledge->id,
            'new_status' => 'submitted',
        ]);

        // Notification for admin created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'type' => 'pledge_submitted',
        ]);
    }

    public function test_over_pledging_exceeding_remaining_need_is_prevented()
    {
        // Remaining need is 2, user requests 5
        $response = $this->actingAs($this->contributorUser)->post("/contributor/needs/{$this->need->id}/pledge", [
            'quantity' => 5,
            'notes' => 'Pengajuan jumlah melebihi kuota sisa yang dibutuhkan.',
            'estimated_delivery_at' => now()->addDays(7)->format('Y-m-d'),
        ]);

        // Expect exception or error redirect
        $this->assertEquals(0, Pledge::where('need_id', $this->need->id)->count());
    }

    public function test_admin_can_approve_pledge()
    {
        $pledge = Pledge::create([
            'need_id' => $this->need->id,
            'contributor_profile_id' => $this->contributorProfile->id,
            'tracking_code' => 'SA-202610-TEST',
            'quantity' => 1,
            'notes' => 'Pengajuan bantuan genset.',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/pledges/{$pledge->id}/approve", [
            'notes' => 'Komitmen telah diverifikasi dan disetujui untuk dikirim.',
        ]);

        $response->assertRedirect();
        $pledge->refresh();

        $this->assertEquals('verified', $pledge->status);
        $this->assertEquals($this->admin->id, $pledge->reviewed_by);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->contributorUser->id,
            'type' => 'pledge_approved',
        ]);
    }

    public function test_admin_can_reject_pledge_with_reason()
    {
        $pledge = Pledge::create([
            'need_id' => $this->need->id,
            'contributor_profile_id' => $this->contributorProfile->id,
            'tracking_code' => 'SA-202610-REJ1',
            'quantity' => 1,
            'notes' => 'Bantuan tidak jelas spesifikasinya.',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/pledges/{$pledge->id}/reject", [
            'reason' => 'Barang yang ditawarkan tidak sesuai dengan spesifikasi voltase sekolah.',
        ]);

        $response->assertRedirect();
        $pledge->refresh();

        $this->assertEquals('rejected', $pledge->status);
        $this->assertEquals('Barang yang ditawarkan tidak sesuai dengan spesifikasi voltase sekolah.', $pledge->rejection_reason);
    }

    public function test_school_can_confirm_receipt_of_assistance()
    {
        $pledge = Pledge::create([
            'need_id' => $this->need->id,
            'contributor_profile_id' => $this->contributorProfile->id,
            'tracking_code' => 'SA-202610-RCV1',
            'quantity' => 1,
            'notes' => 'Pengiriman genset sedang berlangsung.',
            'status' => 'in_delivery',
        ]);

        $response = $this->actingAs($this->schoolUser)->post("/school/pledges/{$pledge->id}/confirm-receipt", [
            'recipient_name' => 'Bapak Markus Kogoya (Kepsek)',
            'notes' => 'Genset telah tiba di sekolah dalam kondisi baru, tersegel, dan telah diuji menyala.',
        ]);

        $response->assertRedirect();
        $pledge->refresh();

        $this->assertEquals('received', $pledge->status);

        $this->assertDatabaseHas('tracking_logs', [
            'pledge_id' => $pledge->id,
            'new_status' => 'received',
        ]);
    }

    public function test_admin_marking_completed_updates_need_fulfilled_quantity()
    {
        $pledge = Pledge::create([
            'need_id' => $this->need->id,
            'contributor_profile_id' => $this->contributorProfile->id,
            'tracking_code' => 'SA-202610-CMPL',
            'quantity' => 2, // Matches entire required quantity
            'notes' => 'Pengiriman 2 unit genset.',
            'status' => 'received',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/pledges/{$pledge->id}/status", [
            'status' => 'completed',
            'notes' => 'Dokumen BAST dan foto serah terima lengkap dan terverifikasi.',
        ]);

        $response->assertRedirect();
        $pledge->refresh();
        $this->need->refresh();

        $this->assertEquals('completed', $pledge->status);
        $this->assertEquals(2, $this->need->quantity_fulfilled);
        $this->assertEquals('fulfilled', $this->need->status); // 100% fulfilled!
    }

    public function test_illegal_status_transition_is_blocked()
    {
        $pledge = Pledge::create([
            'need_id' => $this->need->id,
            'contributor_profile_id' => $this->contributorProfile->id,
            'tracking_code' => 'SA-202610-ILLE',
            'quantity' => 1,
            'notes' => 'Pledge baru diajukan.',
            'status' => 'submitted', // cannot jump directly to completed
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/pledges/{$pledge->id}/status", [
            'status' => 'completed',
        ]);

        $pledge->refresh();
        $this->assertEquals('submitted', $pledge->status); // Remains submitted
    }
}
