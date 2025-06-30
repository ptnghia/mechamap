<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationWorkflow;
use App\Models\AutomationTrigger;
use App\Models\AutomationAction;
use App\Models\AutomationExecution;
use App\Services\WorkflowBuilderService;
use App\Services\AutomationEngineService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Automation Controller
 * Visual workflow builder and automation management
 */
class AutomationController extends BaseAdminController
{
    protected $workflowBuilder;
    protected $automationEngine;

    public function __construct(
        WorkflowBuilderService $workflowBuilder,
        AutomationEngineService $automationEngine
    ) {
        parent::__construct();
        $this->workflowBuilder = $workflowBuilder;
        $this->automationEngine = $automationEngine;
    }

    /**
     * Display automation dashboard
     */
    public function index(): View
    {
        $workflows = AutomationWorkflow::with(['triggers', 'actions'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_workflows' => AutomationWorkflow::count(),
            'active_workflows' => AutomationWorkflow::where('is_active', true)->count(),
            'total_executions' => AutomationExecution::count(),
            'successful_executions' => AutomationExecution::where('status', 'success')->count(),
        ];

        $recentExecutions = AutomationExecution::with(['workflow'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.automation.index', compact(
            'workflows',
            'stats',
            'recentExecutions'
        ));
    }

    /**
     * Show workflow builder interface
     */
    public function builder(Request $request): View
    {
        $workflowId = $request->get('workflow_id');
        $workflow = $workflowId ? AutomationWorkflow::findOrFail($workflowId) : null;

        $availableTriggers = $this->workflowBuilder->getAvailableTriggers();
        $availableActions = $this->workflowBuilder->getAvailableActions();
        $availableConditions = $this->workflowBuilder->getAvailableConditions();

        return view('admin.automation.builder', compact(
            'workflow',
            'availableTriggers',
            'availableActions',
            'availableConditions'
        ));
    }

    /**
     * Create new workflow
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'triggers' => 'required|array|min:1',
            'actions' => 'required|array|min:1',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        try {
            $workflow = $this->workflowBuilder->createWorkflow([
                'name' => $request->name,
                'description' => $request->description,
                'triggers' => $request->triggers,
                'actions' => $request->actions,
                'conditions' => $request->conditions ?? [],
                'is_active' => $request->is_active ?? true,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing workflow
     */
    public function update(Request $request, AutomationWorkflow $workflow): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'triggers' => 'required|array|min:1',
            'actions' => 'required|array|min:1',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        try {
            $updatedWorkflow = $this->workflowBuilder->updateWorkflow($workflow, [
                'name' => $request->name,
                'description' => $request->description,
                'triggers' => $request->triggers,
                'actions' => $request->actions,
                'conditions' => $request->conditions ?? [],
                'is_active' => $request->is_active ?? true,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $updatedWorkflow,
                'message' => 'Workflow updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete workflow
     */
    public function destroy(AutomationWorkflow $workflow): JsonResponse
    {
        try {
            $this->workflowBuilder->deleteWorkflow($workflow);

            return response()->json([
                'success' => true,
                'message' => 'Workflow deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle workflow active status
     */
    public function toggle(AutomationWorkflow $workflow): JsonResponse
    {
        try {
            $workflow->update(['is_active' => !$workflow->is_active]);

            return response()->json([
                'success' => true,
                'data' => ['is_active' => $workflow->is_active],
                'message' => $workflow->is_active ? 'Workflow activated' : 'Workflow deactivated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test workflow execution
     */
    public function test(Request $request, AutomationWorkflow $workflow): JsonResponse
    {
        $request->validate([
            'test_data' => 'nullable|array',
        ]);

        try {
            $result = $this->automationEngine->testWorkflow($workflow, $request->test_data ?? []);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Workflow test completed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Workflow test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow execution history
     */
    public function executions(AutomationWorkflow $workflow): JsonResponse
    {
        $executions = AutomationExecution::where('workflow_id', $workflow->id)
            ->with(['triggerData', 'actionResults'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $executions
        ]);
    }

    /**
     * Get workflow analytics
     */
    public function analytics(AutomationWorkflow $workflow): JsonResponse
    {
        $analytics = [
            'total_executions' => $workflow->executions()->count(),
            'successful_executions' => $workflow->executions()->where('status', 'success')->count(),
            'failed_executions' => $workflow->executions()->where('status', 'failed')->count(),
            'average_execution_time' => $workflow->executions()->avg('execution_time_ms'),
            'last_execution' => $workflow->executions()->latest()->first(),
            'execution_trend' => $this->getExecutionTrend($workflow),
            'success_rate' => $this->getSuccessRate($workflow),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Duplicate workflow
     */
    public function duplicate(AutomationWorkflow $workflow): JsonResponse
    {
        try {
            $duplicatedWorkflow = $this->workflowBuilder->duplicateWorkflow($workflow, [
                'name' => $workflow->name . ' (Copy)',
                'is_active' => false,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $duplicatedWorkflow,
                'message' => 'Workflow duplicated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export workflow
     */
    public function export(AutomationWorkflow $workflow): JsonResponse
    {
        try {
            $exportData = $this->workflowBuilder->exportWorkflow($workflow);

            return response()->json([
                'success' => true,
                'data' => $exportData,
                'filename' => 'workflow-' . $workflow->id . '.json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import workflow
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'workflow_data' => 'required|json',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            $workflowData = json_decode($request->workflow_data, true);
            
            if ($request->name) {
                $workflowData['name'] = $request->name;
            }
            
            $workflowData['created_by'] = auth()->id();
            $workflowData['is_active'] = false; // Import as inactive by default

            $workflow = $this->workflowBuilder->importWorkflow($workflowData);

            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow imported successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow templates
     */
    public function templates(): JsonResponse
    {
        $templates = $this->workflowBuilder->getWorkflowTemplates();

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    /**
     * Create workflow from template
     */
    public function createFromTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|string',
            'name' => 'required|string|max:255',
            'parameters' => 'nullable|array',
        ]);

        try {
            $workflow = $this->workflowBuilder->createFromTemplate(
                $request->template_id,
                $request->name,
                $request->parameters ?? [],
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow created from template successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create workflow from template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system triggers and actions
     */
    public function systemComponents(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'triggers' => $this->workflowBuilder->getAvailableTriggers(),
                'actions' => $this->workflowBuilder->getAvailableActions(),
                'conditions' => $this->workflowBuilder->getAvailableConditions(),
                'variables' => $this->workflowBuilder->getAvailableVariables(),
            ]
        ]);
    }

    // Private helper methods

    private function getExecutionTrend(AutomationWorkflow $workflow): array
    {
        $days = 30;
        $trend = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $executions = $workflow->executions()
                ->whereDate('created_at', $date)
                ->count();
            
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'executions' => $executions,
            ];
        }
        
        return $trend;
    }

    private function getSuccessRate(AutomationWorkflow $workflow): float
    {
        $total = $workflow->executions()->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $successful = $workflow->executions()->where('status', 'success')->count();
        
        return round(($successful / $total) * 100, 2);
    }
}
