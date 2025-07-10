<?php

namespace App\Http\Controllers;

use App\Models\TypingIndicator;
use App\Services\TypingIndicatorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TypingIndicatorController extends Controller
{
    /**
     * Start typing indicator
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'context_type' => 'required|string|in:thread,comment,message,showcase',
            'context_id' => 'required|integer|min:1',
            'typing_type' => 'string|in:comment,reply,message,thread',
            'metadata' => 'array',
        ]);

        $user = auth()->user();
        $contextType = $request->input('context_type');
        $contextId = $request->input('context_id');
        $typingType = $request->input('typing_type', TypingIndicator::TYPE_COMMENT);
        $metadata = $request->input('metadata', []);

        $result = TypingIndicatorService::startTyping(
            $user->id,
            $contextType,
            $contextId,
            $typingType,
            $metadata
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Update typing activity
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'context_type' => 'required|string|in:thread,comment,message,showcase',
            'context_id' => 'required|integer|min:1',
            'typing_type' => 'string|in:comment,reply,message,thread',
            'extension_seconds' => 'integer|min:5|max:300',
        ]);

        $user = auth()->user();
        $contextType = $request->input('context_type');
        $contextId = $request->input('context_id');
        $typingType = $request->input('typing_type', TypingIndicator::TYPE_COMMENT);
        $extensionSeconds = $request->input('extension_seconds');

        $result = TypingIndicatorService::updateTyping(
            $user->id,
            $contextType,
            $contextId,
            $typingType,
            $extensionSeconds
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Stop typing indicator
     */
    public function stop(Request $request): JsonResponse
    {
        $request->validate([
            'context_type' => 'required|string|in:thread,comment,message,showcase',
            'context_id' => 'required|integer|min:1',
            'typing_type' => 'string|in:comment,reply,message,thread',
        ]);

        $user = auth()->user();
        $contextType = $request->input('context_type');
        $contextId = $request->input('context_id');
        $typingType = $request->input('typing_type', TypingIndicator::TYPE_COMMENT);

        $result = TypingIndicatorService::stopTyping(
            $user->id,
            $contextType,
            $contextId,
            $typingType
        );

        return response()->json($result);
    }

    /**
     * Get active typing indicators for context
     */
    public function getActive(Request $request): JsonResponse
    {
        $request->validate([
            'context_type' => 'required|string|in:thread,comment,message,showcase',
            'context_id' => 'required|integer|min:1',
            'typing_type' => 'string|in:comment,reply,message,thread',
            'exclude_self' => 'boolean',
        ]);

        $contextType = $request->input('context_type');
        $contextId = $request->input('context_id');
        $typingType = $request->input('typing_type');
        $excludeSelf = $request->boolean('exclude_self', true);
        $excludeUserId = $excludeSelf ? auth()->id() : null;

        $indicators = TypingIndicatorService::getActiveIndicators(
            $contextType,
            $contextId,
            $typingType,
            $excludeUserId
        );

        return response()->json([
            'success' => true,
            'data' => [
                'indicators' => $indicators,
                'count' => count($indicators),
                'context' => [
                    'type' => $contextType,
                    'id' => $contextId,
                    'typing_type' => $typingType,
                ],
            ],
        ]);
    }

    /**
     * Get current user's typing contexts
     */
    public function myTypingContexts(Request $request): JsonResponse
    {
        $user = auth()->user();
        $contexts = TypingIndicatorService::getUserTypingContexts($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'contexts' => $contexts,
                'count' => count($contexts),
            ],
        ]);
    }

    /**
     * Stop all typing indicators for current user
     */
    public function stopAll(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $deleted = TypingIndicator::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All typing indicators stopped',
            'data' => [
                'stopped_count' => $deleted,
            ],
        ]);
    }

    /**
     * Get typing statistics (admin)
     */
    public function statistics(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('view_admin_statistics')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $statistics = TypingIndicatorService::getTypingStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Clean up expired indicators (admin)
     */
    public function cleanup(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('manage_notifications')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $deleted = TypingIndicatorService::cleanupExpired();

        return response()->json([
            'success' => true,
            'message' => 'Cleanup completed',
            'data' => [
                'deleted_count' => $deleted,
            ],
        ]);
    }

    /**
     * Heartbeat endpoint for keeping typing indicators alive
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $request->validate([
            'indicators' => 'required|array',
            'indicators.*.context_type' => 'required|string|in:thread,comment,message,showcase',
            'indicators.*.context_id' => 'required|integer|min:1',
            'indicators.*.typing_type' => 'string|in:comment,reply,message,thread',
        ]);

        $user = auth()->user();
        $indicators = $request->input('indicators');
        $results = [];

        foreach ($indicators as $indicatorData) {
            $contextType = $indicatorData['context_type'];
            $contextId = $indicatorData['context_id'];
            $typingType = $indicatorData['typing_type'] ?? TypingIndicator::TYPE_COMMENT;

            $result = TypingIndicatorService::updateTyping(
                $user->id,
                $contextType,
                $contextId,
                $typingType
            );

            $results[] = [
                'context_type' => $contextType,
                'context_id' => $contextId,
                'typing_type' => $typingType,
                'success' => $result['success'],
                'message' => $result['message'],
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat processed',
            'data' => [
                'results' => $results,
                'processed_count' => count($results),
            ],
        ]);
    }

    /**
     * Bulk operations for typing indicators
     */
    public function bulk(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:start,stop,update',
            'indicators' => 'required|array|min:1|max:10',
            'indicators.*.context_type' => 'required|string|in:thread,comment,message,showcase',
            'indicators.*.context_id' => 'required|integer|min:1',
            'indicators.*.typing_type' => 'string|in:comment,reply,message,thread',
            'indicators.*.metadata' => 'array',
        ]);

        $user = auth()->user();
        $action = $request->input('action');
        $indicators = $request->input('indicators');
        $results = [];

        foreach ($indicators as $indicatorData) {
            $contextType = $indicatorData['context_type'];
            $contextId = $indicatorData['context_id'];
            $typingType = $indicatorData['typing_type'] ?? TypingIndicator::TYPE_COMMENT;
            $metadata = $indicatorData['metadata'] ?? [];

            switch ($action) {
                case 'start':
                    $result = TypingIndicatorService::startTyping(
                        $user->id,
                        $contextType,
                        $contextId,
                        $typingType,
                        $metadata
                    );
                    break;

                case 'stop':
                    $result = TypingIndicatorService::stopTyping(
                        $user->id,
                        $contextType,
                        $contextId,
                        $typingType
                    );
                    break;

                case 'update':
                    $result = TypingIndicatorService::updateTyping(
                        $user->id,
                        $contextType,
                        $contextId,
                        $typingType
                    );
                    break;

                default:
                    $result = [
                        'success' => false,
                        'message' => 'Invalid action',
                    ];
            }

            $results[] = [
                'context_type' => $contextType,
                'context_id' => $contextId,
                'typing_type' => $typingType,
                'success' => $result['success'],
                'message' => $result['message'],
            ];
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Bulk {$action} completed",
            'data' => [
                'results' => $results,
                'total_count' => count($results),
                'success_count' => $successCount,
                'error_count' => count($results) - $successCount,
            ],
        ]);
    }
}
