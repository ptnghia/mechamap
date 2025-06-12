<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Services\ShoppingCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected ShoppingCartService $cartService;

    public function __construct(OrderService $orderService, ShoppingCartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Lấy danh sách orders của user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = min($request->per_page ?? 20, 50);

            $orders = Order::where('user_id', $user->id)
                ->with(['items.product', 'transactions'])
                ->when($request->status, function($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->search, function($query) use ($request) {
                    $query->where('order_number', 'like', "%{$request->search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => 'Lấy danh sách orders thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách orders', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách orders'
            ], 500);
        }
    }

    /**
     * Tạo order từ giỏ hàng
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'billing_info' => 'required|array',
            'billing_info.full_name' => 'required|string|max:255',
            'billing_info.email' => 'required|email',
            'billing_info.phone' => 'required|string|max:20',
            'billing_info.address' => 'required|string|max:500',
            'billing_info.city' => 'required|string|max:100',
            'billing_info.country_code' => 'required|string|size:2',
            'billing_info.postal_code' => 'string|max:20',
            'discount_code' => 'string|max:50',
            'notes' => 'string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }        try {
            $user = Auth::user();            // Validate cart trước khi tạo order
            $cartValidation = $this->cartService->validateCart($user->id);
            if (!$cartValidation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng có ' . count($cartValidation['issues']) . ' vấn đề, vui lòng kiểm tra lại',
                    'data' => [
                        'issues' => $cartValidation['issues'],
                        'errors' => $cartValidation['errors'] ?? [],
                        'warnings' => $cartValidation['warnings'] ?? [],
                        'invalid_items' => $cartValidation['invalid_items'] ?? [],
                        'needs_price_update' => $cartValidation['needs_price_update'] ?? false,
                    ]
                ], 400);
            }

            // Kiểm tra nếu có price changes thì yêu cầu confirm trước
            if ($cartValidation['needs_price_update'] ?? false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giá sản phẩm đã thay đổi, vui lòng cập nhật giá trước khi đặt hàng',
                    'data' => [
                        'issues' => $cartValidation['warnings'],
                        'warnings' => $cartValidation['warnings'],
                        'needs_price_update' => true,
                    ]
                ], 422);
            }

            // Tạo order từ cart
            $order = $this->orderService->createOrderFromCart(
                $user,
                $request->billing_info,
                $request->discount_code,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order->load(['items.product', 'transactions']),
                ],
                'message' => 'Tạo order thành công'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Lỗi tạo order', [
                'user_id' => Auth::id(),
                'billing_info' => $request->billing_info,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo order'
            ], 500);
        }
    }

    /**
     * Lấy chi tiết order
     */
    public function show(int $orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->with([
                    'items.product.category',
                    'items.product.protectedFiles',
                    'transactions' => function($query) {
                        $query->orderBy('created_at', 'desc');
                    }
                ])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order,
                ],
                'message' => 'Lấy chi tiết order thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy chi tiết order', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy chi tiết order'
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin order (chỉ khi pending)
     */
    public function update(Request $request, int $orderId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'billing_info' => 'array',
            'billing_info.full_name' => 'string|max:255',
            'billing_info.email' => 'email',
            'billing_info.phone' => 'string|max:20',
            'billing_info.address' => 'string|max:500',
            'billing_info.city' => 'string|max:100',
            'billing_info.country_code' => 'string|size:2',
            'billing_info.postal_code' => 'string|max:20',
            'notes' => 'string|max:1000',
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
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            // Cập nhật thông tin có thể thay đổi
            $updateData = [];

            if ($request->has('billing_info')) {
                $updateData['billing_info'] = array_merge(
                    $order->billing_info ?? [],
                    $request->billing_info
                );
            }

            if ($request->has('notes')) {
                $updateData['notes'] = $request->notes;
            }

            if (!empty($updateData)) {
                $order->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order->fresh()->load(['items.product', 'transactions']),
                ],
                'message' => 'Cập nhật order thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order hoặc order không thể cập nhật'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật order', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật order'
            ], 500);
        }
    }

    /**
     * Hủy order (chỉ khi pending hoặc payment_pending)
     */
    public function cancel(Request $request, int $orderId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
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
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'payment_pending'])
                ->firstOrFail();

            $result = $this->orderService->cancelOrder($order, $request->reason);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'order' => $order->fresh(),
                    ],
                    'message' => 'Hủy order thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Không thể hủy order'
                ], 400);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order hoặc order không thể hủy'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi hủy order', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy order'
            ], 500);
        }
    }

    /**
     * Lấy invoice cho order đã hoàn thành
     */
    public function invoice(int $orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->with(['items.product', 'transactions'])
                ->firstOrFail();

            $invoiceData = $this->orderService->generateInvoiceData($order);

            return response()->json([
                'success' => true,
                'data' => [
                    'invoice' => $invoiceData,
                ],
                'message' => 'Lấy invoice thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order hoặc order chưa hoàn thành'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy invoice', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy invoice'
            ], 500);
        }
    }

    /**
     * Lấy danh sách downloads cho order đã hoàn thành
     */
    public function downloads(int $orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->with(['items.product.files'])
                ->firstOrFail();

            $downloads = $this->orderService->getOrderDownloads($order);

            return response()->json([
                'success' => true,
                'data' => [
                    'downloads' => $downloads,
                ],
                'message' => 'Lấy danh sách downloads thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order hoặc order chưa hoàn thành'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy downloads', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy downloads'
            ], 500);
        }
    }

    /**
     * Tái tạo order từ order cũ
     */
    public function reorder(int $orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $originalOrder = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->with(['items.product'])
                ->firstOrFail();

            $result = $this->orderService->reorderFromPreviousOrder($originalOrder);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'cart_items_added' => $result['cart_items_added'],
                        'unavailable_items' => $result['unavailable_items'] ?? [],
                    ],
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng từ order cũ'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Không thể tái tạo order'
                ], 400);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi tái tạo order', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tái tạo order'
            ], 500);
        }
    }
}
