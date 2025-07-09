<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MarketplaceSeller;
use Illuminate\Support\Str;

class CompanyDirectorySeeder extends Seeder
{
    /**
     * Seed marketplace sellers for business users who don't have seller records
     */
    public function run(): void
    {
        $this->command->info('🏭 Seeding Company Directory Data...');

        // Lấy business users chưa có seller record
        $businessUsers = User::whereIn('role', ['manufacturer', 'supplier', 'brand', 'verified_partner'])
                            ->whereDoesntHave('marketplaceSeller')
                            ->get();

        $this->command->info("Found {$businessUsers->count()} business users without seller records");

        $businessAddresses = [
            [
                'street' => 'Industrial Zone A',
                'city' => 'Ho Chi Minh City',
                'state' => 'Ho Chi Minh',
                'country' => 'Vietnam',
                'postal_code' => '70000'
            ],
            [
                'street' => 'Manufacturing District',
                'city' => 'Binh Duong',
                'state' => 'Binh Duong',
                'country' => 'Vietnam',
                'postal_code' => '75000'
            ],
            [
                'street' => 'Technology Park',
                'city' => 'Hanoi',
                'state' => 'Hanoi',
                'country' => 'Vietnam',
                'postal_code' => '10000'
            ],
            [
                'street' => 'Export Processing Zone',
                'city' => 'Da Nang',
                'state' => 'Da Nang',
                'country' => 'Vietnam',
                'postal_code' => '50000'
            ]
        ];

        $specializations = [
            'manufacturer' => [
                ['CNC Machining', 'Precision Manufacturing', 'Quality Control'],
                ['Metal Fabrication', 'Welding Services', 'Assembly'],
                ['Injection Molding', 'Tooling Design', 'Prototyping'],
                ['Industrial Automation', 'Custom Manufacturing', 'Engineering Design']
            ],
            'supplier' => [
                ['Raw Materials', 'Steel Supply', 'Metal Trading'],
                ['Industrial Components', 'Fasteners', 'Hardware'],
                ['Hydraulic Systems', 'Pneumatic Components', 'Valves'],
                ['Electrical Components', 'Sensors', 'Control Systems']
            ],
            'brand' => [
                ['Brand Management', 'Market Research', 'Product Development'],
                ['Marketing Strategy', 'Brand Consulting', 'Market Analysis'],
                ['Product Innovation', 'Brand Development', 'Market Intelligence'],
                ['Strategic Planning', 'Brand Positioning', 'Market Expansion']
            ],
            'verified_partner' => [
                ['Premium Services', 'Verified Solutions', 'Enterprise Support'],
                ['Advanced Manufacturing', 'Premium Quality', 'Certified Processes'],
                ['Elite Partnership', 'Premium Support', 'Verified Excellence'],
                ['Strategic Partnership', 'Premium Solutions', 'Verified Quality']
            ]
        ];

        foreach ($businessUsers as $index => $user) {
            $addressIndex = $index % count($businessAddresses);
            $address = $businessAddresses[$addressIndex];
            
            $roleSpecializations = $specializations[$user->role] ?? $specializations['supplier'];
            $specializationIndex = $index % count($roleSpecializations);
            $userSpecializations = $roleSpecializations[$specializationIndex];

            // Tạo business name dựa trên user name
            $businessName = $this->generateBusinessName($user->name, $user->role);
            
            $seller = MarketplaceSeller::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'seller_type' => $this->mapRoleToSellerType($user->role),
                'business_type' => $this->mapRoleToBusinessType($user->role),
                'business_name' => $businessName,
                'business_registration_number' => 'REG-' . strtoupper(Str::random(8)),
                'tax_identification_number' => 'TAX-' . rand(1000000000, 9999999999),
                'business_description' => $this->generateBusinessDescription($user->role, $businessName),
                'contact_person_name' => $user->name,
                'contact_email' => $user->email,
                'contact_phone' => '+84 ' . rand(20, 99) . ' ' . rand(1000000, 9999999),
                'business_address' => $address,
                'website_url' => 'https://' . Str::slug($businessName) . '.com.vn',
                'industry_categories' => $this->getIndustryCategories($user->role),
                'specializations' => $userSpecializations,
                'certifications' => $this->getCertifications($user->role),
                'capabilities' => $this->getCapabilities($user->role),
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verified_by' => 1, // Admin user
                'verification_notes' => 'Auto-verified during seeding',
                'rating_average' => round(rand(35, 50) / 10, 1), // 3.5 - 5.0
                'rating_count' => rand(15, 150),
                'total_sales' => rand(50, 500),
                'total_revenue' => rand(100000, 5000000),
                'total_products' => rand(10, 100),
                'active_products' => rand(5, 50),
                'commission_rate' => 5.0,
                'processing_time_days' => rand(2, 7),
                'status' => 'active',
                'is_featured' => $user->role === 'verified_partner' || rand(1, 5) === 1,
                'store_name' => $businessName . ' Store',
                'store_slug' => Str::slug($businessName . '-store'),
                'store_description' => 'Official store of ' . $businessName,
            ]);

