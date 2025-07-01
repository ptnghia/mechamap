<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the documentation that owns the rating.
     */
    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    /**
     * Get the user that made the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
