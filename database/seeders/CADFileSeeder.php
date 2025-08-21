<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CADFile;
use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class CADFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to assign as creators
        $users = User::whereIn('role', ['member', 'senior_member', 'manufacturer', 'supplier'])
                    ->limit(10)
                    ->get();

        if ($users->isEmpty()) {
            $this->command->warn('No suitable users found. Creating sample users first...');
            // Create sample users if none exist
            $users = collect([
                User::create([
                    'name' => 'CAD Designer 01',
                    'email' => 'caddesigner01@mechamap.com',
                    'username' => 'caddesigner01',
                    'password' => bcrypt('password'),
                    'role' => 'member',
                    'email_verified_at' => now(),
                ]),
                User::create([
                    'name' => 'Senior Engineer 01',
                    'email' => 'seniorengineer01@mechamap.com',
                    'username' => 'seniorengineer01',
                    'password' => bcrypt('password'),
                    'role' => 'senior_member',
                    'email_verified_at' => now(),
                ]),
            ]);
        }

        // Get categories
        $categories = ProductCategory::limit(5)->get();
        if ($categories->isEmpty()) {
            $this->command->warn('No product categories found. Creating sample categories...');
            $categories = collect([
                ProductCategory::create(['name' => 'Mechanical Parts', 'slug' => 'mechanical-parts']),
                ProductCategory::create(['name' => 'Electronic Components', 'slug' => 'electronic-components']),
                ProductCategory::create(['name' => 'Tools & Equipment', 'slug' => 'tools-equipment']),
            ]);
        }

        $cadFiles = [
            [
                'title' => 'Precision Bearing Housing Assembly',
                'description' => 'High-precision bearing housing designed for industrial machinery applications. Features optimized geometry for maximum load capacity and minimal vibration. Compatible with standard bearing sizes and includes mounting provisions for various configurations.',
                'file_type' => 'step',
                'software_used' => 'SolidWorks',
                'license_type' => 'free',
                'tags' => 'bearing, housing, precision, industrial, machinery',
                'status' => 'approved',
                'material_properties' => [
                    'material' => 'Aluminum 6061-T6',
                    'dimensions' => '120x80x45mm',
                    'tolerance' => '±0.05mm',
                    'surface_finish' => 'Ra 1.6μm',
                ],
            ],
            [
                'title' => 'Automotive Brake Disc Design',
                'description' => 'Ventilated brake disc design for high-performance automotive applications. Features curved vanes for optimal cooling and reduced weight while maintaining structural integrity under extreme conditions.',
                'file_type' => 'dwg',
                'software_used' => 'AutoCAD',
                'license_type' => 'educational',
                'tags' => 'automotive, brake, disc, cooling, performance',
                'status' => 'approved',
                'material_properties' => [
                    'material' => 'Cast Iron GG25',
                    'diameter' => '330mm',
                    'thickness' => '32mm',
                    'weight' => '8.5kg',
                ],
            ],
            [
                'title' => 'Robotic Arm Joint Mechanism',
                'description' => 'Advanced joint mechanism for 6-DOF robotic arm applications. Incorporates harmonic drive reduction and precision bearings for smooth operation and high positioning accuracy.',
                'file_type' => 'iges',
                'software_used' => 'CATIA',
                'license_type' => 'commercial',
                'tags' => 'robotics, joint, mechanism, automation, precision',
                'status' => 'pending',
                'material_properties' => [
                    'reduction_ratio' => '100:1',
                    'max_torque' => '150Nm',
                    'backlash' => '<1 arcmin',
                    'weight' => '2.8kg',
                ],
            ],
            [
                'title' => '3D Printed Drone Frame',
                'description' => 'Lightweight carbon fiber reinforced drone frame optimized for 3D printing. Features integrated cable management and modular component mounting system.',
                'file_type' => 'stl',
                'software_used' => 'Fusion 360',
                'license_type' => 'free',
                'tags' => '3d printing, drone, frame, lightweight, modular',
                'status' => 'approved',
                'material_properties' => [
                    'material' => 'PETG + Carbon Fiber',
                    'weight' => '180g',
                    'dimensions' => '250x250x50mm',
                    'motor_mount' => '16x16mm',
                ],
            ],
            [
                'title' => 'Heat Exchanger Tube Bundle',
                'description' => 'Shell and tube heat exchanger design for chemical processing applications. Optimized for maximum heat transfer efficiency with corrosion-resistant materials.',
                'file_type' => 'step',
                'software_used' => 'NX',
                'license_type' => 'commercial',
                'tags' => 'heat exchanger, chemical, processing, efficiency',
                'status' => 'approved',
                'material_properties' => [
                    'tube_material' => 'Stainless Steel 316L',
                    'shell_material' => 'Carbon Steel A516',
                    'heat_transfer_area' => '45m²',
                    'design_pressure' => '16 bar',
                ],
            ],
            [
                'title' => 'Gear Reduction Box Assembly',
                'description' => 'Compact gear reduction box for electric motor applications. Features helical gears for quiet operation and high efficiency power transmission.',
                'file_type' => 'sldprt',
                'software_used' => 'SolidWorks',
                'license_type' => 'educational',
                'tags' => 'gears, reduction, motor, transmission, efficiency',
                'status' => 'approved',
                'material_properties' => [
                    'reduction_ratio' => '20:1',
                    'input_power' => '5kW',
                    'efficiency' => '96%',
                    'noise_level' => '<65dB',
                ],
            ],
            [
                'title' => 'Hydraulic Cylinder Design',
                'description' => 'Heavy-duty hydraulic cylinder for construction equipment. Features chrome-plated rod and high-pressure seals for extended service life.',
                'file_type' => 'dwg',
                'software_used' => 'AutoCAD',
                'license_type' => 'free',
                'tags' => 'hydraulic, cylinder, construction, heavy duty',
                'status' => 'rejected',
                'material_properties' => [
                    'bore_diameter' => '100mm',
                    'rod_diameter' => '56mm',
                    'stroke_length' => '500mm',
                    'working_pressure' => '250 bar',
                ],
            ],
            [
                'title' => 'Precision Machining Fixture',
                'description' => 'Custom machining fixture for high-precision manufacturing operations. Designed for repeatability and quick setup with integrated clamping system.',
                'file_type' => 'step',
                'software_used' => 'Inventor',
                'license_type' => 'free',
                'tags' => 'fixture, machining, precision, manufacturing, clamping',
                'status' => 'pending',
                'material_properties' => [
                    'material' => 'Tool Steel H13',
                    'hardness' => '52-56 HRC',
                    'repeatability' => '±0.01mm',
                    'clamping_force' => '5000N',
                ],
            ],
        ];

        foreach ($cadFiles as $index => $fileData) {
            $user = $users->random();
            $category = $categories->random();

            CADFile::create([
                'uuid' => Str::uuid(),
                'name' => $fileData['title'],
                'description' => $fileData['description'],
                'file_number' => 'CAD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'version' => '1.0',
                'created_by' => $user->id,
                'file_path' => 'cad-files/sample-' . ($index + 1) . '.' . $fileData['file_type'],
                'original_filename' => Str::slug($fileData['title']) . '.' . $fileData['file_type'],
                'file_extension' => $fileData['file_type'],
                'file_size' => rand(1024 * 100, 1024 * 1024 * 10), // 100KB to 10MB
                'mime_type' => $this->getMimeType($fileData['file_type']),
                'checksum' => md5($fileData['title'] . time()),
                'cad_software' => $fileData['software_used'],
                'software_version' => $this->getSoftwareVersion($fileData['software_used']),
                'model_type' => 'assembly',
                'geometry_type' => '3d_solid',
                'units' => ['length' => 'mm', 'mass' => 'kg', 'time' => 's'],
                'material_type' => $fileData['material_properties']['material'] ?? 'Steel',
                'tags' => explode(', ', $fileData['tags']),
                'keywords' => explode(', ', $fileData['tags']),
                'industry_category' => 'mechanical_engineering',
                'application_area' => 'industrial',
                'complexity_level' => rand(1, 5),
                'license_type' => $fileData['license_type'],
                'usage_rights' => ['commercial_use' => $fileData['license_type'] !== 'educational'],
                'material_properties' => $fileData['material_properties'],
                'download_count' => rand(0, 500),
                'view_count' => rand(10, 1000),
                'like_count' => rand(0, 50),
                'rating_average' => rand(35, 50) / 10, // 3.5 to 5.0
                'rating_count' => rand(0, 25),
                'status' => $fileData['status'],
                'processing_status' => 'completed',
                'processed_at' => now()->subDays(rand(1, 30)),
                'is_featured' => rand(0, 1) == 1,
                'is_active' => true,
                'virus_scanned' => true,
                'virus_scan_at' => now()->subDays(rand(1, 30)),
                'visibility' => 'public',
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Created ' . count($cadFiles) . ' sample CAD files.');
    }

    private function getMimeType($extension)
    {
        return match($extension) {
            'dwg' => 'application/acad',
            'step', 'stp' => 'application/step',
            'iges', 'igs' => 'application/iges',
            'stl' => 'application/sla',
            'obj' => 'application/obj',
            'sldprt' => 'application/solidworks',
            'ipt' => 'application/inventor',
            'f3d' => 'application/fusion360',
            default => 'application/octet-stream',
        };
    }

    private function getSoftwareVersion($software)
    {
        return match($software) {
            'SolidWorks' => '2024 SP2',
            'AutoCAD' => '2024',
            'CATIA' => 'V5-6R2021',
            'Fusion 360' => '2.0.18719',
            'NX' => '2306',
            'Inventor' => '2024',
            'Creo' => '9.0.3.0',
            default => '1.0',
        };
    }
}
