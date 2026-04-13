<?php

namespace Tests\Unit\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserAccountSecurityTest extends TestCase
{
    private Role $mahasiswaRole;
    private User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswaRole = Role::create([
            'name'            => 'Mahasiswa',
            'slug'            => 'mahasiswa',
            'dashboard_route' => 'mahasiswa.dashboard',
            'is_active'       => true,
        ]);

        $this->testUser = User::create([
            'role_id'        => $this->mahasiswaRole->id,
            'name'           => 'Test User',
            'username'       => 'testuser',
            'email'          => 'test@test.com',
            'password'       => Hash::make('password123'),
            'is_active'      => true,
            'login_attempts' => 0,
        ]);
    }

    // =========================================================================
    // isLocked() Tests
    // =========================================================================

    #[Test]
    public function is_locked_returns_false_when_locked_until_is_null()
    {
        $this->testUser->update(['locked_until' => null]);

        $this->assertFalse($this->testUser->isLocked());
    }

    #[Test]
    public function is_locked_returns_false_when_locked_until_is_in_past()
    {
        $this->testUser->update(['locked_until' => now()->subMinutes(10)]);

        $this->assertFalse($this->testUser->isLocked());
    }

    #[Test]
    public function is_locked_returns_true_when_locked_until_is_in_future()
    {
        $this->testUser->update(['locked_until' => now()->addMinutes(30)]);

        $this->assertTrue($this->testUser->isLocked());
    }

    #[Test]
    public function is_locked_returns_true_when_locked_just_now()
    {
        $this->testUser->update(['locked_until' => now()]);

        $this->assertTrue($this->testUser->isLocked());
    }

    // =========================================================================
    // recordFailedLogin() Tests
    // =========================================================================

    #[Test]
    public function record_failed_login_increments_login_attempts()
    {
        $this->testUser->recordFailedLogin();
        $this->testUser->refresh();

        $this->assertEquals(1, $this->testUser->login_attempts);
    }

    #[Test]
    public function record_failed_login_increments_multiple_times()
    {
        $this->testUser->recordFailedLogin();
        $this->testUser->recordFailedLogin();
        $this->testUser->recordFailedLogin();
        $this->testUser->refresh();

        $this->assertEquals(3, $this->testUser->login_attempts);
    }

    #[Test]
    public function record_failed_login_locks_account_after_5_attempts()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->testUser->recordFailedLogin();
        }

        $this->testUser->refresh();
        $this->assertEquals(5, $this->testUser->login_attempts);
        $this->assertNotNull($this->testUser->locked_until);
        $this->assertTrue($this->testUser->locked_until->isFuture());
    }

    #[Test]
    public function record_failed_login_sets_locked_until_to_30_minutes(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->testUser->recordFailedLogin();
        }

        $this->testUser->refresh();
        $difference = now()->diffInMinutes($this->testUser->locked_until);

        // Should be approximately 30 minutes (allow 1 minute tolerance)
        $this->assertGreaterThanOrEqual(29, $difference);
        $this->assertLessThanOrEqual(31, $difference);
    }

    #[Test]
    public function record_failed_login_does_not_lock_before_5_attempts(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->testUser->recordFailedLogin();
        }

        $this->testUser->refresh();
        $this->assertFalse($this->testUser->isLocked());
        $this->assertNull($this->testUser->locked_until);
    }

    #[Test]
    public function record_failed_login_locks_on_exactly_5_attempts()
    {
        for ($i = 0; $i < 4; $i++) {
            $this->testUser->recordFailedLogin();
        }

        $this->testUser->refresh();
        $this->assertNull($this->testUser->locked_until);

        $this->testUser->recordFailedLogin();
        $this->testUser->refresh();

        $this->assertTrue($this->testUser->isLocked());
    }

    // =========================================================================
    // resetLoginAttempts() Tests
    // =========================================================================

    #[Test]
    public function reset_login_attempts_clears_attempts()
    {
        $this->testUser->update(['login_attempts' => 5]);

        $this->testUser->resetLoginAttempts();
        $this->testUser->refresh();

        $this->assertEquals(0, $this->testUser->login_attempts);
    }

    #[Test]
    public function reset_login_attempts_clears_lock()
    {
        $this->testUser->update([
            'login_attempts' => 5,
            'locked_until'   => now()->addMinutes(30),
        ]);

        $this->testUser->resetLoginAttempts();
        $this->testUser->refresh();

        $this->assertNull($this->testUser->locked_until);
        $this->assertFalse($this->testUser->isLocked());
    }

    #[Test]
    public function reset_login_attempts_resets_locked_and_unlocked_accounts()
    {
        // Test with locked account
        $this->testUser->update([
            'login_attempts' => 5,
            'locked_until'   => now()->addMinutes(30),
        ]);

        $this->testUser->resetLoginAttempts();
        $this->testUser->refresh();

        $this->assertEquals(0, $this->testUser->login_attempts);
        $this->assertNull($this->testUser->locked_until);
    }

    // =========================================================================
    // Role Check Methods Tests
    // =========================================================================

    #[Test]
    public function is_kalab_returns_true_for_kalab_user()
    {
        $kalabRole = Role::create([
            'name'            => 'KALAB',
            'slug'            => 'kalab',
            'dashboard_route' => 'kalab.dashboard',
        ]);

        $this->testUser->update(['role_id' => $kalabRole->id]);

        $this->assertTrue($this->testUser->isKalab());
    }

    #[Test]
    public function is_kalab_returns_false_for_non_kalab_user()
    {
        $this->assertFalse($this->testUser->isKalab());
    }

    #[Test]
    public function is_mahasiswa_returns_true_for_mahasiswa_user()
    {
        $this->assertTrue($this->testUser->isMahasiswa());
    }

    #[Test]
    public function is_mahasiswa_returns_false_for_non_mahasiswa_user()
    {
        $kalabRole = Role::create([
            'name'            => 'KALAB',
            'slug'            => 'kalab',
            'dashboard_route' => 'kalab.dashboard',
        ]);

        $this->testUser->update(['role_id' => $kalabRole->id]);

        $this->assertFalse($this->testUser->isMahasiswa());
    }

    #[Test]
    public function is_aslab_works_correctly()
    {
        $aslabRole = Role::create([
            'name'            => 'ASLAB',
            'slug'            => 'aslab',
            'dashboard_route' => 'aslab.dashboard',
        ]);

        $this->testUser->update(['role_id' => $aslabRole->id]);

        $this->assertTrue($this->testUser->isAslab());
    }

    #[Test]
    public function is_dosen_pembimbing_works_correctly()
    {
        $dosenRole = Role::create([
            'name'            => 'Dosen Pembimbing',
            'slug'            => 'dosen-pembimbing',
            'dashboard_route' => 'dosen-pembimbing.dashboard',
        ]);

        $this->testUser->update(['role_id' => $dosenRole->id]);

        $this->assertTrue($this->testUser->isDosenPembimbing());
    }

    #[Test]
    public function role_checks_handle_null_role_gracefully()
    {
        $userWithoutRole = $this->testUser->fresh();
        $userWithoutRole->setRelation('role', null);

        // Should not throw exception even when the loaded role relation is absent
        $this->assertFalse($userWithoutRole->isKalab());
        $this->assertFalse($userWithoutRole->isMahasiswa());
        $this->assertFalse($userWithoutRole->isAslab());
        $this->assertFalse($userWithoutRole->isDosenPembimbing());
    }

    // =========================================================================
    // OAuth Check Tests
    // =========================================================================

    #[Test]
    public function has_oauth_returns_true_when_google_id_exists()
    {
        $this->testUser->update(['google_id' => '123456789']);

        $this->assertTrue($this->testUser->hasOAuth());
    }

    #[Test]
    public function has_oauth_returns_false_when_google_id_is_null()
    {
        $this->testUser->update(['google_id' => null]);

        $this->assertFalse($this->testUser->hasOAuth());
    }

    // =========================================================================
    // Query Scope Tests
    // =========================================================================

    #[Test]
    public function scope_active_returns_only_active_users()
    {
        $activeUser = User::create([
            'role_id'   => $this->mahasiswaRole->id,
            'name'      => 'Active User',
            'username'  => 'active',
            'email'     => 'active@test.com',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);

        $inactiveUser = User::create([
            'role_id'   => $this->mahasiswaRole->id,
            'name'      => 'Inactive User',
            'username'  => 'inactive',
            'email'     => 'inactive@test.com',
            'password'  => Hash::make('password'),
            'is_active' => false,
        ]);

        $active = User::active()->get();

        $this->assertTrue($active->contains($activeUser));
        $this->assertFalse($active->contains($inactiveUser));
    }

    #[Test]
    public function scope_by_role_filters_users_by_role_slug()
    {
        $kalabRole = Role::create([
            'name'            => 'KALAB',
            'slug'            => 'kalab',
            'dashboard_route' => 'kalab.dashboard',
        ]);

        $kalabUser = User::create([
            'role_id'   => $kalabRole->id,
            'name'      => 'KALAB User',
            'username'  => 'kalab_user',
            'email'     => 'kalab@test.com',
            'password'  => Hash::make('password'),
        ]);

        $mahasiswaUsers = User::byRole('mahasiswa')->get();

        $this->assertTrue($mahasiswaUsers->contains($this->testUser));
        $this->assertFalse($mahasiswaUsers->contains($kalabUser));
    }

    #[Test]
    public function scope_for_login_finds_user_by_username_or_email()
    {
        $byUsername = User::forLogin('testuser')->first();
        $byEmail = User::forLogin('test@test.com')->first();

        $this->assertEquals($this->testUser->id, $byUsername->id);
        $this->assertEquals($this->testUser->id, $byEmail->id);
    }

    #[Test]
    public function scope_for_login_returns_null_for_nonexistent_identifier()
    {
        $user = User::forLogin('nonexistent')->first();

        $this->assertNull($user);
    }
}
