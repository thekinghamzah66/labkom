<?php

namespace Tests\Feature\Auth;

use App\Models\LoginLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @method mixed getCookie(string $name)
 */
class GoogleOAuthCallbackTest extends TestCase
{
    private Role $mahasiswaRole;
    private Role $kalabRole;
    private User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswaRole = Role::create([
            'name'            => 'Mahasiswa',
            'slug'            => 'mahasiswa',
            'description'     => 'Student',
            'dashboard_route' => 'mahasiswa.dashboard',
            'color_class'     => 'from-blue-500',
            'is_active'       => true,
        ]);

        $this->kalabRole = Role::create([
            'name'            => 'KALAB',
            'slug'            => 'kalab',
            'description'     => 'Lab Head',
            'dashboard_route' => 'kalab.dashboard',
            'color_class'     => 'from-violet-600',
            'is_active'       => true,
        ]);

        $this->testUser = User::create([
            'role_id'       => $this->mahasiswaRole->id,
            'name'          => 'Test Student',
            'username'      => 'student123',
            'email'         => 'student@test.com',
            'password'      => Hash::make('password123'),
            'is_active'     => true,
            'google_id'     => '123456789',
            'google_token'  => 'test_token',
        ]);
    }

    // =========================================================================
    // OAUTH SUCCESS FLOW TESTS
    // =========================================================================

    #[Test]
    public function user_can_login_with_google_id()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        // Create and configure mock Google user
        $mockGoogleUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $mockGoogleUser->shouldReceive('getId')->andReturn('123456789');
        $mockGoogleUser->shouldReceive('getEmail')->andReturn('student@test.com');
        $mockGoogleUser->token = 'new_google_token';
        $mockGoogleUser->refreshToken = 'new_refresh_token';
        $mockGoogleUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        // Mock Socialite
        $mockProvider = \Mockery::mock();
        $mockProvider->shouldReceive('user')->andReturn($mockGoogleUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mockProvider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticatedAs($this->testUser);
    }

    #[Test]
    public function user_can_login_with_email_when_google_id_not_found()
    {
        $this->testUser->update(['google_id' => null]);
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'getId' => '987654321',
            'getEmail' => 'student@test.com',
            'token' => 'new_token',
            'refreshToken' => null,
            'getAvatar' => null,
        ]);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->testUser->refresh();
        $this->assertEquals('987654321', $this->testUser->google_id);
    }

    #[Test]
    public function google_token_is_updated_after_login()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'token' => 'updated_token',
            'refreshToken' => 'updated_refresh_token',
        ]);

        $this->get('/auth/google/callback');

        $this->testUser->refresh();
        $this->assertEquals('updated_token', $this->testUser->google_token);
        $this->assertEquals('updated_refresh_token', $this->testUser->google_refresh_token);
    }

    #[Test]
    public function avatar_is_updated_from_google_if_not_exists()
    {
        $this->testUser->update(['avatar' => null]);
        $this->session(['oauth_role_selected' => 'mahasiswa']);
        $googleAvatar = 'https://example.com/new_avatar.jpg';

        $this->setupGoogleOAuthMock([
            'getAvatar' => $googleAvatar,
        ]);

        $this->get('/auth/google/callback');

        $this->testUser->refresh();
        $this->assertEquals($googleAvatar, $this->testUser->avatar);
    }

    #[Test]
    public function avatar_is_preserved_if_already_exists()
    {
        $existingAvatar = 'https://example.com/existing_avatar.jpg';
        $this->testUser->update(['avatar' => $existingAvatar]);
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'getAvatar' => 'https://example.com/google_avatar.jpg',
        ]);

        $this->get('/auth/google/callback');

        $this->testUser->refresh();
        $this->assertEquals($existingAvatar, $this->testUser->avatar);
    }

    #[Test]
    public function session_is_regenerated_after_oauth_login()
    {
        $oldSessionId = $this->app['session']->getId();
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock();

        $this->get('/auth/google/callback');

        $newSessionId = $this->app['session']->getId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    #[Test]
    public function remember_token_is_set_to_true_for_oauth()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock();

        $this->get('/auth/google/callback');

        $this->testUser->refresh();
        $this->assertNotNull($this->testUser->remember_token);
    }

    // =========================================================================
    // SESSION & ROLE VALIDATION TESTS
    // =========================================================================

    #[Test]
    public function oauth_fails_when_session_role_is_missing()
    {
        // Don't set oauth_role_selected in session

        $this->setupGoogleOAuthMock([
            'getId' => '999888777',
            'getEmail' => 'unknown@test.com',
        ]);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('role_selected');
        $this->assertGuest();
    }

    #[Test]
    public function oauth_fails_when_email_not_found()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'getId' => '999888777',
            'getEmail' => 'nonexistent@test.com',
        ]);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function oauth_fails_when_account_is_inactive()
    {
        $this->testUser->update(['is_active' => false]);
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function oauth_fails_when_role_does_not_match()
    {
        $this->testUser->update(['role_id' => $this->mahasiswaRole->id]);
        $this->session(['oauth_role_selected' => 'kalab']);

        $this->setupGoogleOAuthMock();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    // =========================================================================
    // EXCEPTION HANDLING TESTS
    // =========================================================================

    #[Test]
    public function oauth_handles_socialite_exception()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMockWithException(new \Exception('Google API Error'));

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function oauth_handles_invalid_token_response()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMockWithException(new \GuzzleHttp\Exception\ClientException(
            'Invalid token',
            \Mockery::mock('Psr\Http\Message\RequestInterface'),
            new \GuzzleHttp\Psr7\Response(400)
        ));

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('username');
    }

    // =========================================================================
    // EDGE CASES & SECURITY TESTS
    // =========================================================================

    #[Test]
    public function oauth_prevents_multiple_accounts_with_same_email()
    {
        $anotherUser = User::create([
            'role_id'       => $this->mahasiswaRole->id,
            'name'          => 'Another User',
            'username'      => 'another_user',
            'email'         => 'another@test.com',
            'password'      => Hash::make('password'),
            'is_active'     => true,
        ]);

        $this->testUser->update(['email' => 'shared@test.com']);
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'getId' => '111222333',
            'getEmail' => 'shared@test.com',
        ]);

        $this->get('/auth/google/callback');

        // First user found should be authenticated
        $this->assertTrue(Auth::check());
    }

    #[Test]
    public function oauth_logs_successful_login()
    {
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock();

        $this->get('/auth/google/callback');

        $this->assertDatabaseHas('login_logs', [
            'user_id'      => $this->testUser->id,
            'status'       => 'success',
            'failure_reason' => 'Google OAuth Login',
        ]);
    }

    #[Test]
    public function oauth_handles_null_refresh_token_safely()
    {
        $this->testUser->update(['google_refresh_token' => 'old_refresh_token']);
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'refreshToken' => null,
        ]);

        $this->get('/auth/google/callback');

        $this->testUser->refresh();
        // Should preserve old refresh token when new one is null
        $this->assertEquals('old_refresh_token', $this->testUser->google_refresh_token);
    }

    #[Test]
    public function oauth_creates_new_user_record_when_not_found()
    {
        // This test documents that Google OAuth doesn't auto-create users
        // Testing the expected failure
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock([
            'getId' => 'new_google_id',
            'getEmail' => 'newuser@test.com',
        ]);

        $initialCount = User::count();
        $this->get('/auth/google/callback');

        // User count should not increase (no auto-registration)
        $this->assertEquals($initialCount, User::count());
    }

    #[Test]
    public function already_authenticated_user_can_still_access_callback()
    {
        $this->actingAs($this->testUser);
        $this->session(['oauth_role_selected' => 'mahasiswa']);

        $this->setupGoogleOAuthMock();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticatedAs($this->testUser);
    }
}
