<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isUserAdministrator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $this->isAdminButNotHimself($user, $model);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateOwnData(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->isAdminButNotHimself($user, $model);
    }

    public function reset(User $user, User $model): bool
    {
        return $this->isAdminButNotHimself($user, $model);
    }

    public function index(User $user): bool
    {
        return $user->isUserAdministrator();
    }

    public function edit(User $user, User $model): bool
    {
        return $this->isAdminButNotHimself($user, $model);
    }

    protected function isAdminButNotHimself(User $user, User $model): bool
    {
        return $user->isUserAdministrator() && $user->id !== $model->id;
    }

    public function changeActiveStatus(User $user, User $model): bool
    {
        if ($user->is($model)) { //if user is trying to deactivate himself
            return false;
        }

        return $user->isUserAdministrator() && !$model->isUserAdministrator();
    }
}
