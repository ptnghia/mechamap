<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'member',
            'role_group' => 'community_members',
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a business user with business information
     */
    public function business(string $role = 'manufacturer'): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => $role,
            'role_group' => 'business_partners',
            'company_name' => fake()->company(),
            'business_license' => 'BL-' . fake()->unique()->numerify('######'),
            'tax_code' => fake()->numerify('##########'),
            'business_description' => fake()->paragraph(),
            'business_categories' => json_encode(fake()->randomElements(['automotive', 'aerospace', 'manufacturing', 'materials'], 2)),
            'business_phone' => fake()->phoneNumber(),
            'business_email' => fake()->companyEmail(),
            'business_address' => fake()->address(),
            'is_verified_business' => false,
        ]);
    }

    /**
     * Create a verified business user
     */
    public function verifiedBusiness(string $role = 'manufacturer'): static
    {
        return $this->business($role)->state(fn(array $attributes) => [
            'is_verified_business' => true,
            'verified_at' => now(),
            'verified_by' => 1,
            'verification_notes' => 'Auto-verified for testing',
        ]);
    }

    /**
     * Create an admin user
     */
    public function admin(string $role = 'super_admin'): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => $role,
            'role_group' => 'system_management',
        ]);
    }

    /**
     * Create a moderator user
     */
    public function moderator(string $role = 'content_moderator'): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => $role,
            'role_group' => 'community_management',
        ]);
    }

    /**
     * Create a community member
     */
    public function member(string $role = 'member'): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => $role,
            'role_group' => 'community_members',
        ]);
    }
}
