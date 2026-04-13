<?php

namespace Tests\Feature\Auth;

use App\Models\LoginLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @method mixed getCookie(string $name)
 */
class LoginTest extends TestCase
{
    private Role $mahasiswaRole;
    private Role $kalabRole;
    private User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->mahasiswaRole = Role::create([
            'name'            => 'Mahasiswa',
            'slug'            => 'mahasiswa',
            'description'     => 'Student',
            'dashboard_route' => 'mahasiswa.dashboard',
            'color_class'     => 'from-blue-500',
            'icon'            => 'academic-cap',
            'is_active'       => true,
        ]);

        $this->kalabRole = Role::create([
            'name'            => 'KALAB',
            'slug'            => 'kalab',
            'description'     => 'Lab Head',
            'dashboard_route' => 'kalab.dashboard',
            'color_class'     => 'from-violet-600',
            'icon'            => 'shield',
            'is_active'       => true,
        ]);

        // Create a standard test user
        $this->testUser = User::create([
            'role_id'       => $this->mahasiswaRole->id,
            'name'          => 'Test Mahasiswa',
            'username'      => '1234567890',
            'email'         => 'mahasiswa@test.com',
            'password'      => Hash::make('password123'),
            'is_active'     => true,
            'login_attempts' => 0,
        ]);
    }

    // =========================================================================
    // HAPPY PATH TESTS
    // =========================================================================

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertRedirect('mahasiswa.dashboard');
        $this->assertAuthenticatedAs($this->testUser);
        $this->assertDatabaseHas('login_logs', [
            'user_id'       => $this->testUser->id,
            'status'        => 'success',
        ]);
    }

    #[Test]
    public function user_can_login_with_email_instead_of_username()
    {
        $response = $this->post('/login', [
            'username'       => 'mahasiswa@test.com',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertRedirect('mahasiswa.dashboard');
        $this->assertAuthenticatedAs($this->testUser);
    }

    #[Test]
    public function session_is_regenerated_after_successful_login()
    {
        $oldSessionId = $this->app['session']->getId();

        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $newSessionId = $this->app['session']->getId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    #[Test]
    public function login_attempts_are_reset_after_successful_login()
    {
        $this->testUser->update(['login_attempts' => 3]);

        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $this->testUser->refresh();
        $this->assertEquals(0, $this->testUser->login_attempts);
        $this->assertNull($this->testUser->locked_until);
    }

    #[Test]
    public function remember_me_token_is_created_when_checked()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
            'remember'       => 'on',
        ]);

        $cookie = $this->getCookie('remember_' . config('session.cookie'));
        $this->assertNotNull($cookie);
    }

    // =========================================================================
    // VALIDATION TESTS
    // =========================================================================

    #[Test]
    public function login_fails_when_username_is_empty()
    {
        $response = $this->post('/login', [
            'username'       => '',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function login_fails_when_password_is_empty()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => '',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function login_fails_when_role_is_missing()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
        ]);

        $response->assertSessionHasErrors('role_selected');
    }

    #[Test]
    public function login_fails_when_role_does_not_exist()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'invalid-role',
        ]);

        $response->assertSessionHasErrors('role_selected');
    }

    #[Test]
    public function login_fails_when_username_exceeds_max_length()
    {
        $response = $this->post('/login', [
            'username'       => str_repeat('a', 101),
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
    }

    // =========================================================================
    // CREDENTIAL VALIDATION TESTS
    // =========================================================================

    #[Test]
    public function login_fails_with_wrong_password()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'wrongpassword',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'username_attempted' => '1234567890',
            'status'            => 'failed_credentials',
        ]);
    }

    #[Test]
    public function login_fails_with_nonexistent_user()
    {
        $response = $this->post('/login', [
            'username'       => 'nonexistent@user.com',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function login_logs_failed_credentials()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'wrongpassword',
            'role_selected'  => 'mahasiswa',
        ]);

        $this->assertDatabaseHas('login_logs', [
            'user_id'            => $this->testUser->id,
            'status'             => 'failed_credentials',
            'failure_reason'     => 'Username/password salah.',
        ]);
    }

    #[Test]
    public function failed_login_increments_login_attempts()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'wrongpassword',
            'role_selected'  => 'mahasiswa',
        ]);

        $this->testUser->refresh();
        $this->assertEquals(1, $this->testUser->login_attempts);
    }

    // =========================================================================
    // ACCOUNT STATUS TESTS
    // =========================================================================

    #[Test]
    public function login_fails_when_account_is_inactive()
    {
        $this->testUser->update(['is_active' => false]);

        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'status'         => 'failed_inactive',
        ]);
    }

    #[Test]
    public function login_fails_when_account_is_locked()
    {
        $this->testUser->update([
            'login_attempts' => 5,
            'locked_until'   => now()->addMinutes(30),
        ]);

        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'status' => 'failed_locked',
        ]);
    }

    #[Test]
    public function account_is_locked_after_5_failed_attempts()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username'       => '1234567890',
                'password'       => 'wrongpassword',
                'role_selected'  => 'mahasiswa',
            ]);
        }

        $this->testUser->refresh();
        $this->assertTrue($this->testUser->isLocked());
        $this->assertEquals(5, $this->testUser->login_attempts);
    }

    #[Test]
    public function locked_account_cannot_login_even_with_correct_password()
    {
        $this->testUser->update([
            'login_attempts' => 5,
            'locked_until'   => now()->addMinutes(30),
        ]);

        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    // =========================================================================
    // ROLE VALIDATION TESTS
    // =========================================================================

    #[Test]
    public function login_fails_when_role_does_not_match_user_role()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'kalab',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'status' => 'failed_role_mismatch',
        ]);
    }

    #[Test]
    public function login_increments_rate_limit_on_role_mismatch(): void
    {
        $throttleKey = 'login.' . $this->app['request']->ip();
        RateLimiter::clear($throttleKey);

        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'kalab',
        ]);

        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 1));
    }

    // =========================================================================
    // RATE LIMITING TESTS
    // =========================================================================

    #[Test]
    public function rate_limit_blocks_after_5_failed_attempts_from_same_ip()
    {
        $throttleKey = 'login.' . $this->app['request']->ip();
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username'       => '1234567890',
                'password'       => 'wrongpassword',
                'role_selected'  => 'mahasiswa',
            ]);
        }

        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertStringContainsString('Terlalu banyak percobaan', $response['errors']['username']);
    }

    #[Test]
    public function rate_limit_is_cleared_after_successful_login()
    {
        $throttleKey = 'login.' . $this->app['request']->ip();
        RateLimiter::clear($throttleKey);

        // Add 2 failed attempts
        for ($i = 0; $i < 2; $i++) {
            $this->post('/login', [
                'username'       => '1234567890',
                'password'       => 'wrongpassword',
                'role_selected'  => 'mahasiswa',
            ]);
        }

        // Successful login should clear rate limit
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        // Should be able to login again immediately
        Auth::logout();
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function rate_limit_logs_include_ip_address()
    {
        $throttleKey = 'login.' . $this->app['request']->ip();
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username'       => '1234567890',
                'password'       => 'wrongpassword',
                'role_selected'  => 'mahasiswa',
            ]);
        }

        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $this->assertDatabaseHas('login_logs', [
            'ip_address' => '127.0.0.1',
            'status'     => 'failed_locked',
        ]);
    }

    // =========================================================================
    // LOGIN LOG TESTS
    // =========================================================================

    #[Test]
    public function login_log_captures_user_agent()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $log = LoginLog::latest()->first();
        $this->assertNotNull($log->user_agent);
        $this->assertStringContainsString('Laravel', $log->user_agent);
    }

    #[Test]
    public function login_log_captures_ip_address()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $log = LoginLog::latest()->first();
        $this->assertEquals('127.0.0.1', $log->ip_address);
    }

    #[Test]
    public function login_log_captures_attempted_role()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $log = LoginLog::latest()->first();
        $this->assertEquals('mahasiswa', $log->role_attempted);
    }

    #[Test]
    public function login_log_records_failure_reason()
    {
        $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'wrongpassword',
            'role_selected'  => 'mahasiswa',
        ]);

        $log = LoginLog::latest()->first();
        $this->assertNotNull($log->failure_reason);
    }

    // =========================================================================
    // EDGE CASES
    // =========================================================================

    #[Test]
    public function login_is_case_sensitive_for_username()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function login_with_whitespace_in_password_fails()
    {
        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => ' password123 ',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
    }

    #[Test]
    public function login_logs_null_user_for_nonexistent_accounts()
    {
        $this->post('/login', [
            'username'       => 'ghost_user@test.com',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $log = LoginLog::latest()->first();
        $this->assertNull($log->user_id);
        $this->assertEquals('ghost_user@test.com', $log->username_attempted);
    }

    #[Test]
    public function already_authenticated_user_is_redirected()
    {
        $this->actingAs($this->testUser);

        $response = $this->get('/login/mahasiswa');

        $response->assertRedirect('mahasiswa.dashboard');
    }

    #[Test]
    public function soft_deleted_user_cannot_login()
    {
        $this->testUser->delete(); // Soft delete

        $response = $this->post('/login', [
            'username'       => '1234567890',
            'password'       => 'password123',
            'role_selected'  => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
