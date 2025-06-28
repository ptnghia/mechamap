<?php

namespace App\Helpers;

class LayoutHelper
{
    /**
     * Get layout configuration for different page types
     */
    public static function getLayoutConfig($pageType = 'default', $options = [])
    {
        $config = [
            'hasSidebar' => false,
            'sidebarType' => 'default',
            'isAuthPage' => false,
            'isAdminPage' => false,
            'isMarketplace' => false,
            'viewType' => null,
            'bodyClass' => '',
        ];

        switch ($pageType) {
            case 'homepage':
                $config['hasSidebar'] = true;
                $config['sidebarType'] = 'default';
                $config['viewType'] = 'homepage';
                break;

            case 'forum':
            case 'threads':
                $config['hasSidebar'] = true;
                $config['sidebarType'] = 'default';
                $config['viewType'] = 'threads';
                break;

            case 'thread-create':
                $config['hasSidebar'] = true;
                $config['sidebarType'] = 'thread-creation';
                $config['viewType'] = 'threads';
                break;

            case 'profile':
                $config['hasSidebar'] = true;
                $config['sidebarType'] = 'professional';
                $config['viewType'] = 'profile';
                break;

            case 'marketplace':
                $config['hasSidebar'] = true;
                $config['sidebarType'] = 'default';
                $config['isMarketplace'] = true;
                $config['viewType'] = 'marketplace';
                break;

            case 'search':
                $config['hasSidebar'] = true;
                $config['sidebarType'] = 'default';
                $config['viewType'] = 'search';
                break;

            case 'auth':
                $config['hasSidebar'] = false;
                $config['isAuthPage'] = true;
                $config['viewType'] = 'auth';
                $config['bodyClass'] = 'auth-page';
                break;

            case 'admin':
                $config['hasSidebar'] = false; // Admin has its own sidebar
                $config['isAdminPage'] = true;
                $config['viewType'] = 'admin';
                $config['bodyClass'] = 'admin-page';
                break;

            case 'landing':
            case 'static':
                $config['hasSidebar'] = false;
                $config['viewType'] = 'static';
                break;

            default:
                // Default configuration
                break;
        }

        // Merge with custom options
        return array_merge($config, $options);
    }

    /**
     * Generate breadcrumbs HTML
     */
    public static function generateBreadcrumbs($items)
    {
        if (empty($items)) {
            return '';
        }

        $html = '<ol class="breadcrumb">';
        
        foreach ($items as $index => $item) {
            $isLast = ($index === count($items) - 1);
            
            if ($isLast) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">';
                $html .= '<i class="' . ($item['icon'] ?? 'fas fa-circle') . ' me-1"></i>';
                $html .= e($item['title']);
                $html .= '</li>';
            } else {
                $html .= '<li class="breadcrumb-item">';
                if (isset($item['url'])) {
                    $html .= '<a href="' . e($item['url']) . '">';
                    $html .= '<i class="' . ($item['icon'] ?? 'fas fa-home') . ' me-1"></i>';
                    $html .= e($item['title']);
                    $html .= '</a>';
                } else {
                    $html .= '<i class="' . ($item['icon'] ?? 'fas fa-circle') . ' me-1"></i>';
                    $html .= e($item['title']);
                }
                $html .= '</li>';
            }
        }
        
        $html .= '</ol>';
        
        return $html;
    }

    /**
     * Get page title with site name
     */
    public static function getPageTitle($title = null, $includeSiteName = true)
    {
        $siteName = get_site_name();
        
        if (!$title) {
            return $siteName . ' - ' . get_site_tagline();
        }
        
        if ($includeSiteName) {
            return $title . ' - ' . $siteName;
        }
        
        return $title;
    }

    /**
     * Get meta description
     */
    public static function getMetaDescription($description = null)
    {
        return $description ?: get_site_description();
    }

    /**
     * Get Open Graph image
     */
    public static function getOgImage($image = null)
    {
        return $image ?: asset('images/brand/mechamap-banner.jpg');
    }

    /**
     * Check if current page should have sidebar
     */
    public static function shouldHaveSidebar($route = null)
    {
        $route = $route ?: request()->route()->getName();
        
        $sidebarRoutes = [
            'home',
            'threads.index',
            'threads.show',
            'threads.create',
            'forums.index',
            'forums.show',
            'marketplace.index',
            'marketplace.show',
            'search',
            'profile.show',
            'profile.edit',
        ];
        
        return in_array($route, $sidebarRoutes);
    }

    /**
     * Get sidebar type based on route
     */
    public static function getSidebarType($route = null)
    {
        $route = $route ?: request()->route()->getName();
        
        $sidebarTypes = [
            'threads.create' => 'thread-creation',
            'profile.show' => 'professional',
            'profile.edit' => 'professional',
        ];
        
        return $sidebarTypes[$route] ?? 'default';
    }
}
