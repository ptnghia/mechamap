<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'notification_templates';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'name',
        'description',
        'channels',
        'email_template',
        'database_template',
        'broadcast_template',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'channels' => 'array',
        'email_template' => 'array',
        'database_template' => 'array',
        'broadcast_template' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific channel
     */
    public function scopeForChannel($query, string $channel)
    {
        return $query->whereJsonContains('channels', $channel);
    }

    /**
     * Get template for specific channel
     */
    public function getTemplateForChannel(string $channel): ?array
    {
        switch ($channel) {
            case 'email':
                return $this->email_template;
            case 'database':
                return $this->database_template;
            case 'broadcast':
                return $this->broadcast_template;
            default:
                return null;
        }
    }

    /**
     * Check if template supports channel
     */
    public function supportsChannel(string $channel): bool
    {
        return in_array($channel, $this->channels ?? []);
    }

    /**
     * Render template with variables
     */
    public function render(string $channel, array $variables = []): ?array
    {
        $template = $this->getTemplateForChannel($channel);
        
        if (!$template) {
            return null;
        }

        $rendered = [];
        
        foreach ($template as $key => $value) {
            if ($key === 'variables') {
                continue; // Skip variables definition
            }
            
            if (is_string($value)) {
                $rendered[$key] = $this->replaceVariables($value, $variables);
            } else {
                $rendered[$key] = $value;
            }
        }

        return $rendered;
    }

    /**
     * Replace variables in template string
     */
    private function replaceVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Get available variables for template
     */
    public function getAvailableVariables(string $channel): array
    {
        $template = $this->getTemplateForChannel($channel);
        return $template['variables'] ?? [];
    }

    /**
     * Validate variables against template
     */
    public function validateVariables(string $channel, array $variables): array
    {
        $required = $this->getAvailableVariables($channel);
        $missing = [];
        
        foreach ($required as $variable) {
            if (!isset($variables[$variable])) {
                $missing[] = $variable;
            }
        }
        
        return $missing;
    }
}
