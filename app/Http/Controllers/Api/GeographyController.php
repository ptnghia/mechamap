<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeographyController extends Controller
{
    /**
     * Get all active countries with regions
     */
    public function countries(Request $request): JsonResponse
    {
        $query = Country::with(['regions' => function($q) {
            $q->where('is_active', true)
              ->orderBy('sort_order')
              ->orderBy('name');
        }])->where('is_active', true);

        // Filter by continent
        if ($request->has('continent')) {
            $query->where('continent', $request->continent);
        }

        // Filter by measurement system
        if ($request->has('measurement_system')) {
            $query->where('measurement_system', $request->measurement_system);
        }

        $countries = $query->orderBy('sort_order')
                          ->orderBy('name')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Get country by code
     */
    public function country(string $code): JsonResponse
    {
        $country = Country::with(['regions' => function($q) {
            $q->where('is_active', true)
              ->orderBy('sort_order')
              ->orderBy('name');
        }])
        ->where('code', strtoupper($code))
        ->where('is_active', true)
        ->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $country
        ]);
    }

    /**
     * Get regions by country
     */
    public function regionsByCountry(string $countryCode): JsonResponse
    {
        $country = Country::where('code', strtoupper($countryCode))
                         ->where('is_active', true)
                         ->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        $regions = $country->regions()
                          ->where('is_active', true)
                          ->orderBy('sort_order')
                          ->orderBy('name')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $regions,
            'country' => $country->only(['id', 'name', 'code'])
        ]);
    }

    /**
     * Get featured regions
     */
    public function featuredRegions(): JsonResponse
    {
        $regions = Region::with(['country:id,name,code,flag_emoji'])
                        ->where('is_featured', true)
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $regions
        ]);
    }

    /**
     * Get region by ID
     */
    public function region(Region $region): JsonResponse
    {
        if (!$region->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found'
            ], 404);
        }

        $region->load(['country:id,name,code,flag_emoji,measurement_system,timezone']);

        return response()->json([
            'success' => true,
            'data' => $region
        ]);
    }

    /**
     * Get continents list
     */
    public function continents(): JsonResponse
    {
        $continents = Country::select('continent')
                            ->where('is_active', true)
                            ->groupBy('continent')
                            ->orderBy('continent')
                            ->pluck('continent')
                            ->filter()
                            ->values();

        return response()->json([
            'success' => true,
            'data' => $continents
        ]);
    }

    /**
     * Get technical standards by location
     */
    public function standardsByLocation(Request $request): JsonResponse
    {
        $countryCode = $request->get('country');
        $regionId = $request->get('region');

        $standards = [];

        if ($countryCode) {
            $country = Country::where('code', strtoupper($countryCode))->first();
            if ($country && $country->standard_organizations) {
                $standards = array_merge($standards, $country->standard_organizations);
            }
        }

        if ($regionId) {
            $region = Region::find($regionId);
            if ($region && $region->local_standards) {
                $standards = array_merge($standards, $region->local_standards);
            }
        }

        return response()->json([
            'success' => true,
            'data' => array_unique($standards)
        ]);
    }

    /**
     * Get CAD software by location
     */
    public function cadSoftwareByLocation(Request $request): JsonResponse
    {
        $countryCode = $request->get('country');

        $cadSoftware = [];

        if ($countryCode) {
            $country = Country::where('code', strtoupper($countryCode))->first();
            if ($country && $country->common_cad_software) {
                $cadSoftware = $country->common_cad_software;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $cadSoftware
        ]);
    }

    /**
     * Get forums by region
     */
    public function forumsByRegion(Region $region): JsonResponse
    {
        $forums = $region->forums()
                        ->with(['category:id,name,slug'])
                        ->where('is_private', false)
                        ->orderBy('order')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $forums,
            'region' => $region->only(['id', 'name', 'code'])
        ]);
    }
}
