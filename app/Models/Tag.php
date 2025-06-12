<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $color_code Hex color code cho tag display (#FF5722 cho manufacturing)
 * @property string $tag_type Loại tag: chung, phần mềm, vật liệu, quy trình, ngành công nghiệp
 * @property string $expertise_level Cấp độ chuyên môn yêu cầu cho tag này
 * @property int $usage_count Số lần tag được sử dụng (cached count)
 * @property \Illuminate\Support\Carbon|null $last_used_at Lần cuối tag được sử dụng
 * @property bool $is_featured Tag được highlight trong suggestions không
 * @property bool $is_active Tag có đang được sử dụng không
 * @property int $sort_order Thứ tự sắp xếp khi hiển thị tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thread> $threads
 * @property-read int|null $threads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereExpertiseLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTagType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUsageCount($value)
 * @mixin \Eloquent
 */
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
        'color_code',
        'tag_type',
        'expertise_level',
        'usage_count',
        'last_used_at',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_used_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'sort_order' => 'integer',
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
