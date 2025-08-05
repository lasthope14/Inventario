<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function updateRole(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function delete(User $user)
    {
        return $user->role->name === 'administrador';
    }
}