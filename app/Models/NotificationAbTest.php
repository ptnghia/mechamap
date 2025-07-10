<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class NotificationAbTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'test_type',
        'notification_type',
        'status',
        'variants',
        'traffic_split',
        'target_metrics',
        'segmentation_rules',
        'start_date',
        'end_date',
        'min_sample_size',
        'confidence_level',
        'statistical_significance',
        'auto_conclude',
        'results',
        'winner_variant',
        'statistical_confidence',
        'effect_size',
        'created_by',
        'concluded_at',
        'conclusion_reason',
    ];

    protected $casts = [
        'variants' => 'array',
        'traffic_split' => 'array',
        'target_metrics' => 'array',
        'segmentation_rules' => 'array',
        'results' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'concluded_at' => 'datetime',
        'auto_conclude' => 'boolean',
        'statistical_confidence' => 'float',
        'effect_size' => 'float',
        'confidence_level' => 'float',
        'statistical_significance' => 'float',
        'min_sample_size' => 'integer',
    ];

    // Test statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_CONCLUDED = 'concluded';
    const STATUS_CANCELLED = 'cancelled';

    // Test types
    const TYPE_TITLE = 'title';
    const TYPE_MESSAGE = 'message';
    const TYPE_TIMING = 'timing';
    const TYPE_PRIORITY = 'priority';
    const TYPE_TEMPLATE = 'template';
    const TYPE_FREQUENCY = 'frequency';

    // Conclusion reasons
    const CONCLUSION_STATISTICAL_SIGNIFICANCE = 'statistical_significance';
    const CONCLUSION_DURATION_REACHED = 'duration_reached';
    const CONCLUSION_SAMPLE_SIZE_REACHED = 'sample_size_reached';
    const CONCLUSION_MANUAL = 'manual';
    const CONCLUSION_EMERGENCY_STOP = 'emergency_stop';

    /**
     * Get test participants
     */
    public function participants(): HasMany
    {
        return $this->hasMany(NotificationAbTestParticipant::class, 'ab_test_id');
    }

    /**
     * Get test metrics
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(NotificationAbTestMetric::class, 'ab_test_id');
    }

    /**
     * Get creator user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if test is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    /**
     * Check if test is concluded
     */
    public function isConcluded(): bool
    {
        return in_array($this->status, [self::STATUS_CONCLUDED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if test can be started
     */
    public function canStart(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               $this->start_date <= now() &&
               !empty($this->variants) &&
               !empty($this->traffic_split);
    }

    /**
     * Check if test should auto-conclude
     */
    public function shouldAutoConclude(): bool
    {
        if (!$this->auto_conclude || !$this->isActive()) {
            return false;
        }

        // Check if duration reached
        if ($this->end_date <= now()) {
            return true;
        }

        // Check if minimum sample size reached
        $totalParticipants = $this->participants()->count();
        if ($totalParticipants >= $this->min_sample_size) {
            // Check statistical significance
            return $this->hasStatisticalSignificance();
        }

        return false;
    }

    /**
     * Check if test has statistical significance
     */
    public function hasStatisticalSignificance(): bool
    {
        $results = $this->calculateResults();
        
        foreach ($this->target_metrics as $metric) {
            if (isset($results['metrics'][$metric]['p_value'])) {
                $pValue = $results['metrics'][$metric]['p_value'];
                if ($pValue <= $this->statistical_significance) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get variant for user
     */
    public function getVariantForUser(User $user): ?string
    {
        // Check if user is already assigned to a variant
        $participant = $this->participants()
            ->where('user_id', $user->id)
            ->first();

        if ($participant) {
            return $participant->variant;
        }

        // Check if user matches segmentation rules
        if (!$this->userMatchesSegmentation($user)) {
            return null;
        }

        // Assign user to variant based on traffic split
        return $this->assignUserToVariant($user);
    }

    /**
     * Check if user matches segmentation rules
     */
    private function userMatchesSegmentation(User $user): bool
    {
        if (empty($this->segmentation_rules)) {
            return true;
        }

        foreach ($this->segmentation_rules as $rule => $criteria) {
            switch ($rule) {
                case 'user_role':
                    if (!in_array($user->role, $criteria)) {
                        return false;
                    }
                    break;
                    
                case 'activity_level':
                    $activityLevel = $this->getUserActivityLevel($user);
                    if (!in_array($activityLevel, $criteria)) {
                        return false;
                    }
                    break;
                    
                case 'registration_date':
                    $registrationSegment = $this->getUserRegistrationSegment($user);
                    if (!in_array($registrationSegment, $criteria)) {
                        return false;
                    }
                    break;
                    
                case 'exclude_segments':
                    $userSegment = $this->getUserSegment($user);
                    if (in_array($userSegment, $criteria)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * Assign user to variant based on traffic split
     */
    private function assignUserToVariant(User $user): string
    {
        // Use user ID for consistent assignment
        $hash = crc32($user->id . $this->id);
        $percentage = abs($hash) % 100;
        
        $cumulative = 0;
        foreach ($this->traffic_split as $variant => $split) {
            $cumulative += $split;
            if ($percentage < $cumulative) {
                // Create participant record
                $this->participants()->create([
                    'user_id' => $user->id,
                    'variant' => $variant,
                    'assigned_at' => now(),
                ]);
                
                return $variant;
            }
        }

        // Fallback to first variant
        $firstVariant = array_key_first($this->traffic_split);
        $this->participants()->create([
            'user_id' => $user->id,
            'variant' => $firstVariant,
            'assigned_at' => now(),
        ]);
        
        return $firstVariant;
    }

    /**
     * Calculate test results
     */
    public function calculateResults(): array
    {
        $results = [
            'participants_by_variant' => [],
            'metrics' => [],
            'statistical_analysis' => [],
            'winner' => null,
            'confidence' => 0,
        ];

        // Get participants by variant
        $participantsByVariant = $this->participants()
            ->selectRaw('variant, COUNT(*) as count')
            ->groupBy('variant')
            ->pluck('count', 'variant')
            ->toArray();

        $results['participants_by_variant'] = $participantsByVariant;

        // Calculate metrics for each variant
        foreach ($this->target_metrics as $metric) {
            $results['metrics'][$metric] = $this->calculateMetricByVariant($metric);
        }

        // Perform statistical analysis
        $results['statistical_analysis'] = $this->performStatisticalAnalysis($results['metrics']);

        // Determine winner
        $results['winner'] = $this->determineWinner($results['metrics'], $results['statistical_analysis']);

        return $results;
    }

    /**
     * Calculate metric by variant
     */
    private function calculateMetricByVariant(string $metric): array
    {
        $metricResults = [];
        
        foreach (array_keys($this->traffic_split) as $variant) {
            $metricResults[$variant] = $this->calculateVariantMetric($variant, $metric);
        }

        return $metricResults;
    }

    /**
     * Calculate specific metric for variant
     */
    private function calculateVariantMetric(string $variant, string $metric): array
    {
        $participants = $this->participants()->where('variant', $variant)->get();
        
        if ($participants->isEmpty()) {
            return ['value' => 0, 'count' => 0];
        }

        // This would integrate with NotificationEngagementService
        // For now, return placeholder data
        return [
            'value' => rand(10, 90), // Placeholder percentage
            'count' => $participants->count(),
            'raw_data' => [],
        ];
    }

    /**
     * Perform statistical analysis
     */
    private function performStatisticalAnalysis(array $metrics): array
    {
        $analysis = [];
        
        foreach ($metrics as $metricName => $metricData) {
            $analysis[$metricName] = [
                'p_value' => $this->calculatePValue($metricData),
                'effect_size' => $this->calculateEffectSize($metricData),
                'confidence_interval' => $this->calculateConfidenceInterval($metricData),
                'statistical_method' => $this->getStatisticalMethod($metricName),
            ];
        }

        return $analysis;
    }

    /**
     * Calculate p-value (placeholder implementation)
     */
    private function calculatePValue(array $metricData): float
    {
        // This would implement actual statistical tests
        // For now, return a placeholder
        return rand(1, 100) / 1000; // 0.001 to 0.1
    }

    /**
     * Calculate effect size (placeholder implementation)
     */
    private function calculateEffectSize(array $metricData): float
    {
        // This would implement Cohen's d or similar
        // For now, return a placeholder
        return rand(1, 100) / 100; // 0.01 to 1.0
    }

    /**
     * Calculate confidence interval (placeholder implementation)
     */
    private function calculateConfidenceInterval(array $metricData): array
    {
        // This would implement actual CI calculation
        return [
            'lower' => rand(1, 50) / 100,
            'upper' => rand(51, 100) / 100,
        ];
    }

    /**
     * Get appropriate statistical method for metric
     */
    private function getStatisticalMethod(string $metric): string
    {
        $config = config('notification-ab-testing.statistics.methods');
        
        foreach ($config as $method => $details) {
            if (in_array($metric, $details['applicable_metrics'])) {
                return $method;
            }
        }

        return 'chi_square'; // Default
    }

    /**
     * Determine winner variant
     */
    private function determineWinner(array $metrics, array $analysis): ?string
    {
        $scores = [];
        
        foreach (array_keys($this->traffic_split) as $variant) {
            $scores[$variant] = 0;
            
            foreach ($this->target_metrics as $metric) {
                $metricConfig = config("notification-ab-testing.metrics.{$metric}");
                $higherIsBetter = $metricConfig['higher_is_better'] ?? true;
                
                $value = $metrics[$metric][$variant]['value'] ?? 0;
                $pValue = $analysis[$metric]['p_value'] ?? 1;
                
                // Only count if statistically significant
                if ($pValue <= $this->statistical_significance) {
                    $scores[$variant] += $higherIsBetter ? $value : (100 - $value);
                }
            }
        }

        if (empty($scores) || max($scores) === 0) {
            return null;
        }

        return array_search(max($scores), $scores);
    }

    /**
     * Helper methods for user segmentation
     */
    private function getUserActivityLevel(User $user): string
    {
        // Implement based on user activity metrics
        return 'medium'; // Placeholder
    }

    private function getUserRegistrationSegment(User $user): string
    {
        $daysSinceRegistration = $user->created_at->diffInDays(now());
        
        if ($daysSinceRegistration < 30) return 'new';
        if ($daysSinceRegistration < 90) return 'recent';
        if ($daysSinceRegistration < 365) return 'established';
        return 'veteran';
    }

    private function getUserSegment(User $user): string
    {
        // Implement user segment detection
        return 'regular_user'; // Placeholder
    }
}
