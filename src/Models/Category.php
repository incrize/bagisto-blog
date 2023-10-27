<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Models;

use CSCart\Bagisto\Blog\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property \CSCart\Bagisto\Blog\Enums\CategoryStatus $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Category extends Model
{
    /**
     * @var string
     */
    protected $table = 'blog_categories';

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'status', 'slug'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => CategoryStatus::class,
    ];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReadPolicy(Builder $query): Builder
    {
        /** @var \Webkul\User\Models\Admin|null $user */
        $user = auth()->user();

        if (!$user || !$user->hasPermission('blog.blog-category:manage')) {
            $query->where('status', '=', CategoryStatus::ACTIVE);
        }

        return $query;
    }
}
