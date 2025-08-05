<?php

namespace App\Policies;

use App\Models\Movimiento;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MovimientoPolicy
{
    public function viewAny(User $user)
{
    return true;
}

public function view(User $user, Movimiento $movimiento)
{
    return true;
}

public function create(User $user)
{
    return in_array($user->role->name, ['administrador', 'almacenista']);
}

public function update(User $user, Movimiento $movimiento)
{
    return in_array($user->role->name, ['administrador', 'almacenista']);
}

public function delete(User $user, Movimiento $movimiento)
{
    return $user->role->name === 'administrador';
}
}
