<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Region;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun SambungAsa');
    }

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke SambungAsa');
    }

    public function test_contributor_can_register_successfully()
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.org',
            'password' => 'Rahasia1234!',
            'password_confirmation' => 'Rahasia1234!',
            'role' => 'contributor',
            'contributor_type' => 'donor',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect('/contributor/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@example.org',
            'role' => 'contributor',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('contributor_profiles', [
            'contributor_type' => 'donor',
        ]);
    }

    public function test_school_can_register_and_sets_status_pending()
    {
        $region = Region::where('is_3t', true)->first();

        $response = $this->post('/register', [
            'name' => 'Kepala Sekolah SD Inpres',
            'email' => 'kepsek.inpres@example.org',
            'password' => 'Rahasia1234!',
            'password_confirmation' => 'Rahasia1234!',
            'role' => 'school',
            'school_name' => 'SD Inpres Ilaga 01',
            'npsn' => '60109988',
            'region_id' => $region->id,
            'phone' => '081299887766',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'kepsek.inpres@example.org',
            'role' => 'school',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('school_profiles', [
            'school_name' => 'SD Inpres Ilaga 01',
            'npsn' => '60109988',
            'verification_status' => 'pending',
        ]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'donatur.test@example.org',
            'password' => bcrypt('password123'),
            'role' => 'contributor',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'donatur.test@example.org',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/contributor/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password()
    {
        User::factory()->create([
            'email' => 'donatur.test@example.org',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'donatur.test@example.org',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response2 = $this->get('/school/dashboard');
        $response2->assertRedirect('/login');

        $response3 = $this->get('/contributor/dashboard');
        $response3->assertRedirect('/login');
    }

    public function test_contributor_cannot_access_admin_dashboard()
    {
        $contributor = User::factory()->create([
            'role' => 'contributor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($contributor)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_school_cannot_access_admin_user_management()
    {
        $school = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $response = $this->actingAs($school)->get('/admin/users');
        $response->assertStatus(403);
    }
}
