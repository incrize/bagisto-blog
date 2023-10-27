<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_id
 * @property \CSCart\Bagisto\Blog\Models\Post $post
 * @property string $title
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class PostTag extends Model
{
    /**
     * @var string
     */
    protected $table = 'blog_post_tags';

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'post_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
