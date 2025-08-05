<?php

namespace App\Policies;

use App\Models\Ubicacion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UbicacionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function view(User $user, Ubicacion $ubicacion)
    {
        return $user->role->name === 'administrador';
    }

    public function create(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function update(User $user, Ubicacion $ubicacion)
    {
        return $user->role->name === 'administrador';
    }

    public function delete(User $user, Ubicacion $ubicacion)
    {
        return $user->role->name === 'administrador';
    }

    public function restore(User $user, Ubicacion $ubicacion)
    {
        return $user->role->name === 'administrador';
    }

    public function forceDelete(User $user, Ubicacion $ubicacion)
    {
        return $user->role->name === 'administrador';
    }
}