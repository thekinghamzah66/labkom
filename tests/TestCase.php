<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Socialite\Two\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * @method mixed getCookie(string $name)
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    /**
     * Setup a Google OAuth mock with optional attributes
     * @param array $attributes Array of method names and return values
     * @return mixed The configured mock Google user
     */
    protected function setupGoogleOAuthMock(array $attributes = []): mixed
    {
        $defaults = [
            'getId' => '123456789',
            'getEmail' => 'student@test.com',
            'token' => 'new_google_token',
            'refreshToken' => 'new_refresh_token',
            'getAvatar' => 'https://example.com/avatar.jpg',
        ];

        $config = array_merge($defaults, $attributes);

        $mockGoogleUser = \Mockery::mock(SocialiteUser::class);
        foreach ($config as $method => $value) {
            if (in_array($method, ['token', 'refreshToken'], true)) {
                $mockGoogleUser->{$method} = $value;
                continue;
            }

            $mockGoogleUser->shouldReceive($method)->andReturn($value);
        }

        // Setup Socialite facade mock
        $mockProvider = \Mockery::mock();
        $mockProvider->shouldReceive('user')->andReturn($mockGoogleUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mockProvider);

        return $mockGoogleUser;
    }

    /**
     * Setup exception-throwing Google OAuth mock
     * @param \Exception $exception The exception to throw
     * @return void
     */
    protected function setupGoogleOAuthMockWithException(\Exception $exception): void
    {
        $mockProvider = \Mockery::mock();
        $mockProvider->shouldReceive('user')->andThrow($exception);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mockProvider);
    }
}
