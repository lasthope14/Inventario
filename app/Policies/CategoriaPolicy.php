<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function view(User $user, Categoria $categoria)
    {
        return $user->role->name === 'administrador';
    }

    public function create(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function update(User $user, Categoria $categoria)
    {
        return $user->role->name === 'administrador';
    }

    public function delete(User $user, Categoria $categoria)
    {
        return $user->role->name === 'administrador';
    }

    public function restore(User $user, Categoria $categoria)
    {
        return $user->role->name === 'administrador';
    }

    public function forceDelete(User $user, Categoria $categoria)
    {
        return $user->role->name === 'administrador';
    }
}