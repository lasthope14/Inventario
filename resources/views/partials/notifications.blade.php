@forelse(auth()->user()->unreadNotifications as $notification)
    <div class="notification-item py-3 px-4 hover:bg-gray-50" data-notification-id="{{ $notification->id }}">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="inline-block h-10 w-10 rounded-full 
                    {{ 
                        $notification->data['tipo'] === 'preventivo' ? 'bg-blue-100 text-blue-500' : 
                        ($notification->data['tipo'] === 'correctivo' ? 'bg-yellow-100 text-yellow-500' :
                        ($notification->data['tipo'] === 'movimiento_created' ? 'bg-green-100 text-green-500' :
                        ($notification->data['tipo'] === 'movimiento_updated' ? 'bg-purple-100 text-purple-500' :
                        'bg-red-100 text-red-500')))
                    }} 
                    flex items-center justify-center">
                    <i class="fas 
                        {{ 
                            $notification->data['tipo'] === 'preventivo' ? 'fa-tools' : 
                            ($notification->data['tipo'] === 'correctivo' ? 'fa-exclamation-triangle' :
                            ($notification->data['tipo'] === 'movimiento_created' ? 'fa-exchange-alt' :
                            ($notification->data['tipo'] === 'movimiento_updated' ? 'fa-edit' :
                            'fa-trash-alt')))
                        }}">
                    </i>
                </span>
            </div>
            <div class="ml-3 w-0 flex-1">
                @if(in_array($notification->data['tipo'], ['preventivo', 'correctivo']))
                    <p class="text-sm font-medium text-gray-900">
                        Nuevo Mantenimiento {{ ucfirst($notification->data['tipo']) }}
                    </p>
                    <p class="text-sm text-gray-500">{{ $notification->data['inventario_nombre'] }}</p>
                    <p class="mt-1 text-xs {{ $notification->data['tipo'] === 'preventivo' ? 'text-blue-500' : 'text-yellow-500' }}">
                        Fecha programada: {{ $notification->data['fecha_programada'] }}
                    </p>
                @else
                    <p class="text-sm font-medium text-gray-900">
                        {{ 
                            $notification->data['tipo'] === 'movimiento_created' ? 'Nuevo Movimiento de Inventario' :
                            ($notification->data['tipo'] === 'movimiento_updated' ? 'Movimiento Actualizado' :
                            'Movimiento Eliminado')
                        }}
                    </p>
                    <p class="text-sm text-gray-500">{{ $notification->data['inventario_nombre'] }}</p>
                    <p class="mt-1 text-xs {{ 
                        $notification->data['tipo'] === 'movimiento_created' ? 'text-green-500' :
                        ($notification->data['tipo'] === 'movimiento_updated' ? 'text-purple-500' :
                        'text-red-500')
                    }}">
                        De: {{ $notification->data['ubicacion_origen'] }} a {{ $notification->data['ubicacion_destino'] }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Cantidad: {{ $notification->data['cantidad'] }}
                    </p>
                    @if($notification->data['tipo'] === 'movimiento_updated' && isset($notification->data['cambios']))
                        <div class="mt-1 text-xs text-gray-500">
                            <p class="font-medium">Cambios realizados:</p>
                            @foreach($notification->data['cambios'] as $campo => $valores)
                                <p>{{ ucfirst($campo) }}: {{ $valores['anterior'] }} → {{ $valores['nuevo'] }}</p>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-xs text-gray-500">
                        Fecha: {{ $notification->data['fecha_movimiento'] }}
                    </p>
                @endif
            </div>
        </div>

        @if(!in_array($notification->data['tipo'], ['movimiento_deleted']))
            <a href="{{ 
                in_array($notification->data['tipo'], ['preventivo', 'correctivo']) 
                    ? route('mantenimientos.show', $notification->data['mantenimiento_id'])
                    : route('movimientos.show', $notification->data['movimiento_id']) 
            }}" class="mt-2 block text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Ver detalles
            </a>
        @endif
    </div>
@empty
    <div class="py-3 px-4 text-sm text-gray-700">
        No hay notificaciones nuevas
    </div>
@endforelse