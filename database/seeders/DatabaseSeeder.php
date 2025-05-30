<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Đảm bảo chạy theo thứ tự đúng
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            ForumSeeder::class,
            ThreadSeeder::class,
            TagSeeder::class,
            SeoSettingSeeder::class,
            PageSeoSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
