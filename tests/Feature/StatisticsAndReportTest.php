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

class StatisticsAndReportTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $schoolUser;
    protected $contributorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active']);
        $this->contributorUser = User::factory()->create(['role' => 'contributor', 'status' => 'active']);

        $region = Region::where('is_3t', true)->first();
        $category = NeedCategory::first();

        $school = SchoolProfile::create([
            'user_id' => $this->schoolUser->id,
            'school_name' => 'SD Negeri Pelopor Wamena',
            'npsn' => '60107711',
            'region_id' => $region->id,
            'address' => 'Distrik Wamena',
            'verification_status' => 'verified',
        ]);

        $contributor = ContributorProfile::create([
            'user_id' => $this->contributorUser->id,
            'contributor_type' => 'donor',
            'verification_status' => 'verified',
        ]);

        $need = Need::create([
            'school_profile_id' => $school->id,
            'need_category_id' => $category->id,
            'title' => 'Seragam Sekolah Siswa Kurang Mampu',
            'description' => 'Seragam merah putih untuk 40 siswa yang belum memiliki seragam.',
            'assistance_type' => 'material',
            'priority' => 'tinggi',
            'quantity_required' => 40,
            'quantity_fulfilled' => 40,
            'unit' => 'stel',
            'status' => 'fulfilled',
        ]);

        Pledge::create([
            'need_id' => $need->id,
            'contributor_profile_id' => $contributor->id,
            'tracking_code' => 'SA-202610-RPT1',
            'quantity' => 40,
            'notes' => 'Penyaluran 40 stel seragam selesai.',
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function test_public_statistics_calculates_real_database_aggregations()
    {
        $response = $this->get('/statistics');

        $response->assertStatus(200);
        $response->assertSee('Transparansi & Statistik Dampak');
        $response->assertSee('Sekolah 3T Terverifikasi');
        $response->assertSee('Kebutuhan Selesai Dipenuhi');
        $response->assertSee('SD Negeri Pelopor Wamena');
    }

    public function test_admin_reports_page_is_protected_and_filterable()
    {
        // Non-admin blocked
        $respForbidden = $this->actingAs($this->contributorUser)->get('/admin/reports');
        $respForbidden->assertStatus(403);

        // Admin can access
        $response = $this->actingAs($this->admin)->get('/admin/reports?status=completed');
        $response->assertStatus(200);
        $response->assertSee('Laporan Penyaluran Bantuan');
        $response->assertSee('SA-202610-RPT1');
    }

    public function test_csv_export_generates_file_with_formula_injection_sanitization()
    {
        // Create a pledge with potential formula injection payload in tracking notes / name
        $contributorEvil = ContributorProfile::create([
            'user_id' => User::factory()->create(['name' => '=CMD|/C calc!A0', 'role' => 'contributor'])->id,
            'contributor_type' => 'donor',
            'verification_status' => 'verified',
        ]);

        $need = Need::first();

        Pledge::create([
            'need_id' => $need->id,
            'contributor_profile_id' => $contributorEvil->id,
            'tracking_code' => 'SA-202610-EVIL',
            'quantity' => 1,
            'notes' => '@SUM(1+1)*cmd',
            'status' => 'verified',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/reports/export');

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'attachment; filename='));

        $content = $response->streamedContent();

        // Formula injection sanitized with leading single quote
        $this->assertTrue(str_contains($content, "'=CMD|/C calc!A0"));
    }

    public function test_admin_can_view_activity_logs()
    {
        // Non-admin forbidden
        $respSchool = $this->actingAs($this->schoolUser)->get('/admin/activity-logs');
        $respSchool->assertStatus(403);

        // Admin allowed
        $response = $this->actingAs($this->admin)->get('/admin/activity-logs');
        $response->assertStatus(200);
        $response->assertSee('Audit Trail');
        $response->assertSee('Log Aktivitas');
    }
}
