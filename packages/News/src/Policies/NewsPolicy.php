<?php

namespace Packages\News\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Packages\News\Models\News;

class NewsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_news');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, News $news): bool
    {
        if ($user->can('view_any_news')) {
            return true;
        }

        return $user->can('view_news') && $news->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_news');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, News $news): bool
    {
        if ($user->can('update_any_news')) {
            return true;
        }

        return $user->can('update_news') && $news->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, News $news): bool
    {
        if ($user->can('delete_any_news')) {
            return true;
        }

        return $user->can('delete_news') && $news->user_id === $user->id;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_news');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, News $news): bool
    {
        if ($user->can('force_delete_any_news')) {
            return true;
        }

        return $user->can('force_delete_news') && $news->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_news');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, News $news): bool
    {
        if ($user->can('restore_any_news')) {
            return true;
        }

        return $user->can('restore_news') && $news->user_id === $user->id;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_news');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, News $news): bool
    {
        if ($user->can('replicate_any_news')) {
            return true;
        }

        return $user->can('replicate_news') && $news->user_id === $user->id;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_news');
    }
}
