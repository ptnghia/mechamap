<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'title',
        'description',
        'mediable_id',
        'mediable_type',
    ];

    /**
     * Get the user that uploaded the media.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent mediable model.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the thread that owns the media.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the URL for the media file.
     */
    public function getUrlAttribute(): string
    {
        if ($this->file_path) {
            // Nếu là URL đầy đủ thì trả về luôn
            if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
                return $this->file_path;
            }

            // Nếu là đường dẫn local thì tạo URL từ storage
            return asset('storage/' . $this->file_path);
        }

        // Fallback về placeholder image
        return 'https://via.placeholder.com/800x600?text=No+Image';
    }
}
