<?php

namespace Packages\React\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Packages\React\Models\Save;

class SavePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_save');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Save $save): bool
    {
        if ($user->can('view_any_save')) {
            return true;
        }

        return $user->can('view_save') && $save->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_save');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Save $save): bool
    {
        if ($user->can('update_any_save')) {
            return true;
        }

        return $user->can('update_save') && $save->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Save $save): bool
    {
        if ($user->can('delete_any_save')) {
            return true;
        }

        return $user->can('delete_save') && $save->user_id === $user->id;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_save');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Save $save): bool
    {
        if ($user->can('force_delete_any_save')) {
            return true;
        }

        return $user->can('force_delete_save') && $save->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_save');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Save $save): bool
    {
        if ($user->can('restore_any_save')) {
            return true;
        }

        return $user->can('restore_save') && $save->user_id === $user->id;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_save');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Save $save): bool
    {
        if ($user->can('replicate_any_save')) {
            return true;
        }

        return $user->can('replicate_save') && $save->user_id === $user->id;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_save');
    }
}
