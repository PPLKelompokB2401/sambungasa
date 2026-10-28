<?php

namespace Tests\Feature;

use App\Models\ContributorProfile;
use App\Models\EvidenceFile;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EvidenceSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $contributorUser;
    protected $contributorProfile;
    protected $schoolUser;
    protected $schoolProfile;
    protected $otherUser;
    protected $pledge;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->otherUser = User::factory()->create(['role' => 'school', 'status' => 'active']);

        $region = Region::where('is_3t', true)->first();
        $category = NeedCategory::first();

        $this->schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active']);
        $this->schoolProfile = SchoolProfile::create([
            'user_id' => $this->schoolUser->id,
            'school_name' => 'SD Negeri Merauke',
            'npsn' => '60108899',
            'region_id' => $region->id,
            'address' => 'Jl. Boven Digoel',
            'verification_status' => 'verified',
        ]);

        $this->contributorUser = User::factory()->create(['role' => 'contributor', 'status' => 'active']);
        $this->contributorProfile = ContributorProfile::create([
            'user_id' => $this->contributorUser->id,
            'contributor_type' => 'donor',
            'verification_status' => 'verified',
        ]);

        $need = Need::create([
            'school_profile_id' => $this->schoolProfile->id,
            'need_category_id' => $category->id,
            'title' => 'Perangkat Komputer Laboratorium',
            'description' => 'Komputer untuk latihan ANBK murid perbatasan.',
            'assistance_type' => 'material',
            'priority' => 'tinggi',
            'quantity_required' => 5,
            'status' => 'published',
        ]);

        $this->pledge = Pledge::create([
            'need_id' => $need->id,
            'contributor_profile_id' => $this->contributorProfile->id,
            'tracking_code' => 'SA-202610-EVID',
            'quantity' => 2,
            'notes' => 'Pengiriman PC laboratorium.',
            'status' => 'in_delivery',
        ]);
    }

    public function test_contributor_can_upload_valid_evidence_file()
    {
        $file = UploadedFile::fake()->create('resi_pengiriman.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->contributorUser)->post("/evidence/{$this->pledge->id}/upload", [
            'evidence_file' => $file,
            'evidence_type' => 'delivery_receipt',
            'caption' => 'Resi kargo udara Jayapura - Merauke',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('evidence_files', [
            'pledge_id' => $this->pledge->id,
            'original_name' => 'resi_pengiriman.pdf',
            'evidence_type' => 'delivery_receipt',
        ]);

        $evidence = EvidenceFile::where('pledge_id', $this->pledge->id)->first();
        $this->assertStringStartsWith('private/evidence/', $evidence->file_path);
        Storage::disk('local')->assertExists($evidence->file_path);
    }

    public function test_dangerous_file_extensions_are_rejected()
    {
        $file = UploadedFile::fake()->create('exploit.php', 50, 'application/x-php');

        $response = $this->actingAs($this->contributorUser)->post("/evidence/{$this->pledge->id}/upload", [
            'evidence_file' => $file,
            'evidence_type' => 'delivery_receipt',
        ]);

        $response->assertSessionHasErrors('evidence_file');
        $this->assertEquals(0, EvidenceFile::count());
    }

    public function test_oversized_file_exceeding_5mb_is_rejected()
    {
        // 6000 KB > 5120 KB
        $file = UploadedFile::fake()->create('video_large.pdf', 6000, 'application/pdf');

        $response = $this->actingAs($this->contributorUser)->post("/evidence/{$this->pledge->id}/upload", [
            'evidence_file' => $file,
            'evidence_type' => 'delivery_receipt',
        ]);

        $response->assertSessionHasErrors('evidence_file');
        $this->assertEquals(0, EvidenceFile::count());
    }

    public function test_authorized_users_can_download_evidence()
    {
        $fakePath = 'private/evidence/test_bast.pdf';
        Storage::disk('local')->put($fakePath, 'DOKUMEN_BAST_RESMI_SAMBUNGASA');

        $evidence = EvidenceFile::create([
            'pledge_id' => $this->pledge->id,
            'uploaded_by' => $this->contributorUser->id,
            'file_path' => $fakePath,
            'original_name' => 'test_bast.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'evidence_type' => 'handover_bast',
        ]);

        // Contributor can download
        $respContributor = $this->actingAs($this->contributorUser)->get("/evidence/{$evidence->id}/download");
        $respContributor->assertStatus(200);

        // School can download
        $respSchool = $this->actingAs($this->schoolUser)->get("/evidence/{$evidence->id}/download");
        $respSchool->assertStatus(200);

        // Admin can download
        $respAdmin = $this->actingAs($this->admin)->get("/evidence/{$evidence->id}/download");
        $respAdmin->assertStatus(200);
    }

    public function test_unauthorized_third_party_is_forbidden_from_downloading_evidence()
    {
        $fakePath = 'private/evidence/private_doc.pdf';
        Storage::disk('local')->put($fakePath, 'SENSITIVE_DATA');

        $evidence = EvidenceFile::create([
            'pledge_id' => $this->pledge->id,
            'uploaded_by' => $this->contributorUser->id,
            'file_path' => $fakePath,
            'original_name' => 'private_doc.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'evidence_type' => 'handover_bast',
        ]);

        // Unrelated user cannot download
        $response = $this->actingAs($this->otherUser)->get("/evidence/{$evidence->id}/download");
        $response->assertStatus(403);
    }
}
