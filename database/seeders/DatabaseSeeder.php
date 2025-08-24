<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Group Conversations Seeders
            ConversationTypeSeeder::class,
            SampleGroupRequestSeeder::class,
            SampleGroupConversationSeeder::class,
            
            // Other existing seeders
            MessagingSeeder::class,
            MarketplaceMessageTemplateSeeder::class,
            ShowcaseRatingSystemSeeder::class,
            TestNotificationSeeder::class,
            ComprehensivePageSeoSeeder::class,
            MultilingualPageSeoSeeder::class,
        ]);
    }
}
