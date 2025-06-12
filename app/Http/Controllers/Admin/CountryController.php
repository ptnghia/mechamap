<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class CountryController extends Controller
{
    /**
     * Danh sách countries với regions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Country::with(['regions' => function($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])->where('is_active', true);

            // Filter by continent
            if ($request->has('continent')) {
                $query->where('continent', $request->continent);
            }

            // Filter by measurement system
            if ($request->has('measurement_system')) {
                $query->where('measurement_system', $request->measurement_system);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('name_local', 'like', '%' . $request->search . '%');
                });
            }

            $countries = $query->orderBy('sort_order')
                              ->orderBy('name')
                              ->get();

            return response()->json([
                'success' => true,
                'data' => $countries,
                'continents' => $this->getContinents(),
                'measurement_systems' => $this->getMeasurementSystems(),
                'message' => 'Danh sách countries'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết country với thống kê
     */
    public function show(Country $country): JsonResponse
    {
        try {
            $country->load([
                'regions' => function($q) {
                    $q->withCount(['users', 'forums'])
                      ->orderBy('sort_order');
                }
            ]);

            // Thống kê chi tiết
            $stats = [
                'total_regions' => $country->regions()->count(),
                'active_regions' => $country->regions()->where('is_active', true)->count(),
                'total_users' => $country->users()->count(),
                'total_forums' => $country->regions()->withCount('forums')->get()->sum('forums_count'),
                'popular_specialties' => $this->getPopularSpecialties($country),
                'cad_software_usage' => $this->getCadSoftwareUsage($country)
            ];

            return response()->json([
                'success' => true,
                'data' => $country,
                'statistics' => $stats,
                'message' => 'Chi tiết country'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo country mới
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code',
            'code_alpha3' => 'required|string|size:3|unique:countries,code_alpha3',
            'continent' => 'required|string',
            'timezone' => 'required|string',
            'language_code' => 'required|string|max:5',
            'measurement_system' => 'required|in:metric,imperial,mixed'
        ]);

        try {
            $country = Country::create([
                'name' => $request->name,
                'name_local' => $request->name_local,
                'code' => strtoupper($request->code),
                'code_alpha3' => strtoupper($request->code_alpha3),
                'phone_code' => $request->phone_code,
                'currency_code' => $request->currency_code,
                'currency_symbol' => $request->currency_symbol,
                'continent' => $request->continent,
                'timezone' => $request->timezone,
                'timezones' => $request->timezones ?? [$request->timezone],
                'language_code' => $request->language_code,
                'languages' => $request->languages ?? [$request->language_code],
                'measurement_system' => $request->measurement_system,
                'standard_organizations' => $request->standard_organizations ?? [],
                'common_cad_software' => $request->common_cad_software ?? [],
                'flag_emoji' => $request->flag_emoji,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'allow_user_registration' => $request->boolean('allow_user_registration', true),
                'mechanical_specialties' => $request->mechanical_specialties ?? [],
                'industrial_sectors' => $request->industrial_sectors ?? []
            ]);

            return response()->json([
                'success' => true,
                'data' => $country,
                'message' => 'Tạo country thành công'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật country
     */
    public function update(Request $request, Country $country): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code,' . $country->id,
            'code_alpha3' => 'required|string|size:3|unique:countries,code_alpha3,' . $country->id,
            'continent' => 'required|string',
            'timezone' => 'required|string',
            'language_code' => 'required|string|max:5',
            'measurement_system' => 'required|in:metric,imperial,mixed'
        ]);

        try {
            $country->update([
                'name' => $request->name,
                'name_local' => $request->name_local,
                'code' => strtoupper($request->code),
                'code_alpha3' => strtoupper($request->code_alpha3),
                'phone_code' => $request->phone_code,
                'currency_code' => $request->currency_code,
                'currency_symbol' => $request->currency_symbol,
                'continent' => $request->continent,
                'timezone' => $request->timezone,
                'timezones' => $request->timezones ?? [$request->timezone],
                'language_code' => $request->language_code,
                'languages' => $request->languages ?? [$request->language_code],
                'measurement_system' => $request->measurement_system,
                'standard_organizations' => $request->standard_organizations ?? [],
                'common_cad_software' => $request->common_cad_software ?? [],
                'flag_emoji' => $request->flag_emoji,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'allow_user_registration' => $request->boolean('allow_user_registration', true),
                'mechanical_specialties' => $request->mechanical_specialties ?? [],
                'industrial_sectors' => $request->industrial_sectors ?? []
            ]);

            return response()->json([
                'success' => true,
                'data' => $country->fresh(),
                'message' => 'Cập nhật country thành công'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa country (soft delete)
     */
    public function destroy(Country $country): JsonResponse
    {
        try {
            // Check if country has users or regions
            if ($country->users()->exists() || $country->regions()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa country đang có users hoặc regions'
                ], 400);
            }

            $country->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa country thành công'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get continents list
     */
    private function getContinents(): array
    {
        return [
            'Asia' => 'Châu Á',
            'Europe' => 'Châu Âu',
            'North America' => 'Bắc Mỹ',
            'South America' => 'Nam Mỹ',
            'Africa' => 'Châu Phi',
            'Oceania' => 'Châu Đại Dương',
            'Antarctica' => 'Nam Cực'
        ];
    }

    /**
     * Get measurement systems
     */
    private function getMeasurementSystems(): array
    {
        return [
            'metric' => 'Hệ mét',
            'imperial' => 'Hệ Anh-Mỹ',
            'mixed' => 'Hỗn hợp'
        ];
    }

    /**
     * Get popular specialties for country
     */
    private function getPopularSpecialties(Country $country): array
    {
        // TODO: Implement based on user expertise data
        return $country->mechanical_specialties ?? [];
    }

    /**
     * Get CAD software usage statistics
     */
    private function getCadSoftwareUsage(Country $country): array
    {
        // TODO: Implement based on user CAD software preferences
        return $country->common_cad_software ?? [];
    }
}
