<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_id'              => Role::where('slug', 'mahasiswa')->first()?->id ?? Role::first()?->id,
            'name'                 => fake()->name(),
            'username'             => fake()->unique()->numerify('##########'), // NIM 10 digit
            'email'                => fake()->unique()->safeEmail(),
            'password'             => 'password', // auto-hashed via cast
            'avatar'               => null,
            'google_id'            => null,
            'google_token'         => null,
            'google_refresh_token' => null,
            'is_active'            => true,
            'email_verified_at'    => now(),
            'login_attempts'       => 0,
            'locked_until'         => null,
            'remember_token'       => Str::random(10),
        ];
    }

    // -------------------------------------------------------------------------
    // States — per Role
    // -------------------------------------------------------------------------

    public function kalab(): static
    {
        return $this->state(fn() => [
            'role_id'  => Role::where('slug', 'kalab')->first()?->id,
            'username' => fake()->unique()->bothify('KALAB-####'),
        ]);
    }

    public function aslab(): static
    {
        return $this->state(fn() => [
            'role_id'  => Role::where('slug', 'aslab')->first()?->id,
            'username' => fake()->unique()->bothify('ASLAB-####'),
        ]);
    }

    public function dosenPembimbing(): static
    {
        return $this->state(fn() => [
            'role_id'  => Role::where('slug', 'dosen-pembimbing')->first()?->id,
            'username' => fake()->unique()->numerify('NIP##########'),
        ]);
    }

    // -------------------------------------------------------------------------
    // States — Account Status
    // -------------------------------------------------------------------------

    public function inactive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn() => [
            'login_attempts' => 5,
            'locked_until'   => now()->addMinutes(30),
        ]);
    }

    public function withGoogle(): static
    {
        return $this->state(fn() => [
            'google_id'    => fake()->unique()->numerify('####################'),
            'google_token' => Str::random(100),
            'password'     => null, // OAuth-only user
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn() => [
            'email_verified_at' => null,
        ]);
    }
}
