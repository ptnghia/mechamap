<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingCart;
use App\Models\TechnicalProduct;
use App\Services\ShoppingCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected ShoppingCartService $cartService;

    public function __construct(ShoppingCartService $cartService)
    {
        $this->cartService = $cartService;
        $this->middleware('auth:sanctum');
    }    /**
     * Lấy giỏ hàng của user hiện tại
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $cartItems = $this->cartService->getCartItems($user->id);
            $summary = $this->cartService->getCartSummary($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $cartItems,
                    'summary' => $summary,
                ],
                'message' => 'Lấy giỏ hàng thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy giỏ hàng', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy giỏ hàng'
            ], 500);
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:technical_products,id',
            'quantity' => 'integer|min:1|max:10',
            'license_type' => 'string|in:standard,extended,commercial',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $product = TechnicalProduct::findOrFail($request->product_id);            // Kiểm tra product có approved không
            if ($product->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm này hiện không khả dụng'
                ], 400);
            }// Kiểm tra user đã mua product này chưa
            if ($this->cartService->hasUserPurchased($user->id, $product->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã sở hữu sản phẩm này'
                ], 400);
            }

            $cartItem = $this->cartService->addToCart(
                $user->id,
                $product->id,
                $request->quantity ?? 1,
                $request->license_type ?? 'standard'
            );

            // Lấy lại cart summary
            $summary = $this->cartService->getCartSummary($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'cart_item' => $cartItem->load('product'),
                    'summary' => $summary,
                ],
                'message' => 'Đã thêm vào giỏ hàng'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Lỗi thêm vào giỏ hàng', [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng'
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, int $cartItemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10',
            'license_type' => 'string|in:standard,extended,commercial',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $cartItem = ShoppingCart::where('id', $cartItemId)
                ->where('user_id', $user->id)
                ->firstOrFail();            $updatedItem = $this->cartService->updateCartItem(
                $cartItem->id,
                $user->id,
                $request->quantity,
                $request->license_type
            );$summary = $this->cartService->getCartSummary($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'cart_item' => $updatedItem->load('product'),
                    'summary' => $summary,
                ],
                'message' => 'Đã cập nhật giỏ hàng'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật giỏ hàng', [
                'user_id' => Auth::id(),
                'cart_item_id' => $cartItemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giỏ hàng'
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function destroy(int $cartItemId): JsonResponse
    {
        try {
            $user = Auth::user();
            $cartItem = ShoppingCart::where('id', $cartItemId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $this->cartService->removeFromCart($user->id, $cartItemId);

            $summary = $this->cartService->getCartSummary($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                ],
                'message' => 'Đã xóa khỏi giỏ hàng'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi xóa khỏi giỏ hàng', [
                'user_id' => Auth::id(),
                'cart_item_id' => $cartItemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa khỏi giỏ hàng'
            ], 500);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->cartService->clearCart($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'items_count' => 0,
                        'subtotal' => 0,
                        'tax_amount' => 0,
                        'total_amount' => 0,
                    ],
                ],
                'message' => 'Đã xóa toàn bộ giỏ hàng'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi xóa toàn bộ giỏ hàng', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa giỏ hàng'
            ], 500);
        }
    }    /**
     * Validate giỏ hàng trước khi checkout
     */
    public function validateCart(): JsonResponse
    {
        try {
            $user = Auth::user();
            $validation = $this->cartService->validateCart($user->id);

            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng có vấn đề, vui lòng kiểm tra lại',
                    'data' => [
                        'issues' => $validation['issues'],
                        'errors' => $validation['errors'] ?? [],
                        'warnings' => $validation['warnings'] ?? [],
                        'invalid_items' => $validation['invalid_items'] ?? [],
                        'needs_price_update' => $validation['needs_price_update'] ?? false,
                    ]
                ], 400);
            }

            // Nếu cần cập nhật giá nhưng cart vẫn valid
            if ($validation['needs_price_update'] ?? false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giá sản phẩm đã thay đổi, vui lòng xác nhận',
                    'data' => [
                        'issues' => $validation['warnings'],
                        'warnings' => $validation['warnings'],
                        'needs_price_update' => true,
                    ]
                ], 422); // 422 Unprocessable Entity for price changes
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => true,
                    'summary' => $this->cartService->getCartSummary($user->id),
                ],
                'message' => 'Giỏ hàng hợp lệ'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi validate giỏ hàng', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra giỏ hàng'
            ], 500);
        }
    }

    /**
     * Cập nhật giá cho tất cả sản phẩm trong giỏ hàng
     */
    public function updatePrices(): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->cartService->updatePrices($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'updated_count' => $result['updated_count'],
                    'updated_items' => $result['updated_items'],
                    'summary' => $this->cartService->getCartSummary($user->id),
                ],
                'message' => $result['updated_count'] > 0
                    ? "Đã cập nhật giá cho {$result['updated_count']} sản phẩm"
                    : 'Không có sản phẩm nào cần cập nhật giá'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật giá giỏ hàng', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giá'
            ], 500);
        }
    }

    /**
     * Estimate shipping và fees
     */
    public function estimate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'string|size:2',
            'state_code' => 'string|max:10',
            'discount_code' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();            $estimate = $this->cartService->estimateCart(
                $user->id,
                $request->country_code ?? 'VN',
                $request->state_code,
                $request->discount_code
            );

            return response()->json([
                'success' => true,
                'data' => $estimate,
                'message' => 'Tính toán chi phí thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi estimate giỏ hàng', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tính toán chi phí'
            ], 500);
        }
    }
}
