<?php

namespace Packages\Category\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Packages\Category\Models\CategoryFollow;

class CategoryFollowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_category::follow');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CategoryFollow $categoryFollow): bool
    {
        if ($user->can('view_any_category::follow')) {
            return true;
        }

        return $user->can('view_category::follow') && $categoryFollow->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_category::follow');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CategoryFollow $categoryFollow): bool
    {
        if ($user->can('update_any_category::follow')) {
            return true;
        }

        return $user->can('update_category::follow') && $categoryFollow->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CategoryFollow $categoryFollow): bool
    {
        if ($user->can('delete_any_category::follow')) {
            return true;
        }

        return $user->can('delete_category::follow') && $categoryFollow->user_id == $user->id;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_category::follow');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CategoryFollow $categoryFollow): bool
    {
        if ($user->can('force_delete_any_category::follow')) {
            return true;
        }

        return $user->can('force_delete_category::follow') && $categoryFollow->user_id == $user->id;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_category::follow');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CategoryFollow $categoryFollow): bool
    {
        if ($user->can('restore_any_category::follow')) {
            return true;
        }

        return $user->can('restore_category::follow') && $categoryFollow->user_id == $user->id;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_category::follow');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CategoryFollow $categoryFollow): bool
    {
        if ($user->can('replicate_any_category::follow')) {
            return true;
        }

        return $user->can('replicate_category::follow') && $categoryFollow->user_id == $user->id;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_category::follow');
    }
}
