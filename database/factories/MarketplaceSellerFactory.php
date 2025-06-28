<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketplaceSeller>
 */
class MarketplaceSellerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessName = $this->faker->company;
        $storeName = $businessName . ' Store';

        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'seller_type' => $this->faker->randomElement(['supplier', 'manufacturer', 'brand']),
            'business_type' => $this->faker->randomElement(['individual', 'company', 'partnership']),
            'business_name' => $businessName,
            'business_registration_number' => $this->faker->numerify('##########'),
            'tax_identification_number' => $this->faker->numerify('##########'),
            'business_description' => $this->faker->paragraph,
            'contact_person_name' => $this->faker->name,
            'contact_email' => $this->faker->email,
            'contact_phone' => $this->faker->phoneNumber,
            'business_address' => [
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'state' => $this->faker->state,
                'postal_code' => $this->faker->postcode,
                'country' => $this->faker->country,
            ],
            'website_url' => $this->faker->url,
            'store_name' => $storeName,
            'store_slug' => Str::slug($storeName),
            'store_description' => $this->faker->paragraph,
            'store_logo' => null,
            'store_banner' => null,
            'industry_categories' => $this->faker->randomElements([
                'mechanical_parts',
                'tools',
                'materials',
                'electronics',
                'safety'
            ], $this->faker->numberBetween(1, 3)),
            'specializations' => $this->faker->randomElements([
                'CNC Machining',
                'Injection Molding',
                'Metal Fabrication',
                'Electronics Assembly',
                'Quality Control'
            ], $this->faker->numberBetween(1, 2)),
            'certifications' => $this->faker->randomElements([
                'ISO 9001',
                'ISO 14001',
                'CE Marking',
                'UL Listed'
            ], $this->faker->numberBetween(0, 2)),
            'capabilities' => [
                'production_capacity' => $this->faker->numberBetween(100, 10000),
                'lead_time_days' => $this->faker->numberBetween(1, 30),
                'minimum_order_quantity' => $this->faker->numberBetween(1, 100),
            ],
            'verification_status' => $this->faker->randomElement(['pending', 'verified', 'rejected']),
            'verified_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'verified_by' => null,
            'verification_documents' => [],
            'verification_notes' => $this->faker->optional()->sentence,
            'rating_average' => $this->faker->randomFloat(2, 0, 5),
            'rating_count' => $this->faker->numberBetween(0, 100),
            'total_sales' => $this->faker->numberBetween(0, 1000),
            'total_revenue' => $this->faker->randomFloat(2, 0, 100000),
            'total_products' => $this->faker->numberBetween(0, 50),
            'active_products' => $this->faker->numberBetween(0, 30),
            'commission_rate' => $this->faker->randomFloat(2, 3, 10),
            'pending_earnings' => $this->faker->randomFloat(2, 0, 5000),
            'available_earnings' => $this->faker->randomFloat(2, 0, 10000),
            'total_earnings' => $this->faker->randomFloat(2, 0, 50000),
            'payment_methods' => [
                'bank_transfer' => true,
                'paypal' => $this->faker->boolean,
                'stripe' => $this->faker->boolean,
            ],
            'auto_approve_orders' => $this->faker->boolean,
            'processing_time_days' => $this->faker->numberBetween(1, 7),
            'shipping_methods' => [
                [
                    'name' => 'Standard Shipping',
                    'cost' => $this->faker->randomFloat(2, 5, 20),
                    'estimated_days' => $this->faker->numberBetween(3, 7),
                ],
                [
                    'name' => 'Express Shipping',
                    'cost' => $this->faker->randomFloat(2, 15, 50),
                    'estimated_days' => $this->faker->numberBetween(1, 3),
                ],
            ],
            'return_policy' => [
                'accepts_returns' => $this->faker->boolean,
                'return_period_days' => $this->faker->numberBetween(7, 30),
                'return_conditions' => $this->faker->sentence,
            ],
            'terms_conditions' => [
                'terms_of_service' => $this->faker->paragraph,
                'privacy_policy' => $this->faker->paragraph,
                'warranty_policy' => $this->faker->paragraph,
            ],
            'store_settings' => [
                'notifications' => [
                    'email_new_orders' => true,
                    'email_order_updates' => true,
                    'email_low_stock' => $this->faker->boolean,
                    'sms_new_orders' => $this->faker->boolean,
                ],
                'display_settings' => [
                    'show_contact_info' => $this->faker->boolean,
                    'show_business_hours' => $this->faker->boolean,
                ],
            ],
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),
            'is_featured' => $this->faker->boolean(0.1), // 10% chance
            'last_active_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'suspended_at' => null,
            'suspension_reason' => null,
        ];
    }

    /**
     * Indicate that the seller is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verified_at' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the seller is pending verification.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending',
            'verified_at' => null,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the seller is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $this->faker->sentence,
        ]);
    }

    /**
     * Create a supplier seller.
     */
    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'seller_type' => 'supplier',
            'commission_rate' => 5.0,
        ]);
    }

    /**
     * Create a manufacturer seller.
     */
    public function manufacturer(): static
    {
        return $this->state(fn (array $attributes) => [
            'seller_type' => 'manufacturer',
            'commission_rate' => 3.0,
        ]);
    }

    /**
     * Create a brand seller.
     */
    public function brand(): static
    {
        return $this->state(fn (array $attributes) => [
            'seller_type' => 'brand',
            'commission_rate' => 0.0,
        ]);
    }
}
