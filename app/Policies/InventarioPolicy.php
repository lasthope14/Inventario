<?php

namespace App\Policies;

use App\Models\Inventario;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InventarioPolicy
{
    public function viewAny(User $user)
{
    return true; // Todos pueden ver la lista
}

public function view(User $user, Inventario $inventario)
{
    return true; // Todos pueden ver detalles
}

public function create(User $user)
{
    return $user->role->name === 'administrador';
}

public function update(User $user, Inventario $inventario)
{
    return $user->role->name === 'administrador';
}

public function delete(User $user, Inventario $inventario)
{
    return $user->role->name === 'administrador';
}
}
