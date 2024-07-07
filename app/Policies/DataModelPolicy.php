<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DataModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_data::model');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DataModel $dataModel): bool
    {
        return $user->can('view_data::model');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_data::model');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataModel $dataModel): bool
    {
        return $user->can('update_data::model');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataModel $dataModel): bool
    {
        return $user->can('delete_data::model');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_data::model');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, DataModel $dataModel): bool
    {
        return $user->can('force_delete_data::model');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_data::model');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, DataModel $dataModel): bool
    {
        return $user->can('restore_data::model');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_data::model');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, DataModel $dataModel): bool
    {
        return $user->can('replicate_data::model');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_data::model');
    }
}
