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
 * @property string $revisionable_type
 * @property int $revisionable_id
 * @property int $revision_number
 * @property string $content_snapshot
 * @property array<array-key, mixed>|null $metadata_snapshot
 * @property string|null $change_summary
 * @property string $change_type
 * @property int $created_by
 * @property string|null $editor_notes
 * @property bool $is_major_revision
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read mixed $change_type_display
 * @property-read Model|\Eloquent $revisionable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision forModel($model)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision majorRevisions()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereChangeSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereContentSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereEditorNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereIsMajorRevision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereMetadataSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereRevisionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereRevisionableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereRevisionableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentRevision whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContentRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'revision_number',
        'content_snapshot',
        'metadata_snapshot',
        'change_summary',
        'change_type',
        'created_by',
        'editor_notes',
        'is_major_revision'
    ];

    protected $casts = [
        'metadata_snapshot' => 'array',
        'is_major_revision' => 'boolean'
    ];

    // Relationships
    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeMajorRevisions($query)
    {
        return $query->where('is_major_revision', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('change_type', $type);
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('revisionable_type', get_class($model))
            ->where('revisionable_id', $model->id);
    }

    // Accessors
    public function getChangeTypeDisplayAttribute()
    {
        return match ($this->change_type) {
            'technical_correction' => 'Sửa lỗi Kỹ thuật',
            'content_update' => 'Cập nhật Nội dung',
            'formatting_fix' => 'Sửa lỗi Định dạng',
            'standard_update' => 'Cập nhật Tiêu chuẩn',
            'formula_revision' => 'Sửa đổi Công thức',
            'procedure_update' => 'Cập nhật Quy trình',
            default => ucfirst(str_replace('_', ' ', $this->change_type))
        };
    }

    // Methods
    public static function createRevision($model, $changeType, $changeSummary, $editorNotes = null, $isMajor = false)
    {
        $lastRevision = static::forModel($model)->orderByDesc('revision_number')->first();
        $nextRevisionNumber = $lastRevision ? $lastRevision->revision_number + 1 : 1;

        return static::create([
            'revisionable_type' => get_class($model),
            'revisionable_id' => $model->id,
            'revision_number' => $nextRevisionNumber,
            'content_snapshot' => $model->content ?? json_encode($model->toArray()),
            'metadata_snapshot' => $model->metadata ?? $model->toArray(),
            'change_summary' => $changeSummary,
            'change_type' => $changeType,
            'created_by' => auth()->id(),
            'editor_notes' => $editorNotes,
            'is_major_revision' => $isMajor
        ]);
    }
}
