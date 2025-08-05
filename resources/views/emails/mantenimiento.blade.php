<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #2c3e50; margin: 0; padding: 0; background-color: #f4f6f8;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; margin-top: 20px;">
        <!-- Header -->
        <div style="text-align: center; padding: 30px 20px; border-bottom: 1px solid #eaedef;">
            <img src="{{ asset('assets/logo.png') }}" alt="Hidroobras" style="max-width: 200px; height: auto; margin-bottom: 20px;">
            <h1 style="color: #006D95; font-size: 24px; font-weight: 600; margin: 0 0 10px 0;">Notificación de Mantenimiento</h1>
            <div style="display: inline-block; padding: 6px 16px; border-radius: 50px; font-size: 14px; font-weight: 500; margin-top: 10px;
                {{ $tipo === 'preventivo' ? 'background-color: #e8f5e9; color: #2e7d32;' : 'background-color: #fff3e0; color: #e65100;' }}">
                {{ ucfirst($tipo) }}
            </div>
        </div>

        <!-- Content -->
        <div style="padding: 30px 40px;">
            <p style="font-size: 16px; color: #37474f; margin-bottom: 25px;">
                {{ $tipo === 'correctivo' ? 
                'Se ha registrado un nuevo mantenimiento correctivo que requiere atención inmediata.' : 
                'Se ha programado un nuevo mantenimiento preventivo que requiere ser atendido según la fecha establecida.' }}
            </p>

            <!-- Información del Mantenimiento -->
            <div style="background-color: #f8fafc; border: 1px solid #eaedef; border-radius: 8px; padding: 25px; margin-bottom: 30px;">
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Equipo/Elemento</div>
                    <div style="font-size: 16px; color: #1e293b; font-weight: 500;">{{ $elemento }}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Fecha Programada</div>
                        <div style="font-size: 15px; color: #1e293b; font-weight: 500;">{{ $fecha }}</div>
                    </div>

                    <div>
                        <div style="font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Estado</div>
                        <div style="font-size: 15px; color: #1e293b; font-weight: 500;">Pendiente</div>
                    </div>
                </div>
            </div>

            <!-- Descripción del Mantenimiento -->
            <div style="margin-bottom: 30px;">
                <div style="font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 10px;">Descripción del Mantenimiento</div>
                <div style="font-size: 15px; color: #1e293b; line-height: 1.6; padding: 15px; background-color: #f8fafc; border-radius: 6px;">
                    {{ $descripcion }}
                </div>
            </div>

            <!-- Botón de Acción -->
            <div style="text-align: center; margin-top: 35px;">
                <a href="{{ url('/mantenimientos/' . $mantenimiento->id) }}" 
                   style="display: inline-block; padding: 12px 28px; background-color: #006D95; color: white; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 500; transition: background-color 0.3s ease;">
                    Ver Detalles del Mantenimiento
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="padding: 25px; text-align: center; background-color: #f8fafc; border-top: 1px solid #eaedef; border-radius: 0 0 8px 8px;">
            <p style="color: #64748b; font-size: 13px; margin: 0 0 5px 0;">Este es un mensaje automático del sistema de mantenimiento.</p>
            <p style="color: #64748b; font-size: 13px; margin: 0;">© {{ date('Y') }} Hidroobras - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>