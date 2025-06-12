<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $engineeringCategories = [
            'CAD Design & Modeling',
            'Manufacturing Processes',
            'Materials Engineering',
            'Mechanical Systems',
            'Automation & Robotics',
            'Quality Control',
            'Product Development',
            'Analysis & Simulation'
        ];

        $categoryName = $this->faker->randomElement($engineeringCategories);

        return [
            'name' => $categoryName,
            'slug' => Str::slug($categoryName),
            'description' => $this->faker->paragraph(3),
            'icon' => 'https://api.iconify.design/material-symbols:engineering.svg',
            'parent_id' => null, // Will be set when creating sub-categories
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
            'commission_rate' => $this->faker->randomFloat(2, 5.00, 15.00),
            'engineering_discipline' => $this->faker->randomElement(['mechanical', 'electrical', 'civil', 'software']),
            'required_software' => [
                $this->faker->randomElement(['SolidWorks', 'AutoCAD', 'Fusion 360', 'Inventor']),
                $this->faker->randomElement(['ANSYS', 'MATLAB', 'LabVIEW', 'PTC Creo'])
            ],
            'product_count' => $this->faker->numberBetween(0, 50),
            'total_sales' => $this->faker->numberBetween(0, 500),
        ];
    }

    /**
     * Create a mechanical engineering category
     */
    public function mechanical(): static
    {
        return $this->state(fn (array $attributes) => [
            'engineering_discipline' => 'mechanical',
            'required_software' => ['SolidWorks', 'AutoCAD', 'ANSYS'],
            'icon' => 'https://api.iconify.design/material-symbols:precision-manufacturing.svg'
        ]);
    }

    /**
     * Create a CAD-focused category
     */
    public function cad(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'CAD Design & Modeling',
            'slug' => 'cad-design-modeling',
            'description' => 'Computer-Aided Design files, 3D models, and technical drawings',
            'engineering_discipline' => 'mechanical',
            'required_software' => ['SolidWorks', 'AutoCAD', 'Fusion 360', 'Inventor'],
            'icon' => 'https://api.iconify.design/file-icons:solidworks.svg'
        ]);
    }

    /**
     * Create a manufacturing category
     */
    public function manufacturing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Manufacturing Processes',
            'slug' => 'manufacturing-processes',
            'description' => 'CNC programming, machining guides, and production documentation',
            'engineering_discipline' => 'mechanical',
            'required_software' => ['Mastercam', 'SolidCAM', 'PowerMill'],
            'icon' => 'https://api.iconify.design/material-symbols:manufacturing.svg'
        ]);
    }

    /**
     * Create a parent category with children
     */
    public function withChildren(int $childCount = 3): static
    {
        return $this->afterCreating(function (ProductCategory $category) use ($childCount) {
            ProductCategory::factory()
                ->count($childCount)
                ->create([
                    'parent_id' => $category->id,
                    'engineering_discipline' => $category->engineering_discipline,
                ]);
        });
    }

    /**
     * Create an inactive category
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
