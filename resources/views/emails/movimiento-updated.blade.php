<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #2c3e50; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #ffffff; padding: 20px; text-align: center; border-bottom: 1px solid #f8f9fa;">
            <img src="{{ asset('assets/logo.png') }}" alt="Hidroobras" style="max-width: 250px; height: auto; margin-bottom: 20px;">
            <h1 style="color: #006D95; font-size: 24px; font-weight: 600; margin: 0; padding: 0;">Actualización de Movimiento</h1>
            <p style="color: #004A66; font-size: 16px; margin-top: 10px;">Se ha actualizado un movimiento en el sistema</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px; background-color: #ffffff;">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; border: 1px solid #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; padding: 10px;">
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; color: #95a5a6; margin-bottom: 5px;">Elemento</div>
                            <div style="font-size: 15px; color: #2c3e50; font-weight: 500;">{{ $movimiento->inventario->nombre }}</div>
                        </div>
                    </td>
                    <td style="width: 50%; padding: 10px;">
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; color: #95a5a6; margin-bottom: 5px;">Cantidad</div>
                            <div style="font-size: 15px; color: #2c3e50; font-weight: 500;">{{ $movimiento->cantidad }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 10px;">
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; color: #95a5a6; margin-bottom: 5px;">Origen</div>
                            <div style="font-size: 15px; color: #2c3e50; font-weight: 500;">
                            @php
                                $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                            @endphp
                            {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : $movimiento->ubicacion_origen }}
                        </div>
                        </div>
                    </td>
                    <td style="width: 50%; padding: 10px;">
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; color: #95a5a6; margin-bottom: 5px;">Destino</div>
                            <div style="font-size: 15px; color: #2c3e50; font-weight: 500;">
                            @php
                                $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                            @endphp
                            {{ $ubicacionDestino ? $ubicacionDestino->nombre : $movimiento->ubicacion_destino }}
                        </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 10px;">
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; color: #95a5a6; margin-bottom: 5px;">Cambios realizados</div>
                            @foreach($cambios as $campo => $valores)
                                <div style="font-size: 15px; color: #2c3e50; margin-bottom: 5px;">
                                    <span style="font-weight: 500;">{{ ucfirst($campo) }}:</span>
                                    <span style="color: #e74c3c;">{{ $valores['anterior'] }}</span> →
                                    <span style="color: #27ae60;">{{ $valores['nuevo'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            </table>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/movimientos/' . $movimiento->id) }}" style="display: inline-block; padding: 12px 24px; background-color: #006D95; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 14px;">Ver detalles</a>
            </div>
        </div>

        <!-- Footer -->
        <div style="padding: 20px; text-align: center; color: #95a5a6; font-size: 12px; border-top: 1px solid #f8f9fa;">
            <p>Este es un mensaje automático del sistema de gestión de inventario.</p>
            <p style="margin-bottom: 0;">© {{ date('Y') }} Hidroobras. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>