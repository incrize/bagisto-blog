<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Policies;

use Webkul\User\Models\Admin;
use CSCart\Bagisto\Blog\Models\PostTag;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostTagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \Webkul\User\Models\Admin|null $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?Admin $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \Webkul\User\Models\Admin|null $user
     * @param \CSCart\Bagisto\Blog\Models\PostTag $tag
     *
     * @return bool
     */
    public function view(?Admin $user, PostTag $tag)
    {
        return ($user->is($tag->post->author) && $user->hasPermission('blog-post:update')) || $user->hasPermission('blog-post:manage');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \Webkul\User\Models\Admin $user
     *
     * @return bool
     */
    public function create(Admin $user)
    {
        return $user->hasPermission('blog-post:create') || $user->hasPermission('blog-post:manage');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param \Webkul\User\Models\Admin $user
     * @param \CSCart\Bagisto\Blog\Models\PostTag $tag
     *
     * @return bool
     */
    public function update(Admin $user, PostTag $tag)
    {
        return ($user->is($tag->post->author) && $user->hasPermission('blog-post:update')) || $user->hasPermission('blog-post:manage');
    }
}