            $this->command->info("✅ Created seller record for: {$user->name} ({$user->role}) -> {$businessName}");
        }

        $this->command->info('🎉 Company Directory seeding completed!');
        $this->command->info('📊 Total marketplace sellers: ' . MarketplaceSeller::count());
        $this->command->info('✅ Verified sellers: ' . MarketplaceSeller::where('verification_status', 'verified')->count());
    }

    private function generateBusinessName(string $userName, string $role): string
    {
        $suffixes = [
            'manufacturer' => ['Manufacturing', 'Industries', 'Corporation', 'Manufacturing Co.'],
            'supplier' => ['Supply Co.', 'Trading', 'Distribution', 'Supply Chain'],
            'brand' => ['Brand', 'Group', 'International', 'Global'],
            'verified_partner' => ['Solutions', 'Technologies', 'Systems', 'Enterprises']
        ];

        $roleSuffixes = $suffixes[$role] ?? $suffixes['supplier'];
        $suffix = $roleSuffixes[array_rand($roleSuffixes)];

        // Nếu user name đã có business suffix, giữ nguyên
        if (str_contains($userName, 'Co.') || str_contains($userName, 'Corp') || str_contains($userName, 'Ltd')) {
            return $userName;
        }

        return $userName . ' ' . $suffix;
    }

    private function mapRoleToSellerType(string $role): string
    {
        return match($role) {
            'manufacturer' => 'manufacturer',
            'supplier' => 'supplier',
            'brand' => 'supplier',
            'verified_partner' => 'manufacturer',
            default => 'supplier'
        };
    }

    private function mapRoleToBusinessType(string $role): string
    {
        return match($role) {
            'manufacturer' => 'corporation',
            'supplier' => 'company',
            'brand' => 'company',
            'verified_partner' => 'corporation',
            default => 'company'
        };
    }

    private function generateBusinessDescription(string $role, string $businessName): string
    {
        $descriptions = [
            'manufacturer' => "Leading manufacturing company specializing in precision engineering and industrial solutions. {$businessName} provides high-quality manufacturing services with advanced technology and experienced team.",
            'supplier' => "Reliable supplier of industrial materials and components. {$businessName} offers comprehensive supply chain solutions for mechanical engineering industry.",
            'brand' => "Established brand in mechanical engineering sector. {$businessName} focuses on innovation and quality products for industrial applications.",
            'verified_partner' => "Premium verified partner providing enterprise-grade solutions. {$businessName} delivers exceptional quality and service excellence."
        ];

        return $descriptions[$role] ?? $descriptions['supplier'];
    }

    private function getIndustryCategories(string $role): array
    {
        $categories = [
            'manufacturer' => ['Manufacturing', 'Industrial Equipment', 'Precision Engineering'],
            'supplier' => ['Raw Materials', 'Industrial Supply', 'Components'],
            'brand' => ['Brand Management', 'Product Development', 'Market Research'],
            'verified_partner' => ['Premium Services', 'Enterprise Solutions', 'Consulting']
        ];

        return $categories[$role] ?? $categories['supplier'];
    }

    private function getCertifications(string $role): array
    {
        return [
            'ISO 9001:2015',
            'ISO 14001:2015',
            'OHSAS 18001',
            'CE Marking',
            'Vietnam Quality Certification'
        ];
    }

    private function getCapabilities(string $role): array
    {
        $capabilities = [
            'manufacturer' => ['CNC Machining', 'Welding', 'Assembly', 'Quality Control', 'Design Engineering'],
            'supplier' => ['Inventory Management', 'Logistics', 'Quality Assurance', 'Technical Support'],
            'brand' => ['Market Analysis', 'Product Development', 'Brand Strategy', 'Customer Relations'],
            'verified_partner' => ['Premium Support', 'Enterprise Solutions', 'Consulting', 'Training']
        ];

        return $capabilities[$role] ?? $capabilities['supplier'];
    }
}
