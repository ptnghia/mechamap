<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string $file_name
 * @property string $file_path
 * @property string $disk
 * @property string $mime_type
 * @property int $file_size
 * @property string $file_extension
 * @property string $file_category
 * @property string|null $cad_metadata
 * @property string|null $cad_software
 * @property string|null $cad_version
 * @property string|null $drawing_scale
 * @property string|null $units
 * @property string|null $dimensions
 * @property string|null $standard_compliance
 * @property string|null $revision_number
 * @property string|null $drawing_date
 * @property string|null $material_specification
 * @property string|null $technical_notes
 * @property string $processing_status
 * @property string|null $conversion_formats
 * @property int $is_public
 * @property int $is_approved
 * @property int $virus_scanned
 * @property string|null $scanned_at
 * @property int $contains_sensitive_data
 * @property int $download_count
 * @property string|null $thumbnail_path
 * @property int|null $width
 * @property int|null $height
 * @property string|null $exif_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $url
 * @property-read Model|\Eloquent $mediable
 * @property-read \App\Models\Thread|null $thread
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCadMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCadSoftware($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCadVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereContainsSensitiveData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereConversionFormats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDownloadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDrawingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDrawingScale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereExifData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFileCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFileExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMaterialSpecification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMediableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMediableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereProcessingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereRevisionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereScannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereStandardCompliance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereTechnicalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereThumbnailPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUnits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereVirusScanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereWidth($value)
 * @mixin \Eloquent
 */
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
        return placeholder_image(800, 600, 'No Image');
    }
}
