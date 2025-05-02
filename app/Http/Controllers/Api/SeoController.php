<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    /**
     * Lấy tất cả cài đặt SEO
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = SeoSetting::all()->groupBy('group');

        $formattedSettings = [];
        foreach ($settings as $group => $items) {
            $formattedSettings[$group] = $items->pluck('value', 'key')->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
            'message' => 'Lấy cài đặt SEO thành công'
        ]);
    }

    /**
     * Lấy cài đặt SEO theo nhóm
     *
     * @param string $group
     * @return JsonResponse
     */
    public function getByGroup(string $group): JsonResponse
    {
        $settings = SeoSetting::where('group', $group)->get();

        if ($settings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhóm cài đặt SEO'
            ], 404);
        }

        $formattedSettings = $settings->pluck('value', 'key')->toArray();

        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
            'message' => 'Lấy cài đặt SEO thành công'
        ]);
    }

    /**
     * Lấy cài đặt SEO cho trang theo route name
     *
     * @param string $routeName
     * @return JsonResponse
     */
    public function getPageSeoByRoute(string $routeName): JsonResponse
    {
        $pageSeo = PageSeo::where('route_name', $routeName)
            ->where('is_active', true)
            ->first();

        if (!$pageSeo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cài đặt SEO cho trang này'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pageSeo,
            'message' => 'Lấy cài đặt SEO cho trang thành công'
        ]);
    }

    /**
     * Lấy cài đặt SEO cho trang theo URL pattern
     *
     * @param string $urlPattern
     * @return JsonResponse
     */
    public function getPageSeoByUrl(string $urlPattern): JsonResponse
    {
        $pageSeo = PageSeo::findByUrl($urlPattern);

        if (!$pageSeo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cài đặt SEO cho URL này'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pageSeo,
            'message' => 'Lấy cài đặt SEO cho URL thành công'
        ]);
    }

    /**
     * Lấy thông tin SEO cho trang tĩnh
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getPageSeo(string $slug): JsonResponse
    {
        // Tìm trang tĩnh theo slug
        $page = \App\Models\Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy trang tĩnh'
            ], 404);
        }

        // Lấy thông tin SEO từ cài đặt hoặc từ trang
        $seoData = [
            'title' => $page->seo_title ?? $page->title,
            'description' => $page->seo_description ?? substr(strip_tags($page->content), 0, 160),
            'keywords' => $page->seo_keywords ?? '',
            'canonical_url' => url("/pages/{$page->slug}"),
            'robots' => 'index, follow',
            'og' => [
                'title' => $page->seo_title ?? $page->title,
                'description' => $page->seo_description ?? substr(strip_tags($page->content), 0, 160),
                'image' => $page->featured_image ? url($page->featured_image) : url('/images/default-og.jpg'),
                'type' => 'article',
                'url' => url("/pages/{$page->slug}")
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $page->seo_title ?? $page->title,
                'description' => $page->seo_description ?? substr(strip_tags($page->content), 0, 160),
                'image' => $page->featured_image ? url($page->featured_image) : url('/images/default-twitter.jpg')
            ],
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $page->title,
                'description' => $page->seo_description ?? substr(strip_tags($page->content), 0, 160),
                'url' => url("/pages/{$page->slug}"),
                'datePublished' => $page->created_at,
                'dateModified' => $page->updated_at
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $seoData,
            'message' => 'Lấy thông tin SEO cho trang tĩnh thành công'
        ]);
    }

    /**
     * Lấy thông tin SEO cho trang bài viết
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getThreadSeo(string $slug): JsonResponse
    {
        // Tìm bài viết theo slug
        $thread = \App\Models\Thread::where('slug', $slug)->with(['user', 'forum'])->first();

        if (!$thread) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }

        // Lấy thông tin SEO từ bài viết
        $seoData = [
            'title' => $thread->title . ' | ' . config('app.name'),
            'description' => substr(strip_tags($thread->content), 0, 160),
            'keywords' => $thread->forum->name . ', ' . implode(', ', explode(' ', $thread->title)),
            'canonical_url' => url("/threads/{$thread->slug}"),
            'robots' => 'index, follow',
            'og' => [
                'title' => $thread->title,
                'description' => substr(strip_tags($thread->content), 0, 160),
                'image' => $thread->featured_image ? url($thread->featured_image) : url('/images/default-og.jpg'),
                'type' => 'article',
                'url' => url("/threads/{$thread->slug}")
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $thread->title,
                'description' => substr(strip_tags($thread->content), 0, 160),
                'image' => $thread->featured_image ? url($thread->featured_image) : url('/images/default-twitter.jpg')
            ],
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@type' => 'DiscussionForumPosting',
                'headline' => $thread->title,
                'description' => substr(strip_tags($thread->content), 0, 160),
                'url' => url("/threads/{$thread->slug}"),
                'datePublished' => $thread->created_at,
                'dateModified' => $thread->updated_at,
                'author' => [
                    '@type' => 'Person',
                    'name' => $thread->user->name,
                    'url' => url("/users/{$thread->user->username}")
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => url('/images/logo.png')
                    ]
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => url("/threads/{$thread->slug}")
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $seoData,
            'message' => 'Lấy thông tin SEO cho trang bài viết thành công'
        ]);
    }

    /**
     * Lấy thông tin SEO cho trang diễn đàn
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getForumSeo(string $slug): JsonResponse
    {
        // Tìm diễn đàn theo slug
        $forum = \App\Models\Forum::where('slug', $slug)->first();

        if (!$forum) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy diễn đàn'
            ], 404);
        }

        // Lấy thông tin SEO từ diễn đàn
        $seoData = [
            'title' => $forum->name . ' | ' . config('app.name'),
            'description' => $forum->description ?? 'Diễn đàn ' . $forum->name . ' trên ' . config('app.name'),
            'keywords' => $forum->name . ', diễn đàn, ' . config('app.name'),
            'canonical_url' => url("/forums/{$forum->slug}"),
            'robots' => 'index, follow',
            'og' => [
                'title' => $forum->name,
                'description' => $forum->description ?? 'Diễn đàn ' . $forum->name . ' trên ' . config('app.name'),
                'image' => $forum->image ? url($forum->image) : url('/images/default-og.jpg'),
                'type' => 'website',
                'url' => url("/forums/{$forum->slug}")
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $forum->name,
                'description' => $forum->description ?? 'Diễn đàn ' . $forum->name . ' trên ' . config('app.name'),
                'image' => $forum->image ? url($forum->image) : url('/images/default-twitter.jpg')
            ],
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@type' => 'DiscussionForumPosting',
                'name' => $forum->name,
                'description' => $forum->description ?? 'Diễn đàn ' . $forum->name . ' trên ' . config('app.name'),
                'url' => url("/forums/{$forum->slug}"),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => url('/images/logo.png')
                    ]
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $seoData,
            'message' => 'Lấy thông tin SEO cho trang diễn đàn thành công'
        ]);
    }

    /**
     * Lấy thông tin SEO cho trang danh mục
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function getCategorySeo(string $slug): JsonResponse
    {
        // Tìm danh mục theo slug
        $category = \App\Models\Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }

        // Lấy thông tin SEO từ danh mục
        $seoData = [
            'title' => $category->name . ' | ' . config('app.name'),
            'description' => $category->description ?? 'Danh mục ' . $category->name . ' trên ' . config('app.name'),
            'keywords' => $category->name . ', danh mục, ' . config('app.name'),
            'canonical_url' => url("/categories/{$category->slug}"),
            'robots' => 'index, follow',
            'og' => [
                'title' => $category->name,
                'description' => $category->description ?? 'Danh mục ' . $category->name . ' trên ' . config('app.name'),
                'image' => url('/images/default-og.jpg'),
                'type' => 'website',
                'url' => url("/categories/{$category->slug}")
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $category->name,
                'description' => $category->description ?? 'Danh mục ' . $category->name . ' trên ' . config('app.name'),
                'image' => url('/images/default-twitter.jpg')
            ],
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $category->name,
                'description' => $category->description ?? 'Danh mục ' . $category->name . ' trên ' . config('app.name'),
                'url' => url("/categories/{$category->slug}"),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => url('/images/logo.png')
                    ]
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $seoData,
            'message' => 'Lấy thông tin SEO cho trang danh mục thành công'
        ]);
    }

    /**
     * Lấy thông tin SEO cho trang người dùng
     *
     * @param string $username
     * @return JsonResponse
     */
    public function getUserSeo(string $username): JsonResponse
    {
        // Tìm người dùng theo username
        $user = \App\Models\User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        // Lấy thông tin SEO từ người dùng
        $seoData = [
            'title' => $user->name . ' | ' . config('app.name'),
            'description' => $user->about_me ?? 'Trang cá nhân của ' . $user->name . ' trên ' . config('app.name'),
            'keywords' => $user->name . ', ' . $user->username . ', trang cá nhân, ' . config('app.name'),
            'canonical_url' => url("/users/{$user->username}"),
            'robots' => 'index, follow',
            'og' => [
                'title' => $user->name,
                'description' => $user->about_me ?? 'Trang cá nhân của ' . $user->name . ' trên ' . config('app.name'),
                'image' => $user->getAvatarUrl(),
                'type' => 'profile',
                'url' => url("/users/{$user->username}")
            ],
            'twitter' => [
                'card' => 'summary',
                'title' => $user->name,
                'description' => $user->about_me ?? 'Trang cá nhân của ' . $user->name . ' trên ' . config('app.name'),
                'image' => $user->getAvatarUrl()
            ],
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => $user->name,
                'url' => url("/users/{$user->username}"),
                'image' => $user->getAvatarUrl(),
                'description' => $user->about_me ?? 'Trang cá nhân của ' . $user->name . ' trên ' . config('app.name'),
                'memberOf' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'url' => url('/')
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $seoData,
            'message' => 'Lấy thông tin SEO cho trang người dùng thành công'
        ]);
    }
}
