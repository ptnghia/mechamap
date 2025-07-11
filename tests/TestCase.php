<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed test data
        $this->seed(\Database\Seeders\TestDataSeeder::class);
    }

    /**
     * Create a test user with specific role
     */
    protected function createUser(string $role = 'member', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * Create a business user with business information
     */
    protected function createBusinessUser(string $role = 'manufacturer', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'role_group' => 'business_partners',
            'email_verified_at' => now(),
            'company_name' => $this->faker->company,
            'business_license' => $this->faker->uuid,
            'tax_code' => $this->faker->numerify('##########'),
            'business_description' => $this->faker->paragraph,
            'business_phone' => $this->faker->phoneNumber,
            'business_email' => $this->faker->companyEmail,
            'business_address' => $this->faker->address,
            'is_verified_business' => false,
        ], $attributes));
    }

    /**
     * Create a verified business user
     */
    protected function createVerifiedBusinessUser(string $role = 'manufacturer', array $attributes = []): User
    {
        return $this->createBusinessUser($role, array_merge([
            'is_verified_business' => true,
            'verified_at' => now(),
            'verified_by' => 1,
        ], $attributes));
    }

    /**
     * Create an admin user
     */
    protected function createAdmin(string $role = 'super_admin', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'role_group' => 'system_management',
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * Assert that user has specific role
     */
    protected function assertUserHasRole(User $user, string $role): void
    {
        $this->assertEquals($role, $user->role);
    }

    /**
     * Assert that user has marketplace permission
     */
    protected function assertUserCanAccessMarketplace(User $user): void
    {
        $this->assertTrue($user->hasPermission('access-marketplace'));
    }

    /**
     * Assert that business user is verified
     */
    protected function assertBusinessUserIsVerified(User $user): void
    {
        $this->assertTrue($user->is_verified_business);
        $this->assertNotNull($user->verified_at);
        $this->assertNotNull($user->verified_by);
    }
}
