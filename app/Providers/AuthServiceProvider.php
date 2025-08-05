<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Inventario;
use App\Models\Proveedor;
use App\Models\Ubicacion;
use App\Models\Empleado;
use App\Models\Categoria;
use App\Policies\InventarioPolicy;
use App\Policies\ProveedorPolicy;
use App\Policies\UbicacionPolicy;
use App\Policies\EmpleadoPolicy;
use App\Policies\CategoriaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Inventario::class => InventarioPolicy::class,
        Proveedor::class => ProveedorPolicy::class,
        Ubicacion::class => UbicacionPolicy::class,
        Empleado::class => EmpleadoPolicy::class,
        Categoria::class => CategoriaPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}