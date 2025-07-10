<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationAbTest;
use App\Models\NotificationAbTestParticipant;
use App\Models\NotificationAbTestEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class NotificationAbTestService
{
    /**
     * Check if A/B testing is enabled
     */
    public static function isEnabled(): bool
    {
        return config('notification-ab-testing.enabled', false);
    }

    /**
     * Get active test for notification type
     */
    public static function getActiveTest(string $notificationType): ?NotificationAbTest
    {
        if (!static::isEnabled()) {
            return null;
        }

        $cacheKey = "ab_test_active_{$notificationType}";
        
        return Cache::remember($cacheKey, 300, function () use ($notificationType) {
            return NotificationAbTest::where('notification_type', $notificationType)
                ->where('status', NotificationAbTest::STATUS_ACTIVE)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
        });
    }

    /**
     * Apply A/B test to notification
     */
    public static function applyTestToNotification(Notification $notification, User $user): Notification
    {
        try {
            $test = static::getActiveTest($notification->type);
            
            if (!$test) {
                return $notification;
            }

            $variant = $test->getVariantForUser($user);
            
            if (!$variant) {
                return $notification;
            }

            // Apply variant modifications
            $modifiedNotification = static::applyVariantModifications($notification, $test, $variant);

            // Record test participation
            static::recordTestParticipation($test, $user, $variant, $modifiedNotification);

            Log::debug("Applied A/B test variant", [
                'test_id' => $test->id,
                'user_id' => $user->id,
                'variant' => $variant,
                'notification_type' => $notification->type,
            ]);

            return $modifiedNotification;

        } catch (Exception $e) {
            Log::error("Failed to apply A/B test", [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return $notification;
        }
    }

    /**
     * Apply variant modifications to notification
     */
    private static function applyVariantModifications(
        Notification $notification, 
        NotificationAbTest $test, 
        string $variant
    ): Notification {
        $variants = $test->variants;
        $variantData = $variants[$variant] ?? null;

        if (!$variantData) {
            return $notification;
        }

        $modifiedNotification = clone $notification;

        switch ($test->test_type) {
            case NotificationAbTest::TYPE_TITLE:
                $modifiedNotification->title = static::processTemplate($variantData, $notification);
                break;

            case NotificationAbTest::TYPE_MESSAGE:
                $modifiedNotification->message = static::processTemplate($variantData, $notification);
                break;

            case NotificationAbTest::TYPE_PRIORITY:
                $modifiedNotification->priority = $variantData;
                break;

            case NotificationAbTest::TYPE_TEMPLATE:
                $data = $modifiedNotification->data ?? [];
                $data['template'] = $variantData;
                $modifiedNotification->data = $data;
                break;

            case NotificationAbTest::TYPE_TIMING:
                // Timing modifications would be handled in the scheduling logic
                $data = $modifiedNotification->data ?? [];
                $data['ab_test_timing'] = $variantData;
                $modifiedNotification->data = $data;
                break;
        }

        // Add A/B test metadata
        $data = $modifiedNotification->data ?? [];
        $data['ab_test'] = [
            'test_id' => $test->id,
            'variant' => $variant,
            'test_type' => $test->test_type,
        ];
        $modifiedNotification->data = $data;

        return $modifiedNotification;
    }

    /**
     * Process template with notification data
     */
    private static function processTemplate(string $template, Notification $notification): string
    {
        $data = $notification->data ?? [];
        
        // Replace placeholders
        $processed = $template;
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $processed = str_replace("{{$key}}", $value, $processed);
            }
        }

        return $processed;
    }

    /**
     * Record test participation
     */
    private static function recordTestParticipation(
        NotificationAbTest $test, 
        User $user, 
        string $variant, 
        Notification $notification
    ): void {
        $participant = NotificationAbTestParticipant::firstOrCreate([
            'ab_test_id' => $test->id,
            'user_id' => $user->id,
        ], [
            'variant' => $variant,
            'assigned_at' => now(),
        ]);

        // Record notification sent event
        $participant->recordNotificationSent();

        // Create event record
        NotificationAbTestEvent::create([
            'participant_id' => $participant->id,
            'notification_id' => $notification->id,
            'event_type' => NotificationAbTestEvent::EVENT_NOTIFICATION_SENT,
            'event_data' => [
                'variant' => $variant,
                'test_type' => $test->test_type,
            ],
            'occurred_at' => now(),
        ]);
    }

    /**
     * Record notification engagement
     */
    public static function recordEngagement(
        Notification $notification, 
        User $user, 
        string $eventType, 
        array $context = []
    ): void {
        try {
            $abTestData = $notification->data['ab_test'] ?? null;
            
            if (!$abTestData) {
                return;
            }

            $test = NotificationAbTest::find($abTestData['test_id']);
            if (!$test || !$test->isActive()) {
                return;
            }

            $participant = NotificationAbTestParticipant::where('ab_test_id', $test->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$participant) {
                return;
            }

            // Record engagement based on event type
            switch ($eventType) {
                case 'view':
                case 'open':
                    $participant->recordNotificationOpened();
                    $eventType = NotificationAbTestEvent::EVENT_NOTIFICATION_OPENED;
                    break;

                case 'click':
                    $participant->recordNotificationClicked();
                    $eventType = NotificationAbTestEvent::EVENT_NOTIFICATION_CLICKED;
                    break;

                case 'dismiss':
                    $participant->recordNotificationDismissed();
                    $eventType = NotificationAbTestEvent::EVENT_NOTIFICATION_DISMISSED;
                    break;

                case 'action':
                    $action = $context['action'] ?? 'unknown';
                    $participant->recordActionTaken($action, $context);
                    $eventType = NotificationAbTestEvent::EVENT_ACTION_TAKEN;
                    break;

                case 'conversion':
                    $value = $context['value'] ?? 0;
                    $participant->recordConversion($value);
                    $eventType = NotificationAbTestEvent::EVENT_CONVERSION;
                    break;
            }

            // Create event record
            NotificationAbTestEvent::create([
                'participant_id' => $participant->id,
                'notification_id' => $notification->id,
                'event_type' => $eventType,
                'event_data' => array_merge($context, [
                    'variant' => $abTestData['variant'],
                ]),
                'occurred_at' => now(),
            ]);

            Log::debug("Recorded A/B test engagement", [
                'test_id' => $test->id,
                'user_id' => $user->id,
                'event_type' => $eventType,
                'variant' => $abTestData['variant'],
            ]);

        } catch (Exception $e) {
            Log::error("Failed to record A/B test engagement", [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create new A/B test
     */
    public static function createTest(array $testData): NotificationAbTest
    {
        $test = NotificationAbTest::create([
            'name' => $testData['name'],
            'description' => $testData['description'] ?? '',
            'test_type' => $testData['test_type'],
            'notification_type' => $testData['notification_type'],
            'status' => NotificationAbTest::STATUS_DRAFT,
            'variants' => $testData['variants'],
            'traffic_split' => $testData['traffic_split'] ?? config('notification-ab-testing.defaults.traffic_split'),
            'target_metrics' => $testData['target_metrics'],
            'segmentation_rules' => $testData['segmentation_rules'] ?? [],
            'start_date' => Carbon::parse($testData['start_date']),
            'end_date' => Carbon::parse($testData['end_date']),
            'min_sample_size' => $testData['min_sample_size'] ?? config('notification-ab-testing.defaults.min_sample_size'),
            'confidence_level' => $testData['confidence_level'] ?? config('notification-ab-testing.defaults.confidence_level'),
            'statistical_significance' => $testData['statistical_significance'] ?? config('notification-ab-testing.defaults.statistical_significance'),
            'auto_conclude' => $testData['auto_conclude'] ?? config('notification-ab-testing.defaults.auto_conclude'),
            'created_by' => auth()->id(),
        ]);

        Log::info("Created A/B test", [
            'test_id' => $test->id,
            'name' => $test->name,
            'type' => $test->test_type,
            'notification_type' => $test->notification_type,
        ]);

        return $test;
    }

    /**
     * Start A/B test
     */
    public static function startTest(NotificationAbTest $test): bool
    {
        try {
            if (!$test->canStart()) {
                throw new Exception("Test cannot be started in current state");
            }

            // Check for conflicting tests
            $conflictingTest = NotificationAbTest::where('notification_type', $test->notification_type)
                ->where('status', NotificationAbTest::STATUS_ACTIVE)
                ->where('id', '!=', $test->id)
                ->first();

            if ($conflictingTest) {
                throw new Exception("Another test is already active for this notification type");
            }

            $test->update(['status' => NotificationAbTest::STATUS_ACTIVE]);

            // Clear cache
            Cache::forget("ab_test_active_{$test->notification_type}");

            Log::info("Started A/B test", [
                'test_id' => $test->id,
                'name' => $test->name,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to start A/B test", [
                'test_id' => $test->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Conclude A/B test
     */
    public static function concludeTest(NotificationAbTest $test, string $reason = null): bool
    {
        try {
            $results = $test->calculateResults();
            
            $test->update([
                'status' => NotificationAbTest::STATUS_CONCLUDED,
                'results' => $results,
                'winner_variant' => $results['winner'],
                'statistical_confidence' => $results['confidence'] ?? 0,
                'concluded_at' => now(),
                'conclusion_reason' => $reason ?? NotificationAbTest::CONCLUSION_MANUAL,
            ]);

            // Clear cache
            Cache::forget("ab_test_active_{$test->notification_type}");

            Log::info("Concluded A/B test", [
                'test_id' => $test->id,
                'winner' => $results['winner'],
                'reason' => $reason,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to conclude A/B test", [
                'test_id' => $test->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Check and auto-conclude tests
     */
    public static function checkAutoConcludeTests(): int
    {
        $concluded = 0;
        
        $activeTests = NotificationAbTest::where('status', NotificationAbTest::STATUS_ACTIVE)
            ->where('auto_conclude', true)
            ->get();

        foreach ($activeTests as $test) {
            if ($test->shouldAutoConclude()) {
                $reason = $test->end_date <= now() ? 
                    NotificationAbTest::CONCLUSION_DURATION_REACHED :
                    NotificationAbTest::CONCLUSION_STATISTICAL_SIGNIFICANCE;

                if (static::concludeTest($test, $reason)) {
                    $concluded++;
                }
            }
        }

        if ($concluded > 0) {
            Log::info("Auto-concluded A/B tests", ['count' => $concluded]);
        }

        return $concluded;
    }

    /**
     * Get test summary
     */
    public static function getTestSummary(NotificationAbTest $test): array
    {
        $results = $test->calculateResults();
        
        return [
            'test' => [
                'id' => $test->id,
                'name' => $test->name,
                'status' => $test->status,
                'type' => $test->test_type,
                'notification_type' => $test->notification_type,
                'start_date' => $test->start_date,
                'end_date' => $test->end_date,
                'duration_days' => $test->start_date->diffInDays($test->end_date),
            ],
            'participants' => $results['participants_by_variant'],
            'metrics' => $results['metrics'],
            'winner' => $results['winner'],
            'statistical_analysis' => $results['statistical_analysis'],
            'is_concluded' => $test->isConcluded(),
            'conclusion_reason' => $test->conclusion_reason,
        ];
    }
}
