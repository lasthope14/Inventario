<!-- resources/views/admin/users/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <!-- Encabezado de la Tarjeta -->
        <div class="card-header bg-primary text-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h2 class="mb-3 mb-md-0 fs-4"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
                <!-- Puedes agregar más botones o enlaces aquí si es necesario -->
            </div>
        </div>

        <!-- Cuerpo de la Tarjeta -->
        <div class="card-body">
            <!-- Mensajes de Éxito -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <!-- Mensajes de Error -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <!-- Tabla de Usuarios -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Rol</th>
                            <th class="text-center">Notificaciones</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="text-center">{{ $user->name }}</td>
                                <td class="text-center">{{ $user->email }}</td>
                                <td class="text-center">
                                    @if($user->role)
                                        <span class="badge bg-success">{{ $user->role->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Sin rol</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($user->receives_notifications)
                                        <span class="badge bg-success"><i class="fas fa-bell me-1"></i> Activadas</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-bell-slash me-1"></i> Desactivadas</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <!-- Botón para abrir el modal de cambio de rol -->
                                        <button 
                                            class="btn btn-outline-primary btn-sm me-2" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#roleModal-{{ $user->id }}"
                                            data-bs-placement="top"
                                            title="Configurar Usuario"
                                            aria-label="Configurar Usuario"
                                        >
                                            <i class="fas fa-user-cog"></i>
                                        </button>

                                        <!-- Botón para eliminar usuario -->
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')"
                                                data-bs-placement="top"
                                                title="Eliminar Usuario"
                                                aria-label="Eliminar Usuario"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modales de Cambio de Rol Fuera de la Tarjeta -->
@foreach($users as $user)
    <!-- Modal de Cambio de Rol -->
    <div class="modal fade" id="roleModal-{{ $user->id }}" tabindex="-1" aria-labelledby="roleModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalLabel-{{ $user->id }}">Configurar Usuario: {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="role_id_{{ $user->id }}" class="form-label">Seleccionar Rol</label>
                            <select name="role_id" class="form-select" id="role_id_{{ $user->id }}" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="receives_notifications_{{ $user->id }}" 
                                   name="receives_notifications" {{ $user->receives_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="receives_notifications_{{ $user->id }}">
                                Recibir notificaciones por correo
                            </label>
                        </div>
                        
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>
                            Al desactivar las notificaciones por correo, el usuario no recibirá alertas de cambios en el inventario.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cerrar automáticamente las alertas después de 3 segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);

        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
