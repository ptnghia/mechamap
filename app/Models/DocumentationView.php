<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationView extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'user_id',
        'ip_address',
        'user_agent',
        'time_spent',
        'scroll_percentage',
    ];

    protected $casts = [
        'time_spent' => 'integer',
        'scroll_percentage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the documentation that owns the view.
     */
    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    /**
     * Get the user that made the view.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
