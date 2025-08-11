<div class="element-card-compact">
    <div class="element-image-compact">
        @if($inventario->imagen)
            <img src="{{ asset('storage/' . $inventario->imagen) }}" 
                 alt="{{ $inventario->nombre }}" 
                 class="lazy-load" 
                 data-src="{{ asset('storage/' . $inventario->imagen) }}">
        @else
            <div class="image-placeholder">
                <i class="fas fa-image"></i>
            </div>
        @endif
    </div>
    <div class="element-content-compact">
        <div class="element-header-compact">
            <h6 class="element-name-compact">{{ $inventario->nombre }}</h6>
        </div>
        <div class="element-details-compact">
            <div class="detail-row-compact">
                <span class="detail-label-compact">Marca/Modelo:</span>
                <span class="detail-value-compact">{{ $inventario->marca }} {{ $inventario->modelo }}</span>
            </div>
            <div class="detail-row-compact">
                <span class="detail-label-compact">Serie:</span>
                <span class="detail-value-compact">{{ $inventario->numero_serie }}</span>
            </div>
            <div class="detail-row-compact">
                <span class="detail-label-compact">Propietario:</span>
                <span class="detail-value-compact">{{ $inventario->propietario ?? 'No asignado' }}</span>
            </div>
        </div>
        <div class="element-locations-compact">
            @php
                $ubicaciones = $inventario->ubicaciones ?? collect();
                $ubicacionesLimitadas = $ubicaciones->take(2);
                $ubicacionesRestantes = $ubicaciones->count() - 2;
            @endphp
            
            @foreach($ubicacionesLimitadas as $ubicacionInventario)
                <div class="location-item-compact">
                    <div class="location-info-compact">
                        <span class="location-name-compact">{{ $ubicacionInventario->ubicacion->nombre ?? 'Sin ubicación' }}</span>
                        <span class="location-quantity-compact">{{ $ubicacionInventario->cantidad }} unidades</span>
                    </div>
                    <div class="status-badge-element-new status-{{ str_replace(' ', '-', $ubicacionInventario->estado) }}">
                        <i class="status-icon-compact fas fa-circle"></i>
                        <span class="status-text-compact">{{ ucfirst($ubicacionInventario->estado) }}</span>
                    </div>
                </div>
            @endforeach
            
            @if($ubicacionesRestantes > 0)
                <div class="more-locations-compact">
                    <span class="more-locations-text">+{{ $ubicacionesRestantes }} ubicaciones más</span>
                </div>
            @endif
        </div>
        <div class="element-actions-compact">
            <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn btn-outline-primary btn-xs">
                <i class="fas fa-eye"></i> Ver
            </a>
            <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-outline-secondary btn-xs">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
</div>