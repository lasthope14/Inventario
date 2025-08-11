<tr class="element-row-compact">
    <td class="element-image-cell">
        @if($inventario->imagen)
            <img src="{{ asset('storage/' . $inventario->imagen) }}" 
                 alt="{{ $inventario->nombre }}" 
                 class="element-thumbnail">
        @else
            <div class="image-placeholder-small">
                <i class="fas fa-image"></i>
            </div>
        @endif
    </td>
    <td class="element-info-cell">
        <div class="element-name-table">{{ $inventario->nombre }}</div>
        <div class="element-details-table">
            <span class="detail-item">{{ $inventario->marca }} {{ $inventario->modelo }}</span>
            @if($inventario->numero_serie)
                <span class="detail-separator">•</span>
                <span class="detail-item">Serie: {{ $inventario->numero_serie }}</span>
            @endif
        </div>
    </td>
    <td class="element-category-cell">
        {{ $inventario->categoria->nombre ?? 'Sin categoría' }}
    </td>
    <td class="element-locations-cell">
        @php
            $ubicaciones = $inventario->ubicaciones ?? collect();
            $primeraUbicacion = $ubicaciones->first();
        @endphp
        
        @if($primeraUbicacion)
            <div class="location-summary">
                <span class="location-name">{{ $primeraUbicacion->ubicacion->nombre ?? 'Sin ubicación' }}</span>
                <span class="location-quantity">({{ $primeraUbicacion->cantidad }} unidades)</span>
                @if($ubicaciones->count() > 1)
                    <span class="more-locations">+{{ $ubicaciones->count() - 1 }} más</span>
                @endif
            </div>
        @else
            <span class="no-location">Sin ubicación</span>
        @endif
    </td>
    <td class="element-status-cell">
        @if($primeraUbicacion)
            <div class="status-badge-element-new status-{{ str_replace(' ', '-', $primeraUbicacion->estado) }}">
                <i class="status-icon-compact fas fa-circle"></i>
                <span class="status-text-compact">{{ ucfirst($primeraUbicacion->estado) }}</span>
            </div>
        @else
            <span class="no-status">-</span>
        @endif
    </td>
    <td class="element-value-cell">
        @if($inventario->valor_unitario)
            ${{ number_format($inventario->valor_unitario, 0, ',', '.') }}
        @else
            -
        @endif
    </td>
    <td class="element-actions-cell">
        <div class="btn-group btn-group-sm">
            <a href="{{ route('inventarios.show', $inventario->id) }}" 
               class="btn btn-outline-primary btn-xs" 
               title="Ver detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('inventarios.edit', $inventario->id) }}" 
               class="btn btn-outline-secondary btn-xs" 
               title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        </div>
    </td>
</tr>