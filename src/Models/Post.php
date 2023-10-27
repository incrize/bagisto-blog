<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Models;

use CSCart\Bagisto\Blog\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;
use Webkul\User\Models\Admin;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $published_at
 * @property \Webkul\User\Models\Admin $author
 * @property \Illuminate\Database\Eloquent\Collection<array-key, \CSCart\Bagisto\Blog\Models\Category> $categories
 */
class Post extends Model
{
    /**
     * @var string
     */
    protected $table = 'blog_posts';

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'content', 'published_at'];

    /**
     * @var array
     */
    protected $casts = ['published_at' => 'datetime'];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function (Post $post): void {
            if ($post->author_id === null) {
                $user = auth()->user();
                if ($user === null) {
                    throw new RuntimeException();
                }
                assert($user instanceof Admin);

                $post->author_id = $user->id;
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_post_categories')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReadPolicy(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user !== null) {
            return $query->where(function (Builder $q) use ($user) {
                return $q->whereNotNull('published_at')->orWhere('author_id', $user->getKey());
            });
        }

        $query->whereHas('categories', function (Builder $q) {
            $q->where('status', '=', CategoryStatus::ACTIVE);
        });

        return $query->whereNotNull('published_at');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<int>                            $categoryIds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCategory(Builder $query, array $categoryIds): Builder
    {
        return $query->whereHas('categories', function (Builder $query) use ($categoryIds) {
            $query->whereIn('blog_categories.id', $categoryIds);
        });
    }
}
