<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Policies;

use Webkul\User\Models\Admin;
use CSCart\Bagisto\Blog\Enums\CategoryStatus;
use CSCart\Bagisto\Blog\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \Webkul\User\Models\Admin|null $user
     * @param \CSCart\Bagisto\Blog\Models\Category $category
     *
     * @return bool
     */
    public function view(?Admin $user, Category $category)
    {
        if ($category->status === CategoryStatus::ACTIVE) {
            return true;
        }

        return $user && $user->hasPermission('blog.blog-category:manage');
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
        return $user->hasPermission('blog.blog-category:manage');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param \Webkul\User\Models\Admin $user
     * @param \CSCart\Bagisto\Blog\Models\Category $category
     *
     * @return bool
     */
    public function update(Admin $user, Category $category)
    {
        return $user->hasPermission('blog.blog-category:manage');
    }
}
