<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * AutomationWorkflow Model
 * Represents an automation workflow with triggers, conditions, and actions
 */
class AutomationWorkflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'workflow_data',
        'is_active',
        'execution_count',
        'success_count',
        'failure_count',
        'last_executed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'workflow_data' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this workflow
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this workflow
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all triggers for this workflow
     */
    public function triggers(): HasMany
    {
        return $this->hasMany(AutomationTrigger::class, 'workflow_id');
    }

    /**
     * Get all actions for this workflow
     */
    public function actions(): HasMany
    {
        return $this->hasMany(AutomationAction::class, 'workflow_id');
    }

    /**
     * Get all executions for this workflow
     */
    public function executions(): HasMany
    {
        return $this->hasMany(AutomationExecution::class, 'workflow_id');
    }

    /**
     * Get workflow success rate
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->execution_count === 0) {
            return 0;
        }

        return round(($this->success_count / $this->execution_count) * 100, 2);
    }

    /**
     * Get workflow status
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->execution_count === 0) {
            return 'never_executed';
        }

        $recentFailures = $this->executions()
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', 'failed')
            ->count();

        if ($recentFailures >= 5) {
            return 'failing';
        }

        return 'active';
    }

    /**
     * Check if workflow can be executed
     */
    public function canExecute(): bool
    {
        return $this->is_active && 
               $this->triggers()->exists() && 
               $this->actions()->exists();
    }

    /**
     * Record workflow execution
     */
    public function recordExecution(string $status, array $data = []): AutomationExecution
    {
        $execution = $this->executions()->create([
            'status' => $status,
            'execution_data' => $data,
            'execution_time_ms' => $data['execution_time_ms'] ?? null,
            'triggered_by' => $data['triggered_by'] ?? null,
        ]);

        // Update workflow statistics
        $this->increment('execution_count');
        
        if ($status === 'success') {
            $this->increment('success_count');
        } elseif ($status === 'failed') {
            $this->increment('failure_count');
        }

        $this->update(['last_executed_at' => now()]);

        return $execution;
    }

    /**
     * Get workflow configuration
     */
    public function getConfiguration(): array
    {
        return [
            'triggers' => $this->triggers->map(function ($trigger) {
                return [
                    'id' => $trigger->id,
                    'type' => $trigger->trigger_type,
                    'config' => $trigger->trigger_config,
                    'conditions' => $trigger->conditions,
                ];
            })->toArray(),
            'actions' => $this->actions->map(function ($action) {
                return [
                    'id' => $action->id,
                    'type' => $action->action_type,
                    'config' => $action->action_config,
                    'order' => $action->execution_order,
                ];
            })->toArray(),
            'workflow_data' => $this->workflow_data,
        ];
    }

    /**
     * Scope for active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for workflows by creator
     */
    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope for recently executed workflows
     */
    public function scopeRecentlyExecuted($query, $hours = 24)
    {
        return $query->where('last_executed_at', '>=', now()->subHours($hours));
    }
}

/**
 * AutomationTrigger Model
 * Represents a trigger that can start a workflow
 */
class AutomationTrigger extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'trigger_type',
        'trigger_config',
        'conditions',
        'is_active',
        'execution_count',
        'last_triggered_at',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'conditions' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * Get the workflow this trigger belongs to
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'workflow_id');
    }

    /**
     * Check if trigger conditions are met
     */
    public function checkConditions(array $data): bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if (!$this->evaluateCondition($condition, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate a single condition
     */
    private function evaluateCondition(array $condition, array $data): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? null;

        if (!$field || !isset($data[$field])) {
            return false;
        }

        $fieldValue = $data[$field];

        switch ($operator) {
            case 'equals':
                return $fieldValue == $value;
            case 'not_equals':
                return $fieldValue != $value;
            case 'greater_than':
                return $fieldValue > $value;
            case 'less_than':
                return $fieldValue < $value;
            case 'contains':
                return str_contains($fieldValue, $value);
            case 'not_contains':
                return !str_contains($fieldValue, $value);
            case 'in':
                return in_array($fieldValue, (array) $value);
            case 'not_in':
                return !in_array($fieldValue, (array) $value);
            default:
                return false;
        }
    }

    /**
     * Record trigger execution
     */
    public function recordTrigger(): void
    {
        $this->increment('execution_count');
        $this->update(['last_triggered_at' => now()]);
    }
}

/**
 * AutomationAction Model
 * Represents an action that can be executed in a workflow
 */
class AutomationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'action_type',
        'action_config',
        'execution_order',
        'is_active',
        'execution_count',
        'success_count',
        'failure_count',
        'last_executed_at',
    ];

    protected $casts = [
        'action_config' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
    ];

    /**
     * Get the workflow this action belongs to
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'workflow_id');
    }

    /**
     * Record action execution
     */
    public function recordExecution(string $status): void
    {
        $this->increment('execution_count');
        
        if ($status === 'success') {
            $this->increment('success_count');
        } elseif ($status === 'failed') {
            $this->increment('failure_count');
        }

        $this->update(['last_executed_at' => now()]);
    }

    /**
     * Get action success rate
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->execution_count === 0) {
            return 0;
        }

        return round(($this->success_count / $this->execution_count) * 100, 2);
    }

    /**
     * Scope for actions by execution order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('execution_order');
    }
}

/**
 * AutomationExecution Model
 * Represents a single execution of a workflow
 */
class AutomationExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'status',
        'execution_data',
        'execution_time_ms',
        'error_message',
        'triggered_by',
    ];

    protected $casts = [
        'execution_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the workflow this execution belongs to
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'workflow_id');
    }

    /**
     * Get the user who triggered this execution
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Scope for successful executions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed executions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for recent executions
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
