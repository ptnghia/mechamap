<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'user_id',
        'file_name',
        'file_path',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the documentation that owns the download.
     */
    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    /**
     * Get the user that made the download.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
