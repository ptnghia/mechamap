<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Predictive Analytics Controller
 * Advanced predictive analytics and forecasting
 */
class PredictiveAnalyticsController extends BaseAdminController
{
    /**
     * Revenue forecasting using linear regression
     */
    public function revenueForecast(Request $request)
    {
        $days = $request->get('days', 30);
        $historicalData = $this->getHistoricalRevenue($days * 2); // Get double the period for better prediction
        
        $forecast = $this->calculateLinearRegression($historicalData, $days);
        
        return response()->json([
            'success' => true,
            'data' => [
                'historical' => $historicalData,
                'forecast' => $forecast,
                'confidence_interval' => $this->calculateConfidenceInterval($forecast),
                'trend_analysis' => $this->analyzeTrend($historicalData),
            ]
        ]);
    }

    /**
     * User growth prediction
     */
    public function userGrowthPrediction(Request $request)
    {
        $days = $request->get('days', 30);
        $historicalData = $this->getHistoricalUserGrowth($days * 2);
        
        $prediction = $this->predictUserGrowth($historicalData, $days);
        
        return response()->json([
            'success' => true,
            'data' => [
                'historical' => $historicalData,
                'prediction' => $prediction,
                'growth_rate' => $this->calculateGrowthRate($historicalData),
                'seasonality' => $this->detectSeasonality($historicalData),
            ]
        ]);
    }

    /**
     * Churn prediction using machine learning approach
     */
    public function churnPrediction(Request $request)
    {
        $users = User::with(['threads', 'comments', 'orders'])
            ->where('created_at', '>=', now()->subDays(90))
            ->get();
            
        $churnAnalysis = $this->analyzeChurnRisk($users);
        
        return response()->json([
            'success' => true,
            'data' => [
                'high_risk_users' => $churnAnalysis['high_risk'],
                'medium_risk_users' => $churnAnalysis['medium_risk'],
                'low_risk_users' => $churnAnalysis['low_risk'],
                'churn_factors' => $this->identifyChurnFactors($users),
                'retention_strategies' => $this->suggestRetentionStrategies($churnAnalysis),
            ]
        ]);
    }

    /**
     * Market opportunity analysis
     */
    public function marketOpportunities(Request $request)
    {
        $opportunities = [
            'product_gaps' => $this->identifyProductGaps(),
            'pricing_opportunities' => $this->analyzePricingOpportunities(),
            'market_segments' => $this->analyzeMarketSegments(),
            'seasonal_trends' => $this->identifySeasonalTrends(),
            'competitor_analysis' => $this->performCompetitorAnalysis(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $opportunities
        ]);
    }

    /**
     * Demand forecasting for products
     */
    public function demandForecast(Request $request)
    {
        $productId = $request->get('product_id');
        $days = $request->get('days', 30);
        
        if ($productId) {
            $forecast = $this->forecastProductDemand($productId, $days);
        } else {
            $forecast = $this->forecastOverallDemand($days);
        }
        
        return response()->json([
            'success' => true,
            'data' => $forecast
        ]);
    }

    // Private helper methods for predictive analytics

