<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class RegionController extends Controller
{
    /**
     * Danh sách regions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Region::with(['country'])
                           ->where('is_active', true);

            // Filter by country
            if ($request->has('country_id')) {
                $query->where('country_id', $request->country_id);
            }

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('name_local', 'like', '%' . $request->search . '%');
                });
            }

            // Filter featured regions
            if ($request->boolean('featured_only')) {
                $query->where('is_featured', true);
            }

            $regions = $query->orderBy('sort_order')
                            ->orderBy('name')
                            ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $regions,
                'types' => $this->getRegionTypes(),
                'message' => 'Danh sách regions'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết region với thống kê
     */
    public function show(Region $region): JsonResponse
    {
        try {
            $region->load(['country']);

            // Thống kê chi tiết
            $stats = [
                'total_users' => $region->users()->count(),
                'total_forums' => $region->forums()->count(),
                'total_threads' => $region->threads()->count(),
                'industrial_zones_count' => count($region->industrial_zones ?? []),
                'universities_count' => count($region->universities ?? []),
                'major_companies_count' => count($region->major_companies ?? []),
                'distance_from_capital' => $this->calculateDistanceFromCapital($region),
                'timezone_info' => $this->getTimezoneInfo($region)
            ];

            return response()->json([
                'success' => true,
                'data' => $region,
                'statistics' => $stats,
                'message' => 'Chi tiết region'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo region mới
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'type' => 'required|in:province,state,prefecture,region,city,zone',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        try {
            // Check unique code within country
            if ($request->code) {
                $existingRegion = Region::where('country_id', $request->country_id)
                                       ->where('code', $request->code)
                                       ->first();
                if ($existingRegion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mã region đã tồn tại trong country này'
                    ], 400);
                }
            }

            $region = Region::create([
                'country_id' => $request->country_id,
                'name' => $request->name,
                'name_local' => $request->name_local,
                'code' => $request->code,
                'type' => $request->type,
                'timezone' => $request->timezone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'industrial_zones' => $request->industrial_zones ?? [],
                'universities' => $request->universities ?? [],
                'major_companies' => $request->major_companies ?? [],
                'specialization_areas' => $request->specialization_areas ?? [],
                'forum_moderator_timezone' => $request->forum_moderator_timezone,
                'local_standards' => $request->local_standards ?? [],
                'common_materials' => $request->common_materials ?? [],
                'icon' => $request->icon,
                'color' => $request->color,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => $request->boolean('is_featured', false)
            ]);

            return response()->json([
                'success' => true,
                'data' => $region->load('country'),
                'message' => 'Tạo region thành công'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật region
     */
    public function update(Request $request, Region $region): JsonResponse
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'type' => 'required|in:province,state,prefecture,region,city,zone',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        try {
            // Check unique code within country (excluding current region)
            if ($request->code) {
                $existingRegion = Region::where('country_id', $request->country_id)
                                       ->where('code', $request->code)
                                       ->where('id', '!=', $region->id)
                                       ->first();
                if ($existingRegion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mã region đã tồn tại trong country này'
                    ], 400);
                }
            }

            $region->update([
                'country_id' => $request->country_id,
                'name' => $request->name,
                'name_local' => $request->name_local,
                'code' => $request->code,
                'type' => $request->type,
                'timezone' => $request->timezone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'industrial_zones' => $request->industrial_zones ?? [],
                'universities' => $request->universities ?? [],
                'major_companies' => $request->major_companies ?? [],
                'specialization_areas' => $request->specialization_areas ?? [],
                'forum_moderator_timezone' => $request->forum_moderator_timezone,
                'local_standards' => $request->local_standards ?? [],
                'common_materials' => $request->common_materials ?? [],
                'icon' => $request->icon,
                'color' => $request->color,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => $request->boolean('is_featured', false)
            ]);

            return response()->json([
                'success' => true,
                'data' => $region->fresh()->load('country'),
                'message' => 'Cập nhật region thành công'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa region
     */
    public function destroy(Region $region): JsonResponse
    {
        try {
            // Check if region has users or forums
            if ($region->users()->exists() || $region->forums()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa region đang có users hoặc forums'
                ], 400);
            }

            $region->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa region thành công'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get regions by country
     */
    public function byCountry(Country $country): JsonResponse
    {
        try {
            $regions = $country->regions()
                              ->where('is_active', true)
                              ->orderBy('sort_order')
                              ->orderBy('name')
                              ->get();

            return response()->json([
                'success' => true,
                'data' => $regions,
                'country' => $country,
                'message' => 'Regions của country'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured regions
     */
    public function featured(): JsonResponse
    {
        try {
            $regions = Region::with(['country'])
                            ->where('is_featured', true)
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $regions,
                'message' => 'Featured regions'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get region types
     */
    private function getRegionTypes(): array
    {
        return [
            'province' => 'Tỉnh/Thành phố',
            'state' => 'Bang (US)',
            'prefecture' => 'Tỉnh (Japan)',
            'region' => 'Vùng',
            'city' => 'Thành phố lớn',
            'zone' => 'Khu vực đặc biệt'
        ];
    }

    /**
     * Calculate distance from country capital
     */
    private function calculateDistanceFromCapital(Region $region): ?float
    {
        // TODO: Implement distance calculation from country capital
        // This would require capital city coordinates in countries table
        return null;
    }

    /**
     * Get timezone information
     */
    private function getTimezoneInfo(Region $region): array
    {
        $timezone = $region->timezone ?? $region->country->timezone ?? 'UTC';

        try {
            $tz = new \DateTimeZone($timezone);
            $now = new \DateTime('now', $tz);

            return [
                'timezone' => $timezone,
                'current_time' => $now->format('Y-m-d H:i:s'),
                'offset' => $now->getOffset() / 3600, // Hours
                'is_dst' => $tz->getTransitions(time(), time())[0]['isdst'] ?? false
            ];
        } catch (Exception $e) {
            return [
                'timezone' => $timezone,
                'error' => 'Invalid timezone'
            ];
        }
    }
}
