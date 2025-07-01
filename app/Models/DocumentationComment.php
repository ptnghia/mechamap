<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'user_id',
        'parent_id',
        'content',
        'status',
        'is_staff_response',
    ];

    protected $casts = [
        'is_staff_response' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the documentation that owns the comment.
     */
    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    /**
     * Get the user that made the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent()
    {
        return $this->belongsTo(DocumentationComment::class, 'parent_id');
    }

    /**
     * Get the child comments.
     */
    public function replies()
    {
        return $this->hasMany(DocumentationComment::class, 'parent_id');
    }
}
