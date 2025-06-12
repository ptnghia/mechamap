<?php

namespace Database\Factories;

use App\Models\TechnicalProduct;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TechnicalProductFactory extends Factory
{
    protected $model = TechnicalProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $productTitles = [
            'Gear Box Assembly Design for Automotive Applications',
            'CNC Machining Program for Precision Components',
            'Hydraulic System Design with Flow Analysis',
            'Robot Arm Kinematics and Control Documentation',
            'Bearing Selection and Life Calculation Spreadsheet',
            'Weld Joint Design and Stress Analysis',
            'Pump Impeller Design with CFD Analysis',
            'Motor Mount Bracket - CAD Files and Drawings',
            'Conveyor Belt System Design Package',
            'Heat Exchanger Thermal Analysis Model',
        ];

        $title = $this->faker->randomElement($productTitles);
        $slug = Str::slug($title);

        return [
            'seller_id' => User::factory(),
            'category_id' => ProductCategory::factory(),
            'title' => $title,
            'slug' => $slug,
            'description' => $this->generateTechnicalDescription(),
            'short_description' => $this->faker->sentence(15),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'VND']),
            'discount_percentage' => $this->faker->optional(0.3)->randomFloat(2, 5, 30),
            'tags' => $this->generateTags(),
            'software_compatibility' => $this->generateSoftwareCompatibility(),
            'file_formats' => $this->generateFileFormats(),
            'complexity_level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'industry_applications' => $this->generateIndustryApplications(),
            'preview_images' => $this->generatePreviewImages(),
            'sample_files' => $this->generateSampleFiles(),
            'protected_files' => [], // Will be populated when files are uploaded
            'documentation_files' => $this->generateDocumentationFiles(),
            'view_count' => $this->faker->numberBetween(0, 1000),
            'download_count' => $this->faker->numberBetween(0, 500),
            'sales_count' => $this->faker->numberBetween(0, 100),
            'total_revenue' => $this->faker->randomFloat(2, 0, 5000),
            'rating_average' => $this->faker->randomFloat(2, 3.0, 5.0),
            'rating_count' => $this->faker->numberBetween(0, 50),
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved']),
            'is_featured' => $this->faker->boolean(10), // 10% chance
            'is_bestseller' => $this->faker->boolean(5), // 5% chance
            'meta_title' => $title,
            'meta_description' => $this->faker->sentence(20),
            'keywords' => implode(', ', $this->generateKeywords()),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Generate technical description
     */
    private function generateTechnicalDescription(): string
    {
        $descriptions = [
            "This comprehensive design package includes detailed CAD models, engineering drawings, and calculation sheets for industrial applications. All files are professionally prepared and ready for manufacturing.",
            "Complete engineering solution with step-by-step documentation, material specifications, and quality control guidelines. Includes finite element analysis results and optimization recommendations.",
            "Professional-grade technical documentation with detailed assembly instructions, bill of materials, and supplier recommendations. Suitable for both educational and commercial use.",
            "Advanced engineering design with comprehensive analysis results, including stress analysis, thermal calculations, and performance optimization data.",
        ];

        return $this->faker->randomElement($descriptions) . "\n\n" .
               "Technical Specifications:\n" .
               "• Material: " . $this->faker->randomElement(['Steel AISI 1045', 'Aluminum 6061', 'Stainless Steel 316']) . "\n" .
               "• Operating Temperature: " . $this->faker->numberBetween(-20, 200) . "°C\n" .
               "• Safety Factor: " . $this->faker->randomFloat(1, 2.0, 5.0) . "\n" .
               "• Standards Compliance: " . $this->faker->randomElement(['ISO 9001', 'ASME BPVC', 'DIN Standards']);
    }

    /**
     * Generate tags
     */
    private function generateTags(): array
    {
        $availableTags = [
            'CAD', 'SolidWorks', 'AutoCAD', 'Mechanical', 'Engineering', 'Design',
            'Manufacturing', 'CNC', 'FEA', 'CFD', 'Simulation', 'Analysis',
            'Automotive', 'Aerospace', 'Industrial', 'Robotics', 'Automation'
        ];

        return $this->faker->randomElements($availableTags, $this->faker->numberBetween(3, 8));
    }

    /**
     * Generate software compatibility
     */
    private function generateSoftwareCompatibility(): array
    {
        $software = [
            'SolidWorks' => $this->faker->randomElement(['2020+', '2021+', '2022+']),
            'AutoCAD' => $this->faker->randomElement(['2019+', '2020+', '2021+']),
            'Fusion 360' => $this->faker->randomElement(['Latest', '2.0.9313+']),
        ];

        return $this->faker->randomElements($software, $this->faker->numberBetween(1, 3), false);
    }

    /**
     * Generate file formats
     */
    private function generateFileFormats(): array
    {
        $formats = ['dwg', 'step', 'iges', 'stl', 'pdf', 'docx', 'xlsx'];
        return $this->faker->randomElements($formats, $this->faker->numberBetween(2, 5));
    }

    /**
     * Generate industry applications
     */
    private function generateIndustryApplications(): array
    {
        $industries = ['automotive', 'aerospace', 'manufacturing', 'robotics', 'energy', 'medical'];
        return $this->faker->randomElements($industries, $this->faker->numberBetween(1, 3));
    }

    /**
     * Generate preview images URLs
     */
    private function generatePreviewImages(): array
    {
        $imageCount = $this->faker->numberBetween(2, 6);
        $images = [];

        for ($i = 0; $i < $imageCount; $i++) {
            $images[] = 'https://picsum.photos/800/600?random=' . $this->faker->numberBetween(1, 1000);
        }

        return $images;
    }

    /**
     * Generate sample files
     */
    private function generateSampleFiles(): array
    {
        return [
            [
                'name' => 'Sample Drawing.pdf',
                'url' => '/storage/samples/sample_drawing.pdf',
                'size' => $this->faker->numberBetween(100000, 5000000)
            ],
            [
                'name' => 'Preview Model.step',
                'url' => '/storage/samples/preview_model.step',
                'size' => $this->faker->numberBetween(500000, 10000000)
            ]
        ];
    }

    /**
     * Generate documentation files
     */
    private function generateDocumentationFiles(): array
    {
        return [
            [
                'name' => 'Assembly Instructions.pdf',
                'url' => '/storage/docs/assembly_instructions.pdf',
                'type' => 'manual'
            ],
            [
                'name' => 'Material Specifications.xlsx',
                'url' => '/storage/docs/material_specs.xlsx',
                'type' => 'specification'
            ]
        ];
    }

    /**
     * Generate keywords
     */
    private function generateKeywords(): array
    {
        $keywords = [
            'mechanical design', 'CAD modeling', 'engineering analysis',
            'technical drawing', 'manufacturing', 'automation',
            'precision engineering', 'industrial design'
        ];

        return $this->faker->randomElements($keywords, $this->faker->numberBetween(3, 6));
    }

    /**
     * Create approved product
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Create featured product
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'featured_until' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => 'approved',
        ]);
    }

    /**
     * Create bestseller product
     */
    public function bestseller(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_bestseller' => true,
            'sales_count' => $this->faker->numberBetween(100, 500),
            'rating_average' => $this->faker->randomFloat(2, 4.0, 5.0),
            'rating_count' => $this->faker->numberBetween(20, 100),
            'status' => 'approved',
        ]);
    }

    /**
     * Create free product
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'discount_percentage' => 0,
        ]);
    }

    /**
     * Create CAD-focused product
     */
    public function cad(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Professional CAD Assembly - ' . $this->faker->words(3, true),
            'tags' => ['CAD', 'SolidWorks', 'Mechanical', 'Design', 'Engineering'],
            'software_compatibility' => [
                'SolidWorks' => '2021+',
                'AutoCAD' => '2020+',
                'Fusion 360' => 'Latest'
            ],
            'file_formats' => ['dwg', 'step', 'iges', 'sldprt', 'sldasm'],
            'complexity_level' => 'intermediate',
        ]);
    }
}
