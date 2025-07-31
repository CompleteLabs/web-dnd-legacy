<?php

namespace App\Policies;

use App\Models\Cutpoint;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CutpointPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // ADMIN bisa lihat semua, non-ADMIN hanya bisa lihat data sendiri (filter di query/resource)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cutpoint $cutpoint): bool
    {
        // ADMIN bisa lihat semua, non-ADMIN hanya bisa lihat data miliknya
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['ADMIN']) || $user->id === 3;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cutpoint $cutpoint): bool
    {
        return in_array($user->role?->name, ['ADMIN']) || $user->id === 3;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cutpoint $cutpoint): bool
    {
        return in_array($user->role?->name, ['ADMIN']) || $user->id === 3;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cutpoint $cutpoint): bool
    {
        return in_array($user->role?->name, ['ADMIN']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cutpoint $cutpoint): bool
    {
        return in_array($user->role?->name, ['ADMIN']);
    }
}
