<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Add static pages
        $staticPages = [
            '/',
            '/whats-new',
            '/forum-listing',
            '/public-showcase',
            '/gallery',
            '/search',
            '/members',
            '/faq',
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->addUrl(url($page), '1.0', 'daily');
        }

        // Add categories
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap .= $this->addUrl(route('categories.show', $category), '0.8', 'weekly');
        }

        // Add forums
        $forums = Forum::all();
        foreach ($forums as $forum) {
            $sitemap .= $this->addUrl(route('forums.show', $forum), '0.8', 'weekly');
        }

        // Add threads (limit to recent ones to avoid huge sitemap)
        $threads = Thread::latest()->take(1000)->get();
        foreach ($threads as $thread) {
            $sitemap .= $this->addUrl(route('threads.show', $thread), '0.6', 'weekly', $thread->updated_at->toIso8601String());
        }

        // Add user profiles (limit to active users)
        $users = User::where('status', 'active')->take(500)->get();
        foreach ($users as $user) {
            $sitemap .= $this->addUrl(route('profile.show', $user->username), '0.4', 'monthly');
        }

        $sitemap .= '</urlset>';

        // Save the sitemap
        File::put(public_path('sitemap.xml'), $sitemap);

        $this->info('Sitemap generated successfully!');
    }

    /**
     * Add a URL to the sitemap.
     *
     * @param string $url
     * @param string $priority
     * @param string $changefreq
     * @param string|null $lastmod
     * @return string
     */
    private function addUrl($url, $priority = '0.5', $changefreq = 'weekly', $lastmod = null)
    {
        $url = htmlspecialchars($url);
        $xml = "  <url>" . PHP_EOL;
        $xml .= "    <loc>{$url}</loc>" . PHP_EOL;
        
        if ($lastmod) {
            $xml .= "    <lastmod>{$lastmod}</lastmod>" . PHP_EOL;
        }
        
        $xml .= "    <changefreq>{$changefreq}</changefreq>" . PHP_EOL;
        $xml .= "    <priority>{$priority}</priority>" . PHP_EOL;
        $xml .= "  </url>" . PHP_EOL;
        
        return $xml;
    }
}
