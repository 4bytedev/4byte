<?php

namespace Packages\Article\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Packages\Article\Models\ArticleComment;

class ArticleCommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_article::comment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ArticleComment $articleComment): bool
    {
        if ($user->can('view_any_article::comment')) {
            return true;
        }

        return $user->can('view_article::comment') && $articleComment->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_article::comment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArticleComment $articleComment): bool
    {
        if ($user->can('update_any_article::comment')) {
            return true;
        }

        return $user->can('update_article::comment') && $articleComment->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArticleComment $articleComment): bool
    {
        if ($user->can('delete_any_article::comment')) {
            return true;
        }

        return $user->can('delete_article::comment') && $articleComment->user_id == $user->id;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_article::comment');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ArticleComment $articleComment): bool
    {
        if ($user->can('force_delete_any_article::comment')) {
            return true;
        }

        return $user->can('force_delete_article::comment') && $articleComment->user_id == $user->id;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_article::comment');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ArticleComment $articleComment): bool
    {
        if ($user->can('restore_any_article::comment')) {
            return true;
        }

        return $user->can('restore_article::comment') && $articleComment->user_id == $user->id;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_article::comment');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ArticleComment $articleComment): bool
    {
        if ($user->can('replicate_any_article::comment')) {
            return true;
        }

        return $user->can('replicate_article::comment') && $articleComment->user_id == $user->id;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_article::comment');
    }
}
