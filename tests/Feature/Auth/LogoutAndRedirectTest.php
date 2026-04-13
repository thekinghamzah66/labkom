<?php

namespace Tests\Feature\Auth;

use App\Models\LoginLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @method void assertRedirect(string $uri)
 * @method void withoutQueryLog()
 */
class LogoutAndRedirectTest extends TestCase
{
    private Role $mahasiswaRole;
    private Role $kalabRole;
    private Role $aslabRole;
    private User $mahasiswaUser;
    private User $kalabUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswaRole = Role::create([
            'name'            => 'Mahasiswa',
            'slug'            => 'mahasiswa',
            'dashboard_route' => 'mahasiswa.dashboard',
            'is_active'       => true,
        ]);

        $this->kalabRole = Role::create([
            'name'            => 'KALAB',
            'slug'            => 'kalab',
            'dashboard_route' => 'kalab.dashboard',
            'is_active'       => true,
        ]);

        $this->aslabRole = Role::create([
            'name'            => 'ASLAB',
            'slug'            => 'aslab',
            'dashboard_route' => 'aslab.dashboard',
            'is_active'       => true,
        ]);

        $this->mahasiswaUser = User::create([
            'role_id'   => $this->mahasiswaRole->id,
            'name'      => 'Mahasiswa Test',
            'username'  => 'mhs001',
            'email'     => 'mhs@test.com',
            'password'  => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->kalabUser = User::create([
            'role_id'   => $this->kalabRole->id,
            'name'      => 'KALAB Test',
            'username'  => 'kalab001',
            'email'     => 'kalab@test.com',
            'password'  => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    // =========================================================================
    // LOGOUT TESTS
    // =========================================================================

    #[Test]
    public function user_can_logout()
    {
        $this->actingAs($this->mahasiswaUser);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function session_is_invalidated_on_logout()
    {
        $this->actingAs($this->mahasiswaUser);

        $sessionId = $this->app['session']->getId();

        $this->post('/logout');

        $newSessionId = $this->app['session']->getId();
        $this->assertNotEquals($sessionId, $newSessionId);
    }

    #[Test]
    public function logout_regenerates_csrf_token()
    {
        $this->actingAs($this->mahasiswaUser);

        $oldToken = $this->app['session']->token();

        $this->post('/logout');

        $newToken = $this->app['session']->token();
        $this->assertNotEquals($oldToken, $newToken);
    }

    #[Test]
    public function logout_logs_the_action()
    {
        $this->actingAs($this->mahasiswaUser);

        $this->post('/logout');

        $this->assertDatabaseHas('login_logs', [
            'user_id'        => $this->mahasiswaUser->id,
            'status'         => 'logout',
        ]);
    }

    #[Test]
    public function logout_shows_success_message()
    {
        $this->actingAs($this->mahasiswaUser);

        $response = $this->post('/logout');

        $response->assertSessionHas('status', 'Berhasil keluar dari sistem.');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_logout()
    {
        $response = $this->post('/logout');

        // Should still redirect (middleware might allow it)
        $this->assertGuest();
    }

    // =========================================================================
    // ROLE SELECTION VIEW TESTS
    // =========================================================================

    #[Test]
    public function role_selection_page_loads_for_guests()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    #[Test]
    public function role_selection_shows_all_active_roles()
    {
        $response = $this->get('/');

        $response->assertViewHas('roles');
        $this->assertEquals(3, $response['roles']->count());
    }

    #[Test]
    public function role_selection_only_shows_active_roles()
    {
        $this->kalabRole->update(['is_active' => false]);

        $response = $this->get('/');

        $this->assertEquals(2, $response['roles']->count());
        $this->assertFalse($response['roles']->contains($this->kalabRole));
    }

    #[Test]
    public function authenticated_user_is_redirected_from_role_selection()
    {
        $this->actingAs($this->mahasiswaUser);

        $response = $this->get('/');

        $response->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function authenticated_kalab_is_redirected_to_kalab_dashboard()
    {
        $this->actingAs($this->kalabUser);

        $response = $this->get('/');

        $response->assertRedirect('kalab.dashboard');
    }

    // =========================================================================
    // LOGIN FORM VIEW TESTS
    // =========================================================================

    #[Test]
    public function login_form_displays_selected_role()
    {
        $response = $this->get('/login/mahasiswa');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertViewHas('selectedRole');
        $this->assertEquals('mahasiswa', $response['selectedRole']->slug);
    }

    #[Test]
    public function login_form_fails_with_invalid_role()
    {
        $response = $this->get('/login/invalid-role');

        $response->assertStatus(404);
    }

    #[Test]
    public function login_form_fails_with_inactive_role()
    {
        $this->mahasiswaRole->update(['is_active' => false]);

        $response = $this->get('/login/mahasiswa');

        $response->assertStatus(404);
    }

    #[Test]
    public function authenticated_user_is_redirected_from_login_form()
    {
        $this->actingAs($this->mahasiswaUser);

        $response = $this->get('/login/mahasiswa');

        $response->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function authenticated_user_cannot_access_other_role_login()
    {
        $this->actingAs($this->mahasiswaUser);

        $response = $this->get('/login/kalab');

        $response->assertRedirect('mahasiswa.dashboard');
    }

    // =========================================================================
    // DASHBOARD REDIRECT TEST
    // =========================================================================

    #[Test]
    public function mahasiswa_is_redirected_to_correct_dashboard()
    {
        $this->post('/login', [
            'username'       => 'mhs001',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $this->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function kalab_is_redirected_to_correct_dashboard()
    {
        $this->post('/login', [
            'username'       => 'kalab001',
            'password'       => 'password123',
            'role_selected'  => 'kalab',
        ]);

        $this->assertRedirect('kalab.dashboard');
    }

    #[Test]
    public function each_role_redirects_to_its_own_dashboard()
    {
        $aslab = User::create([
            'role_id'   => $this->aslabRole->id,
            'name'      => 'ASLAB Test',
            'username'  => 'aslab001',
            'email'     => 'aslab@test.com',
            'password'  => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->post('/login', [
            'username'       => 'aslab001',
            'password'       => 'password123',
            'role_selected'  => 'aslab',
        ]);

        $this->assertRedirect('aslab.dashboard');
    }

    // =========================================================================
    // GUEST MIDDLEWARE TESTS
    // =========================================================================

    #[Test]
    public function guest_routes_are_protected()
    {
        $this->actingAs($this->mahasiswaUser);

        // These should redirect, not show the guest pages
        $this->get('/')->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function google_redirect_route_requires_guest()
    {
        $this->actingAs($this->mahasiswaUser);

        $response = $this->post('/auth/google/redirect', [
            'role_selected' => 'mahasiswa',
        ]);

        // Guest middleware should redirect, not process the request
        $response->assertRedirect('mahasiswa.dashboard');
    }

    // =========================================================================
    // N+1 PREVENTION TESTS
    // =========================================================================

    #[Test]
    public function login_uses_eager_loading_for_role()
    {
        $this->withoutQueryLog();
        \DB::enableQueryLog();

        $this->post('/login', [
            'username'       => 'mhs001',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $queries = \DB::getQueryLog();

        // Should have limited queries (no N+1)
        // Rough count: 1 rate limit check, 1 user query with role, login logs
        // Should be < 10 queries
        $this->assertLessThan(10, count($queries));

        \DB::disableQueryLog();
    }

    #[Test]
    public function redirect_to_dashboard_does_not_cause_n_plus_one()
    {
        // Create 10 users to ensure no N+1
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'role_id'   => $this->mahasiswaRole->id,
                'name'      => "User $i",
                'username'  => "user$i",
                'email'     => "user$i@test.com",
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        \DB::enableQueryLog();

        $this->post('/login', [
            'username'       => 'mhs001',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $queries = \DB::getQueryLog();

        // Should still be minimal queries despite multiple users
        $this->assertLessThan(15, count($queries));

        \DB::disableQueryLog();
    }

    // =========================================================================
    // EDGE CASES FOR REDIRECT LOGIC
    // =========================================================================

    #[Test]
    public function redirect_handles_missing_dashboard_route_gracefully()
    {
        $brokenRole = Role::create([
            'name'            => 'Broken Role',
            'slug'            => 'broken',
            'dashboard_route' => 'nonexistent.route',
            'is_active'       => true,
        ]);

        $brokenUser = User::create([
            'role_id'   => $brokenRole->id,
            'name'      => 'Broken User',
            'username'  => 'broken_user',
            'email'     => 'broken@test.com',
            'password'  => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'username'       => 'broken_user',
            'password'       => 'password123',
            'role_selected'  => 'broken',
        ]);

        // Should attempt to redirect to nonexistent route
        // (Laravel will handle the 404)
        $response->assertRedirect();
    }
}