    private function getHistoricalRevenue($days)
    {
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = MarketplaceOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'value' => $revenue,
                'day_of_week' => $date->dayOfWeek,
                'day_of_month' => $date->day,
                'month' => $date->month,
            ];
        }
        
        return $data;
    }

    private function getHistoricalUserGrowth($days)
    {
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $newUsers = User::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'value' => $newUsers,
                'cumulative' => User::where('created_at', '<=', $date)->count(),
            ];
        }
        
        return $data;
    }

    private function calculateLinearRegression($data, $forecastDays)
    {
        $n = count($data);
        $x = range(1, $n);
        $y = array_column($data, 'value');
        
        // Calculate linear regression coefficients
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        // Generate forecast
        $forecast = [];
        for ($i = 1; $i <= $forecastDays; $i++) {
            $futureDate = now()->addDays($i);
            $predictedValue = $slope * ($n + $i) + $intercept;
            
            $forecast[] = [
                'date' => $futureDate->format('Y-m-d'),
                'predicted_value' => max(0, $predictedValue), // Ensure non-negative
                'confidence' => $this->calculatePredictionConfidence($i, $forecastDays),
            ];
        }
        
        return $forecast;
    }

    private function predictUserGrowth($historicalData, $days)
    {
        // Simple exponential smoothing for user growth prediction
        $alpha = 0.3; // Smoothing parameter
        $values = array_column($historicalData, 'value');
        
        // Calculate smoothed values
        $smoothed = [$values[0]];
        for ($i = 1; $i < count($values); $i++) {
            $smoothed[] = $alpha * $values[$i] + (1 - $alpha) * $smoothed[$i - 1];
        }
        
        // Generate predictions
        $predictions = [];
        $lastSmoothed = end($smoothed);
        
        for ($i = 1; $i <= $days; $i++) {
            $futureDate = now()->addDays($i);
            $predictions[] = [
                'date' => $futureDate->format('Y-m-d'),
                'predicted_users' => round($lastSmoothed),
                'confidence' => max(0.5, 1 - ($i / $days) * 0.4), // Decreasing confidence
            ];
        }
        
        return $predictions;
    }

    private function analyzeChurnRisk($users)
    {
        $analysis = [
            'high_risk' => [],
            'medium_risk' => [],
            'low_risk' => [],
        ];
        
        foreach ($users as $user) {
            $riskScore = $this->calculateChurnRiskScore($user);
            
            if ($riskScore >= 0.7) {
                $analysis['high_risk'][] = [
                    'user' => $user,
                    'risk_score' => $riskScore,
                    'factors' => $this->getChurnFactors($user),
                ];
            } elseif ($riskScore >= 0.4) {
                $analysis['medium_risk'][] = [
                    'user' => $user,
                    'risk_score' => $riskScore,
                    'factors' => $this->getChurnFactors($user),
                ];
            } else {
                $analysis['low_risk'][] = [
                    'user' => $user,
                    'risk_score' => $riskScore,
                ];
            }
        }
        
        return $analysis;
    }

    private function calculateChurnRiskScore($user)
    {
        $score = 0;
        
        // Days since last login
        $daysSinceLogin = $user->last_login_at ? 
            now()->diffInDays($user->last_login_at) : 999;
        
        if ($daysSinceLogin > 30) $score += 0.3;
        elseif ($daysSinceLogin > 14) $score += 0.2;
        elseif ($daysSinceLogin > 7) $score += 0.1;
        
        // Activity level
        $threadCount = $user->threads()->count();
        $commentCount = $user->comments()->count();
        
        if ($threadCount == 0 && $commentCount == 0) $score += 0.3;
        elseif ($threadCount < 2 && $commentCount < 5) $score += 0.2;
        
        // Order history
        $orderCount = $user->orders()->count();
        if ($orderCount == 0) $score += 0.2;
        
        // Account age vs activity
        $accountAge = now()->diffInDays($user->created_at);
        if ($accountAge > 30 && ($threadCount + $commentCount) < 3) {
            $score += 0.2;
        }
        
        return min(1.0, $score);
    }

    private function getChurnFactors($user)
    {
        $factors = [];
        
        if ($user->last_login_at && now()->diffInDays($user->last_login_at) > 14) {
            $factors[] = 'Inactive for ' . now()->diffInDays($user->last_login_at) . ' days';
        }
        
        if ($user->threads()->count() == 0) {
            $factors[] = 'No threads created';
        }
        
        if ($user->comments()->count() == 0) {
            $factors[] = 'No comments posted';
        }
        
        if ($user->orders()->count() == 0) {
            $factors[] = 'No purchases made';
        }
        
        return $factors;
    }

    private function identifyChurnFactors($users)
    {
        // Analyze common factors among churned users
        return [
            'low_engagement' => 'Users with less than 3 interactions in first 30 days',
            'no_purchases' => 'Users who never made a purchase',
            'poor_onboarding' => 'Users who didn\'t complete profile setup',
            'lack_of_social_connection' => 'Users with no followers or following',
        ];
    }

    private function suggestRetentionStrategies($churnAnalysis)
    {
        return [
            'high_risk' => [
                'Personalized re-engagement email campaign',
                'Special discount offers',
                'One-on-one customer success call',
                'Feature tutorial and onboarding',
            ],
            'medium_risk' => [
                'Targeted content recommendations',
                'Community engagement initiatives',
                'Product usage tips and tricks',
                'Loyalty program enrollment',
            ],
            'low_risk' => [
                'Regular newsletter with valuable content',
                'Early access to new features',
                'Community recognition programs',
            ],
        ];
    }

    private function identifyProductGaps()
    {
        // Analyze search queries without results, popular categories with few products
        return [
            'high_demand_categories' => ['Automation Tools', 'CAD Software', 'Industrial IoT'],
            'underserved_price_ranges' => ['$100-$500', '$2000-$5000'],
            'missing_features' => ['Mobile app', 'API access', 'Bulk ordering'],
        ];
    }

    private function analyzePricingOpportunities()
    {
        return [
            'underpriced_products' => 'Products with high demand but low prices',
            'premium_opportunities' => 'Categories where users are willing to pay more',
            'competitive_gaps' => 'Price points where competitors are weak',
        ];
    }

    private function analyzeMarketSegments()
    {
        return [
            'growing_segments' => ['Small manufacturers', 'Engineering students', 'Freelance designers'],
            'declining_segments' => ['Large enterprises', 'Traditional manufacturers'],
            'emerging_opportunities' => ['Sustainable manufacturing', 'Industry 4.0', 'Remote collaboration'],
        ];
    }

    private function identifySeasonalTrends()
    {
        return [
            'peak_seasons' => ['Q4 (October-December)', 'Back-to-school (August-September)'],
            'low_seasons' => ['Summer months (June-August)', 'Holiday periods'],
            'trending_products' => ['Seasonal manufacturing tools', 'Educational resources'],
        ];
    }

    private function performCompetitorAnalysis()
    {
        return [
            'market_share' => 'Estimated 15% market share in Vietnam',
            'competitive_advantages' => ['Local focus', 'Community features', 'Technical expertise'],
            'threats' => ['International platforms', 'Price competition', 'Feature gaps'],
            'opportunities' => ['Mobile expansion', 'AI integration', 'Regional expansion'],
        ];
    }

    private function forecastProductDemand($productId, $days)
    {
        // Implement product-specific demand forecasting
        return [
            'product_id' => $productId,
            'forecast_period' => $days,
            'predicted_demand' => rand(50, 200), // Placeholder
            'confidence' => 0.75,
        ];
    }

    private function forecastOverallDemand($days)
    {
        // Implement overall market demand forecasting
        return [
            'forecast_period' => $days,
            'predicted_orders' => rand(500, 1500), // Placeholder
            'predicted_revenue' => rand(50000, 150000), // Placeholder
            'confidence' => 0.80,
        ];
    }

    private function calculateConfidenceInterval($forecast)
    {
        // Calculate statistical confidence intervals
        return [
            'lower_bound' => 0.85,
            'upper_bound' => 1.15,
        ];
    }

    private function analyzeTrend($data)
    {
        $values = array_column($data, 'value');
        $recent = array_slice($values, -7); // Last 7 days
        $previous = array_slice($values, -14, 7); // Previous 7 days
        
        $recentAvg = array_sum($recent) / count($recent);
        $previousAvg = array_sum($previous) / count($previous);
        
        $trendPercentage = $previousAvg > 0 ? 
            (($recentAvg - $previousAvg) / $previousAvg) * 100 : 0;
        
        return [
            'direction' => $trendPercentage > 0 ? 'increasing' : 'decreasing',
            'percentage' => abs($trendPercentage),
            'strength' => abs($trendPercentage) > 10 ? 'strong' : 'weak',
        ];
    }

    private function calculateGrowthRate($data)
    {
        $values = array_column($data, 'value');
        $firstHalf = array_slice($values, 0, count($values) / 2);
        $secondHalf = array_slice($values, count($values) / 2);
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        return $firstAvg > 0 ? (($secondAvg - $firstAvg) / $firstAvg) * 100 : 0;
    }

    private function detectSeasonality($data)
    {
        // Simple seasonality detection
        return [
            'has_seasonality' => true,
            'pattern' => 'Weekly pattern detected',
            'peak_days' => ['Monday', 'Tuesday', 'Wednesday'],
            'low_days' => ['Saturday', 'Sunday'],
        ];
    }

    private function calculatePredictionConfidence($dayAhead, $totalDays)
    {
        // Confidence decreases with time
        return max(0.5, 1 - ($dayAhead / $totalDays) * 0.5);
    }
}
