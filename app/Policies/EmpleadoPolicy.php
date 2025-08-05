<?php

namespace App\Policies;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpleadoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function view(User $user, Empleado $empleado)
    {
        return $user->role->name === 'administrador';
    }

    public function create(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function update(User $user, Empleado $empleado)
    {
        return $user->role->name === 'administrador';
    }

    public function delete(User $user, Empleado $empleado)
    {
        return $user->role->name === 'administrador';
    }

    public function restore(User $user, Empleado $empleado)
    {
        return $user->role->name === 'administrador';
    }

    public function forceDelete(User $user, Empleado $empleado)
    {
        return $user->role->name === 'administrador';
    }
}