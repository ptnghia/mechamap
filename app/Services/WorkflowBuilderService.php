<?php

namespace App\Services;

use App\Models\AutomationWorkflow;
use App\Models\AutomationTrigger;
use App\Models\AutomationAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Workflow Builder Service
 * Handles creation, modification, and management of automation workflows
 */
class WorkflowBuilderService
{
    /**
     * Available trigger types
     */
    public function getAvailableTriggers(): array
    {
        return [
            'user_registered' => [
                'name' => 'User Registered',
                'description' => 'Triggered when a new user registers',
                'category' => 'User Events',
                'icon' => 'fas fa-user-plus',
                'config_fields' => [
                    'user_role' => [
                        'type' => 'select',
                        'label' => 'User Role',
                        'options' => ['any', 'member', 'supplier', 'manufacturer', 'brand'],
                        'default' => 'any'
                    ]
                ]
            ],
            'order_created' => [
                'name' => 'Order Created',
                'description' => 'Triggered when a new order is placed',
                'category' => 'Marketplace Events',
                'icon' => 'fas fa-shopping-cart',
                'config_fields' => [
                    'min_amount' => [
                        'type' => 'number',
                        'label' => 'Minimum Order Amount',
                        'default' => 0
                    ],
                    'product_category' => [
                        'type' => 'select',
                        'label' => 'Product Category',
                        'options' => ['any', 'tools', 'materials', 'software'],
                        'default' => 'any'
                    ]
                ]
            ],
            'thread_created' => [
                'name' => 'Thread Created',
                'description' => 'Triggered when a new forum thread is created',
                'category' => 'Forum Events',
                'icon' => 'fas fa-comments',
                'config_fields' => [
                    'forum_category' => [
                        'type' => 'select',
                        'label' => 'Forum Category',
                        'options' => ['any', 'general', 'technical', 'marketplace'],
                        'default' => 'any'
                    ]
                ]
            ],
            'user_inactive' => [
                'name' => 'User Inactive',
                'description' => 'Triggered when a user has been inactive for a specified period',
                'category' => 'User Events',
                'icon' => 'fas fa-user-clock',
                'config_fields' => [
                    'inactive_days' => [
                        'type' => 'number',
                        'label' => 'Inactive Days',
                        'default' => 30,
                        'min' => 1,
                        'max' => 365
                    ]
                ]
            ],
            'scheduled' => [
                'name' => 'Scheduled',
                'description' => 'Triggered at specified times or intervals',
                'category' => 'Time Events',
                'icon' => 'fas fa-clock',
                'config_fields' => [
                    'schedule_type' => [
                        'type' => 'select',
                        'label' => 'Schedule Type',
                        'options' => ['daily', 'weekly', 'monthly', 'custom'],
                        'default' => 'daily'
                    ],
                    'time' => [
                        'type' => 'time',
                        'label' => 'Time',
                        'default' => '09:00'
                    ]
                ]
            ],
            'api_webhook' => [
                'name' => 'API Webhook',
                'description' => 'Triggered by external API webhook',
                'category' => 'External Events',
                'icon' => 'fas fa-plug',
                'config_fields' => [
                    'webhook_url' => [
                        'type' => 'text',
                        'label' => 'Webhook URL',
                        'readonly' => true
                    ],
                    'secret_key' => [
                        'type' => 'password',
                        'label' => 'Secret Key',
                        'generate' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Available action types
     */
    public function getAvailableActions(): array
    {
        return [
            'send_email' => [
                'name' => 'Send Email',
                'description' => 'Send an email to specified recipients',
                'category' => 'Communication',
                'icon' => 'fas fa-envelope',
                'config_fields' => [
                    'to' => [
                        'type' => 'text',
                        'label' => 'To (Email addresses)',
                        'placeholder' => 'user@example.com, admin@example.com'
                    ],
                    'subject' => [
                        'type' => 'text',
                        'label' => 'Subject',
                        'variables' => true
                    ],
                    'template' => [
                        'type' => 'select',
                        'label' => 'Email Template',
                        'options' => ['welcome', 'order_confirmation', 'reminder', 'custom']
                    ],
                    'content' => [
                        'type' => 'textarea',
                        'label' => 'Custom Content',
                        'variables' => true,
                        'condition' => 'template=custom'
                    ]
                ]
            ],
            'create_notification' => [
                'name' => 'Create Notification',
                'description' => 'Create an in-app notification',
                'category' => 'Communication',
                'icon' => 'fas fa-bell',
                'config_fields' => [
                    'user_id' => [
                        'type' => 'select',
                        'label' => 'Target User',
                        'options' => 'users',
                        'variables' => true
                    ],
                    'title' => [
                        'type' => 'text',
                        'label' => 'Title',
                        'variables' => true
                    ],
                    'message' => [
                        'type' => 'textarea',
                        'label' => 'Message',
                        'variables' => true
                    ],
                    'type' => [
                        'type' => 'select',
                        'label' => 'Notification Type',
                        'options' => ['info', 'success', 'warning', 'error']
                    ]
                ]
            ],
            'update_user_role' => [
                'name' => 'Update User Role',
                'description' => 'Change a user\'s role or permissions',
                'category' => 'User Management',
                'icon' => 'fas fa-user-cog',
                'config_fields' => [
                    'user_id' => [
                        'type' => 'select',
                        'label' => 'Target User',
                        'options' => 'users',
                        'variables' => true
                    ],
                    'new_role' => [
                        'type' => 'select',
                        'label' => 'New Role',
                        'options' => ['guest', 'member', 'senior_member', 'moderator', 'supplier', 'manufacturer', 'brand']
                    ]
                ]
            ],
            'create_task' => [
                'name' => 'Create Task',
                'description' => 'Create a task for admin or user',
                'category' => 'Task Management',
                'icon' => 'fas fa-tasks',
                'config_fields' => [
                    'assigned_to' => [
                        'type' => 'select',
                        'label' => 'Assign To',
                        'options' => 'users',
                        'variables' => true
                    ],
                    'title' => [
                        'type' => 'text',
                        'label' => 'Task Title',
                        'variables' => true
                    ],
                    'description' => [
                        'type' => 'textarea',
                        'label' => 'Task Description',
                        'variables' => true
                    ],
                    'priority' => [
                        'type' => 'select',
                        'label' => 'Priority',
                        'options' => ['low', 'medium', 'high', 'urgent']
                    ],
                    'due_date' => [
                        'type' => 'date',
                        'label' => 'Due Date',
                        'variables' => true
                    ]
                ]
            ],
            'api_request' => [
                'name' => 'API Request',
                'description' => 'Make an HTTP request to external API',
                'category' => 'External Integration',
                'icon' => 'fas fa-exchange-alt',
                'config_fields' => [
                    'url' => [
                        'type' => 'text',
                        'label' => 'API URL',
                        'variables' => true
                    ],
                    'method' => [
                        'type' => 'select',
                        'label' => 'HTTP Method',
                        'options' => ['GET', 'POST', 'PUT', 'DELETE']
                    ],
                    'headers' => [
                        'type' => 'textarea',
                        'label' => 'Headers (JSON)',
                        'placeholder' => '{"Authorization": "Bearer token"}'
                    ],
                    'body' => [
                        'type' => 'textarea',
                        'label' => 'Request Body (JSON)',
                        'variables' => true
                    ]
                ]
            ],
            'delay' => [
                'name' => 'Delay',
                'description' => 'Wait for a specified amount of time',
                'category' => 'Flow Control',
                'icon' => 'fas fa-pause',
                'config_fields' => [
                    'duration' => [
                        'type' => 'number',
                        'label' => 'Duration',
                        'min' => 1
                    ],
                    'unit' => [
                        'type' => 'select',
                        'label' => 'Time Unit',
                        'options' => ['seconds', 'minutes', 'hours', 'days']
                    ]
                ]
            ]
        ];
    }

    /**
     * Available condition operators
     */
    public function getAvailableConditions(): array
    {
        return [
            'equals' => 'Equals',
            'not_equals' => 'Not Equals',
            'greater_than' => 'Greater Than',
            'less_than' => 'Less Than',
            'greater_equal' => 'Greater Than or Equal',
            'less_equal' => 'Less Than or Equal',
            'contains' => 'Contains',
            'not_contains' => 'Does Not Contain',
            'starts_with' => 'Starts With',
            'ends_with' => 'Ends With',
            'in' => 'In List',
            'not_in' => 'Not In List',
            'is_empty' => 'Is Empty',
            'is_not_empty' => 'Is Not Empty',
            'matches_regex' => 'Matches Regex'
        ];
    }

    /**
     * Available variables for use in actions
     */
    public function getAvailableVariables(): array
    {
        return [
            'trigger' => [
                'user_id' => 'Triggered User ID',
                'user_name' => 'Triggered User Name',
                'user_email' => 'Triggered User Email',
                'timestamp' => 'Trigger Timestamp',
                'data' => 'Trigger Data'
            ],
            'system' => [
                'current_date' => 'Current Date',
                'current_time' => 'Current Time',
                'site_name' => 'Site Name',
                'site_url' => 'Site URL',
                'admin_email' => 'Admin Email'
            ]
        ];
    }

    /**
     * Create a new workflow
     */
    public function createWorkflow(array $data): AutomationWorkflow
    {
        return DB::transaction(function () use ($data) {
            $workflow = AutomationWorkflow::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'workflow_data' => $data,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $data['created_by'],
            ]);

            // Create triggers
            foreach ($data['triggers'] as $triggerData) {
                $this->createTrigger($workflow, $triggerData);
            }

            // Create actions
            foreach ($data['actions'] as $index => $actionData) {
                $this->createAction($workflow, $actionData, $index);
            }

            return $workflow->load(['triggers', 'actions']);
        });
    }

    /**
     * Update an existing workflow
     */
    public function updateWorkflow(AutomationWorkflow $workflow, array $data): AutomationWorkflow
    {
        return DB::transaction(function () use ($workflow, $data) {
            $workflow->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'workflow_data' => $data,
                'is_active' => $data['is_active'] ?? true,
                'updated_by' => $data['updated_by'],
            ]);

            // Delete existing triggers and actions
            $workflow->triggers()->delete();
            $workflow->actions()->delete();

            // Create new triggers
            foreach ($data['triggers'] as $triggerData) {
                $this->createTrigger($workflow, $triggerData);
            }

            // Create new actions
            foreach ($data['actions'] as $index => $actionData) {
                $this->createAction($workflow, $actionData, $index);
            }

            return $workflow->load(['triggers', 'actions']);
        });
    }

    /**
     * Delete a workflow
     */
    public function deleteWorkflow(AutomationWorkflow $workflow): bool
    {
        return DB::transaction(function () use ($workflow) {
            $workflow->triggers()->delete();
            $workflow->actions()->delete();
            $workflow->executions()->delete();
            
            return $workflow->delete();
        });
    }

    /**
     * Duplicate a workflow
     */
    public function duplicateWorkflow(AutomationWorkflow $workflow, array $overrides = []): AutomationWorkflow
    {
        $data = array_merge($workflow->workflow_data, $overrides);
        
        return $this->createWorkflow($data);
    }

    /**
     * Export workflow to JSON
     */
    public function exportWorkflow(AutomationWorkflow $workflow): array
    {
        return [
            'name' => $workflow->name,
            'description' => $workflow->description,
            'workflow_data' => $workflow->workflow_data,
            'triggers' => $workflow->triggers->map(function ($trigger) {
                return [
                    'trigger_type' => $trigger->trigger_type,
                    'trigger_config' => $trigger->trigger_config,
                    'conditions' => $trigger->conditions,
                ];
            })->toArray(),
            'actions' => $workflow->actions->map(function ($action) {
                return [
                    'action_type' => $action->action_type,
                    'action_config' => $action->action_config,
                    'execution_order' => $action->execution_order,
                ];
            })->toArray(),
            'exported_at' => now()->toISOString(),
            'version' => '1.0'
        ];
    }

    /**
     * Import workflow from JSON
     */
    public function importWorkflow(array $data): AutomationWorkflow
    {
        return $this->createWorkflow($data);
    }

    /**
     * Get workflow templates
     */
    public function getWorkflowTemplates(): array
    {
        return [
            'welcome_new_user' => [
                'name' => 'Welcome New User',
                'description' => 'Send welcome email and create onboarding tasks for new users',
                'category' => 'User Onboarding',
                'triggers' => [
                    [
                        'trigger_type' => 'user_registered',
                        'trigger_config' => ['user_role' => 'any']
                    ]
                ],
                'actions' => [
                    [
                        'action_type' => 'send_email',
                        'action_config' => [
                            'to' => '{{trigger.user_email}}',
                            'subject' => 'Welcome to MechaMap!',
                            'template' => 'welcome'
                        ]
                    ],
                    [
                        'action_type' => 'create_notification',
                        'action_config' => [
                            'user_id' => '{{trigger.user_id}}',
                            'title' => 'Welcome to MechaMap!',
                            'message' => 'Complete your profile to get started.',
                            'type' => 'info'
                        ]
                    ]
                ]
            ],
            'order_confirmation' => [
                'name' => 'Order Confirmation',
                'description' => 'Send confirmation email and create fulfillment task for new orders',
                'category' => 'E-commerce',
                'triggers' => [
                    [
                        'trigger_type' => 'order_created',
                        'trigger_config' => ['min_amount' => 0]
                    ]
                ],
                'actions' => [
                    [
                        'action_type' => 'send_email',
                        'action_config' => [
                            'to' => '{{trigger.user_email}}',
                            'subject' => 'Order Confirmation #{{trigger.order_id}}',
                            'template' => 'order_confirmation'
                        ]
                    ],
                    [
                        'action_type' => 'create_task',
                        'action_config' => [
                            'assigned_to' => 'admin',
                            'title' => 'Process Order #{{trigger.order_id}}',
                            'description' => 'New order requires processing',
                            'priority' => 'medium'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Create workflow from template
     */
    public function createFromTemplate(string $templateId, string $name, array $parameters, int $createdBy): AutomationWorkflow
    {
        $templates = $this->getWorkflowTemplates();
        
        if (!isset($templates[$templateId])) {
            throw new \InvalidArgumentException("Template not found: {$templateId}");
        }

        $template = $templates[$templateId];
        
        $data = [
            'name' => $name,
            'description' => $template['description'],
            'triggers' => $template['triggers'],
            'actions' => $template['actions'],
            'is_active' => false, // Start inactive
            'created_by' => $createdBy,
        ];

        // Apply parameters to template
        if (!empty($parameters)) {
            $data = $this->applyTemplateParameters($data, $parameters);
        }

        return $this->createWorkflow($data);
    }

    // Private helper methods

    private function createTrigger(AutomationWorkflow $workflow, array $data): AutomationTrigger
    {
        return $workflow->triggers()->create([
            'trigger_type' => $data['trigger_type'],
            'trigger_config' => $data['trigger_config'] ?? [],
            'conditions' => $data['conditions'] ?? [],
            'is_active' => true,
        ]);
    }

    private function createAction(AutomationWorkflow $workflow, array $data, int $order): AutomationAction
    {
        return $workflow->actions()->create([
            'action_type' => $data['action_type'],
            'action_config' => $data['action_config'] ?? [],
            'execution_order' => $order,
            'is_active' => true,
        ]);
    }

    private function applyTemplateParameters(array $data, array $parameters): array
    {
        $json = json_encode($data);
        
        foreach ($parameters as $key => $value) {
            $json = str_replace("{{param.{$key}}}", $value, $json);
        }
        
        return json_decode($json, true);
    }
}
