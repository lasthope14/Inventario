@extends('layouts.app')

@section('content')
<style>
    /* Estilos Base */
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Header del Perfil */
    .profile-header {
        background: #fff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #0056b3;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    /* Tarjetas de Perfil */
    .profile-card {
        background: #fff;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .profile-card h3 {
        color: #2d3748;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Formularios */
    .form-control {
        border-radius: 8px;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.2rem rgba(0,86,179,0.25);
    }

    /* Botones */
    .btn-custom {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #0056b3;
        border: none;
    }

    .btn-primary:hover {
        background-color: #004494;
        transform: translateY(-1px);
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
        transform: translateY(-1px);
    }

    /* Badges y Zonas Especiales */
    .status-badge {
        background-color: #e2e8f0;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        color: #4a5568;
    }

    .danger-zone {
        border: 1px solid #dc3545;
        border-radius: 15px;
        padding: 1.5rem;
        background-color: #fff5f5;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 15px;
        border: none;
    }

    .modal-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
    }

    /* Responsive Breakpoints */
    @media (max-width: 991px) {
        .profile-container {
            padding: 0 0.5rem;
        }

        .profile-header {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .avatar-circle {
            width: 70px;
            height: 70px;
            font-size: 1.8rem;
        }
    }

    @media (max-width: 768px) {
        .profile-card {
            padding: 1rem;
        }

        .profile-header .col-md-6 {
            width: 100%;
            text-align: center;
        }

        .profile-header .col-md-6.d-flex {
            flex-direction: column;
            align-items: center;
        }

        .avatar-circle {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .ms-4 {
            margin-left: 0 !important;
            text-align: center;
        }

        .status-badge {
            margin-top: 1rem;
            display: block;
            text-align: center;
        }

        .form-label {
            font-size: 0.9rem;
        }

        small.text-muted {
            font-size: 0.8rem;
        }

        /* Modal en móviles */
        .modal-dialog {
            margin: 0.5rem;
        }

        .modal-content {
            padding: 0.5rem;
        }

        .modal-footer {
            flex-direction: column;
        }

        .modal-footer button {
            width: 100%;
            margin: 0.25rem 0 !important;
        }
    }

    /* Transiciones y Animaciones */
    .profile-card, 
    .btn-custom, 
    .form-control, 
    .avatar-circle {
        transition: all 0.3s ease;
    }

    /* Dark Theme Styles */
    [data-bs-theme="dark"] .profile-header {
        background-color: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .profile-card {
        background-color: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .profile-card h3 {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .form-control {
        background-color: #334155;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .form-control:focus {
        background-color: #334155;
        border-color: #0056b3;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .form-control[readonly] {
        background-color: #475569;
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .text-muted {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .status-badge {
        background-color: #334155;
        color: #e2e8f0;
    }

    [data-bs-theme="dark"] .danger-zone {
        background-color: #1e293b;
        border-color: #dc3545;
    }

    [data-bs-theme="dark"] .modal-content {
        background-color: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .modal-header {
        border-bottom-color: #475569;
    }

    [data-bs-theme="dark"] .modal-footer {
        border-top-color: #475569;
    }
</style>

<div class="profile-container">
    <!-- Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <div class="avatar-circle">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="ms-4">
                    <h1 class="h3 mb-1">{{ $user->name }}</h1>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="status-badge">
                    <i class="fas fa-clock me-2"></i>Miembro desde {{ $user->created_at->format('M Y') }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Perfil -->
        <div class="col-md-6 mb-4">
            <div class="profile-card">
                <h3><i class="fas fa-user"></i>Información del Perfil</h3>
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        <small class="text-muted">El correo electrónico no se puede cambiar</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-custom">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </form>
            </div>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="col-md-6 mb-4">
            <div class="profile-card">
                <h3><i class="fas fa-lock"></i>Cambiar Contraseña</h3>
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    
                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-custom">
                        <i class="fas fa-key me-2"></i>Actualizar Contraseña
                    </button>
                </form>
            </div>
        </div>

        <!-- Eliminar Cuenta -->
        <div class="col-12">
            <div class="profile-card">
                <div class="danger-zone">
                    <h3 class="text-danger"><i class="fas fa-exclamation-triangle"></i>Eliminar Cuenta</h3>
                    <p class="text-muted mb-4">Una vez que elimines tu cuenta, todos tus datos serán eliminados permanentemente.</p>
                    <button class="btn btn-danger btn-custom" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar mi cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Cuenta -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirmar Eliminación de Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <p>Por favor, ingresa tu contraseña para confirmar:</p>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (session('status'))
<script>
    // Mostrar notificación de éxito
    document.addEventListener('DOMContentLoaded', function() {
        // Aquí puedes agregar lógica para mostrar notificaciones
        console.log('Perfil actualizado exitosamente');
    });
</script>
@endif
@endsection