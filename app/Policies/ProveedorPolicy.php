<?php

namespace App\Policies;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProveedorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function view(User $user, Proveedor $proveedor)
    {
        return $user->role->name === 'administrador';
    }

    public function create(User $user)
    {
        return $user->role->name === 'administrador';
    }

    public function update(User $user, Proveedor $proveedor)
    {
        return $user->role->name === 'administrador';
    }

    public function delete(User $user, Proveedor $proveedor)
    {
        return $user->role->name === 'administrador';
    }

    public function restore(User $user, Proveedor $proveedor)
    {
        return $user->role->name === 'administrador';
    }

    public function forceDelete(User $user, Proveedor $proveedor)
    {
        return $user->role->name === 'administrador';
    }
}