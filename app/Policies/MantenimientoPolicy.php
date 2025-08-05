<?php

namespace App\Policies;

use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MantenimientoPolicy
{
    public function viewAny(User $user)
{
    return true;
}

public function view(User $user, Mantenimiento $mantenimiento)
{
    return true;
}

public function create(User $user)
{
    return in_array($user->role->name, ['administrador', 'almacenista']);
}

public function update(User $user, Mantenimiento $mantenimiento)
{
    return in_array($user->role->name, ['administrador', 'almacenista']);
}

public function delete(User $user, Mantenimiento $mantenimiento)
{
    return $user->role->name === 'administrador';
}
}
