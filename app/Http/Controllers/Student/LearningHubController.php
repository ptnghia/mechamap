<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\EducationalResource;
use App\Models\StudyGroup;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LearningHubController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
    }

    /**
     * Display the student learning hub dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get student statistics
        $stats = $this->getStudentStats($user);
        
        // Get resource counts by category
        $resourceCounts = $this->getResourceCounts();
        
        // Get recent resources
        $recentResources = $this->getRecentResources();
        
        // Get learning progress
        $progress = $this->getLearningProgress($user);
        
        // Get study groups
        $studyGroups = $this->getStudyGroups($user);
        
        return view('student.learning-hub', compact(
            'stats',
            'resourceCounts', 
            'recentResources',
            'progress',
            'studyGroups'
        ));
    }

    /**
     * Get student statistics.
     */
    private function getStudentStats(User $user): array
    {
        return Cache::remember("student_stats_{$user->id}", 300, function() use ($user) {
            return [
                'courses_enrolled' => $user->enrolledCourses()->count(),
                'completed_lessons' => $user->completedLessons()->count(),
                'study_hours' => $user->studySessions()->sum('duration_minutes') / 60,
                'achievements' => $user->achievements()->count(),
            ];
        });
    }

    /**
     * Get resource counts by category.
     */
    private function getResourceCounts(): array
    {
        return Cache::remember('resource_counts', 600, function() {
            return [
                'textbooks' => EducationalResource::where('category', 'textbook')->count(),
                'videos' => EducationalResource::where('category', 'video')->count(),
                'papers' => EducationalResource::where('category', 'research_paper')->count(),
                'tools' => EducationalResource::where('category', 'software_tool')->count(),
            ];
        });
    }

    /**
     * Get recent educational resources.
     */
    private function getRecentResources()
    {
        return EducationalResource::with(['user', 'category'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get learning progress for user.
     */
    private function getLearningProgress(User $user): array
    {
        $progress = StudentProgress::where('user_id', $user->id)->first();
        
        if (!$progress) {
            return [
                'overall' => 0,
                'current_course' => 0,
            ];
        }

        return [
            'overall' => $progress->overall_progress ?? 0,
            'current_course' => $progress->current_course_progress ?? 0,
        ];
    }

    /**
     * Get study groups for user.
     */
    private function getStudyGroups(User $user)
    {
        return StudyGroup::whereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->withCount('members')
            ->limit(3)
            ->get();
    }

    /**
     * Display educational resources by category.
     */
    public function resourcesByCategory(Request $request, string $category): View
    {
        $resources = EducationalResource::where('category', $category)
            ->where('is_published', true)
            ->with(['user', 'downloads'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categoryName = $this->getCategoryName($category);

        return view('student.resources.category', compact('resources', 'category', 'categoryName'));
    }

    /**
     * Display a specific educational resource.
     */
    public function showResource(Request $request, int $id): View
    {
        $resource = EducationalResource::with(['user', 'downloads', 'reviews'])
            ->findOrFail($id);

        // Track view
        $resource->increment('view_count');

        // Get related resources
        $relatedResources = EducationalResource::where('category', $resource->category)
            ->where('id', '!=', $resource->id)
            ->where('is_published', true)
            ->limit(4)
            ->get();

        return view('student.resources.show', compact('resource', 'relatedResources'));
    }

    /**
     * Get category display name.
     */
    private function getCategoryName(string $category): string
    {
        $categories = [
            'textbook' => __('student.textbooks'),
            'video' => __('student.video_tutorials'),
            'research_paper' => __('student.research_papers'),
            'software_tool' => __('student.tools_software'),
        ];

        return $categories[$category] ?? ucfirst($category);
    }

    /**
     * Display learning path for student.
     */
    public function learningPath(): View
    {
        $user = Auth::user();
        
        // Get or create learning path
        $learningPath = $user->learningPath ?? $this->createDefaultLearningPath($user);
        
        // Get progress data
        $progress = $this->getLearningProgress($user);
        
        // Get recommended next steps
        $nextSteps = $this->getRecommendedNextSteps($user);
        
        return view('student.learning-path', compact('learningPath', 'progress', 'nextSteps'));
    }

    /**
     * Create default learning path for new student.
     */
    private function createDefaultLearningPath(User $user)
    {
        // This would create a default learning path based on student's profile
        // For now, return a basic structure
        return (object) [
            'name' => __('student.default_learning_path'),
            'description' => __('student.default_path_description'),
            'milestones' => [],
            'current_milestone' => 0,
        ];
    }

    /**
     * Get recommended next steps for student.
     */
    private function getRecommendedNextSteps(User $user): array
    {
        // This would use AI/ML to recommend next steps
        // For now, return basic recommendations
        return [
            [
                'title' => __('student.complete_profile'),
                'description' => __('student.complete_profile_desc'),
                'action_url' => route('profile.edit'),
                'priority' => 'high',
            ],
            [
                'title' => __('student.join_study_group'),
                'description' => __('student.join_study_group_desc'),
                'action_url' => route('student.study-groups.index'),
                'priority' => 'medium',
            ],
            [
                'title' => __('student.start_first_project'),
                'description' => __('student.start_first_project_desc'),
                'action_url' => route('student.projects.create'),
                'priority' => 'medium',
            ],
        ];
    }
}
