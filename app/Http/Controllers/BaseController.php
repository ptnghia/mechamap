<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LayoutHelper;

class BaseController extends Controller
{
    /**
     * Render view with master layout
     */
    protected function renderWithMasterLayout($view, $data = [], $pageType = 'default', $layoutOptions = [])
    {
        // Get layout configuration
        $layoutConfig = LayoutHelper::getLayoutConfig($pageType, $layoutOptions);

        // Merge layout config with view data
        $viewData = array_merge($layoutConfig, $data);

        return view($view, $viewData);
    }

    /**
     * Render homepage
     */
    protected function renderHomepage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'homepage');
    }

    /**
     * Render forum/threads page
     */
    protected function renderForumPage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'forum');
    }

    /**
     * Render thread creation page
     */
    protected function renderThreadCreatePage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'thread-create');
    }

    /**
     * Render profile page
     */
    protected function renderProfilePage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'profile');
    }

    /**
     * Render marketplace page
     */
    protected function renderMarketplacePage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'marketplace');
    }

    /**
     * Render search page
     */
    protected function renderSearchPage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'search');
    }

    /**
     * Render auth page
     */
    protected function renderAuthPage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'auth');
    }

    /**
     * Render static/landing page
     */
    protected function renderStaticPage($view, $data = [])
    {
        return $this->renderWithMasterLayout($view, $data, 'static');
    }

    /**
     * Set page title
     */
    protected function setPageTitle($title, $includeSiteName = true)
    {
        return LayoutHelper::getPageTitle($title, $includeSiteName);
    }

    /**
     * Set breadcrumbs
     */
    protected function setBreadcrumbs($items)
    {
        return LayoutHelper::generateBreadcrumbs($items);
    }

    /**
     * Add common view data
     */
    protected function addCommonViewData($data = [])
    {
        return array_merge([
            'currentRoute' => request()->route()->getName(),
            'currentUrl' => request()->url(),
            'currentPath' => request()->path(),
        ], $data);
    }

    /**
     * Render view with breadcrumbs
     */
    protected function renderWithBreadcrumbs($view, $data = [], $breadcrumbs = [], $pageType = 'default')
    {
        if (!empty($breadcrumbs)) {
            $data['breadcrumbs'] = $this->setBreadcrumbs($breadcrumbs);
        }

        return $this->renderWithMasterLayout($view, $data, $pageType);
    }

    /**
     * Render view with page header
     */
    protected function renderWithPageHeader($view, $data = [], $title = '', $subtitle = '', $pageType = 'default')
    {
        $data['pageTitle'] = $title;
        $data['pageSubtitle'] = $subtitle;

        return $this->renderWithMasterLayout($view, $data, $pageType);
    }

    /**
     * Get layout config for current route
     */
    protected function getLayoutConfigForRoute($route = null)
    {
        $route = $route ?: request()->route()->getName();

        // Determine page type based on route
        if (str_contains($route, 'admin.')) {
            return 'admin';
        } elseif (in_array($route, ['login', 'register', 'password.request', 'password.reset'])) {
            return 'auth';
        } elseif ($route === 'home') {
            return 'homepage';
        } elseif (str_contains($route, 'threads.')) {
            if ($route === 'threads.create') {
                return 'thread-create';
            }
            return 'forum';
        } elseif (str_contains($route, 'marketplace.')) {
            return 'marketplace';
        } elseif (str_contains($route, 'profile.')) {
            return 'profile';
        } elseif ($route === 'search') {
            return 'search';
        } else {
            return 'default';
        }
    }

    /**
     * Auto-render based on current route
     */
    protected function autoRender($view, $data = [])
    {
        $pageType = $this->getLayoutConfigForRoute();
        return $this->renderWithMasterLayout($view, $data, $pageType);
    }
}
