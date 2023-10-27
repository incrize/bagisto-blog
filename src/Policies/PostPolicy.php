<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Policies;

use Webkul\User\Models\Admin;
use CSCart\Bagisto\Blog\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param \Webkul\User\Models\Admin $user
     *
     * @return bool
     */
    public function create(Admin $user)
    {
        return $user->hasPermission('blog.blog-post:create') || $user->hasPermission('blog.blog-post:manage');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param \Webkul\User\Models\Admin $user
     * @param \CSCart\Bagisto\Blog\Models\Post $post
     *
     * @return bool
     */
    public function update(Admin $user, Post $post)
    {
        return ($user->is($post->author) && $user->hasPermission('blog.blog-post:update')) || $user->hasPermission('blog.blog-post:manage');
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \Webkul\User\Models\Admin|null $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?Admin $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \Webkul\User\Models\Admin|null          $user
     * @param \CSCart\Bagisto\Blog\Models\Post $post
     *
     * @return bool
     */
    public function view(?Admin $user, Post $post)
    {
        if ($post->published_at) {
            return true;
        }

        return $user && $user->is($post->author);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \Webkul\User\Models\Admin $user
     * @param \CSCart\Bagisto\Blog\Models\Post $post
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Post $post)
    {
        return $this->update($user, $post);
    }
}
