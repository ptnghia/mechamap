<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the threads that are tagged with this tag.
     */
    public function threads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class, 'thread_tag')
            ->withTimestamps();
    }
}
