@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
   <div class="card shadow-sm mb-4">
       <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
           <h2 class="mb-0 fs-4">Detalles del Mantenimiento</h2>
           <a href="{{ route('inventarios.show', $mantenimiento->inventario) }}" class="btn btn-light btn-sm">
               <i class="fas fa-arrow-left me-2"></i> Volver al Inventario
           </a>
       </div>
       <div class="card-body">
           <div class="row">
               <div class="col-lg-6 col-md-6 mb-4">
                   <h5 class="border-bottom pb-2 mb-3">Información General</h5>
                   <dl class="row">
                       <dt class="col-sm-5"><i class="fas fa-clipboard-list me-2"></i> Tipo:</dt>
                       <dd class="col-sm-7">{{ ucfirst($mantenimiento->tipo) }}</dd>
               
                       <dt class="col-sm-5"><i class="fas fa-dollar-sign me-2"></i> Costo:</dt>
                       <dd class="col-sm-7">{{ $mantenimiento->costo ? '$'.number_format($mantenimiento->costo, 2) : 'No especificado' }}</dd>
               
                       <dt class="col-sm-5"><i class="fas fa-user-shield me-2"></i> Autorizado por:</dt>
                       <dd class="col-sm-7">{{ $mantenimiento->autorizado_por ?? 'No especificado' }}</dd>
               
                       <dt class="col-sm-5"><i class="fas fa-user me-2"></i> Solicitado por:</dt>
                       <dd class="col-sm-7">{{ $mantenimiento->solicitadoPor->name ?? 'No especificado' }}</dd>
               
                       <dt class="col-sm-5"><i class="fas fa-calendar me-2"></i> Fecha Programada:</dt>
                       <dd class="col-sm-7">{{ $mantenimiento->fecha_programada ? $mantenimiento->fecha_programada->format('d/m/Y') : 'No especificada' }}</dd>
                       
                       <dt class="col-sm-5"><i class="fas fa-check-circle me-2"></i> Fecha Realizado:</dt>
                       <dd class="col-sm-7">{{ $mantenimiento->fecha_realizado ? $mantenimiento->fecha_realizado->format('d/m/Y') : 'No realizado aún' }}</dd>
                       
                       <dt class="col-sm-5"><i class="fas fa-user me-2"></i> Responsable del Mantenimiento:</dt>
                       <dd class="col-sm-7">{{ $mantenimiento->responsable->nombre ?? 'No asignado' }}</dd>
                       
                       <dt class="col-sm-5"><i class="fas fa-info-circle me-2"></i> Estado:</dt>
                       <dd class="col-sm-7">
                           @if($mantenimiento->fecha_realizado)
                               <span class="badge bg-success">Realizado</span>
                           @else
                               <span class="badge bg-warning">Pendiente</span>
                           @endif
                       </dd>
               
                       <dt class="col-sm-5"><i class="fas fa-sync me-2"></i> Periodicidad:</dt>
                       <dd class="col-sm-7">{{ ucfirst($mantenimiento->periodicidad ?? 'No especificada') }}</dd>
                   </dl>
               </div>
               <div class="col-lg-6 col-md-12 mb-4">
                   <h5 class="border-bottom pb-2 mb-3">Detalles del Mantenimiento</h5>
                   <dl class="row">
                       <dt class="col-sm-4"><i class="fas fa-tasks me-2"></i> Descripción:</dt>
                       <dd class="col-sm-8">{{ $mantenimiento->descripcion }}</dd>

                       <dt class="col-sm-4"><i class="fas fa-clipboard-check me-2"></i> Resultado:</dt>
                       <dd class="col-sm-8">{{ $mantenimiento->resultado ?? 'No disponible' }}</dd>
                   </dl>
                   <div class="d-flex justify-content-center mt-4">
                       <lottie-player 
                           src="https://lottie.host/633a2696-72c9-4c7d-bf83-23824456b7cb/Kz24arNfQf.json"
                           background="transparent"
                           speed="1"
                           style="width: 250px; height: 250px;"
                           loop
                           autoplay>
                       </lottie-player>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <div class="row">
       <div class="col-lg-4 col-md-6 mb-4">
           <div class="card shadow-sm h-100">
               <div class="card-header bg-info text-white">
                   <h5 class="mb-0"><i class="fas fa-history me-2"></i> Historial de Mantenimientos</h5>
               </div>
               <div class="card-body">
                   <ul class="list-group">
                       @forelse($mantenimientosAnteriores as $mantenimientoAnterior)
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                               {{ $mantenimientoAnterior->fecha_programada->format('d/m/Y') }} - {{ ucfirst($mantenimientoAnterior->tipo) }}
                               @if($mantenimientoAnterior->fecha_realizado)
                                   <span class="badge bg-success rounded-pill">Realizado</span>
                               @else
                                   <span class="badge bg-warning rounded-pill">Pendiente</span>
                               @endif
                           </li>
                       @empty
                           <li class="list-group-item">No hay mantenimientos anteriores registrados.</li>
                       @endforelse
                   </ul>
               </div>
           </div>
       </div>
       <div class="col-lg-4 col-md-6 mb-4">
           <div class="card shadow-sm h-100">
               <div class="card-header bg-success text-white">
                   <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Estado de Mantenimientos</h5>
               </div>
               <div class="card-body">
                   <canvas id="mantenimientoChart"></canvas>
               </div>
           </div>
       </div>
       <div class="col-lg-4 col-md-12 mb-4">
           <div class="card shadow-sm h-100">
               <div class="card-header bg-primary text-white">
                   <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Resumen de Mantenimientos</h5>
               </div>
               <div class="card-body">
                   <div class="d-flex align-items-center mb-3">
                       <div class="flex-shrink-0">
                           <i class="fas fa-tools fa-2x text-primary"></i>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-0">Total de Mantenimientos</h6>
                           <p class="mb-0 fs-4">{{ $totalMantenimientos }}</p>
                       </div>
                   </div>
                   <div class="d-flex align-items-center mb-3">
                       <div class="flex-shrink-0">
                           <i class="fas fa-calendar-check fa-2x text-success"></i>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-0">Último Mantenimiento Realizado</h6>
                           <p class="mb-0">{{ $ultimoMantenimiento ? $ultimoMantenimiento->fecha_realizado->format('d/m/Y') : 'N/A' }}</p>
                       </div>
                   </div>
                   <div class="d-flex align-items-center">
                       <div class="flex-shrink-0">
                           <i class="fas fa-calendar-alt fa-2x text-info"></i>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-0">Próximo Mantenimiento</h6>
                           <p class="mb-0">{{ $proximoMantenimiento ? $proximoMantenimiento->format('d/m/Y') : 'No programado' }}</p>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   @if(auth()->user()->role->name === 'administrador')
   <div class="text-end mt-3">
       <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="btn btn-primary mb-2">
           <i class="fas fa-edit me-2"></i>Editar
       </a>
       <form action="{{ route('mantenimientos.destroy', $mantenimiento) }}" method="POST" class="d-inline">
           @csrf
           @method('DELETE')
           <button type="submit" class="btn btn-danger mb-2" onclick="return confirm('¿Estás seguro de que quieres eliminar este mantenimiento?')">
               <i class="fas fa-trash me-2"></i>Eliminar
           </button>
       </form>
   </div>
   @endif
   <div class="text-end mt-3">
       <a href="{{ route('mantenimientos.index', ['inventario_id' => $mantenimiento->inventario_id]) }}" class="btn btn-secondary mb-2">
           <i class="fas fa-list me-2"></i>Volver a la lista
       </a>
   </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
   .card {
       transition: all 0.3s ease;
   }
   .card:hover {
       box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
   }
   dt {
       font-weight: bold;
   }
   dd {
       margin-bottom: 0.5rem;
   }
   .card-header h5 i, dt i, .btn i, .nav-link i {
       width: 20px;
       text-align: center;
       margin-right: 0.75rem !important;
   }

   lottie-player {
       max-width: 100%;
       height: auto;
       margin: 0 auto;
   }

   @media (max-width: 992px) {
       lottie-player {
           width: 200px !important;
           height: 200px !important;
       }
   }

   @media (max-width: 768px) {
       .text-end {
           text-align: center !important;
       }
       .btn {
           width: 100%;
           margin-bottom: 0.5rem;
       }
       lottie-player {
           width: 150px !important;
           height: 150px !important;
       }
   }

   .card-body {
       overflow: hidden;
   }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@2.0.8/dist/lottie-player.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   var ctx = document.getElementById('mantenimientoChart').getContext('2d');
   var mantenimientoChart = new Chart(ctx, {
       type: 'doughnut',
       data: {
           labels: ['Realizados', 'Pendientes'],
           datasets: [{
               data: [{{ $mantenimientosRealizados }}, {{ $mantenimientosPendientes }}],
               backgroundColor: ['#28a745', '#ffc107'],
               borderColor: ['#ffffff', '#ffffff'],
               borderWidth: 2
           }]
       },
       options: {
           responsive: true,
           maintainAspectRatio: false,
           plugins: {
               legend: {
                   position: 'bottom',
               }
           },
           cutout: '70%',
           tooltips: {
               callbacks: {
                   label: function(tooltipItem, data) {
                       var dataset = data.datasets[tooltipItem.datasetIndex];
                       var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                           return previousValue + currentValue;
                       });
                       var currentValue = dataset.data[tooltipItem.index];
                       var percentage = Math.floor(((currentValue/total) * 100)+0.5);
                       return currentValue + " (" + percentage + "%)";
                   }
               }
           }
       }
   });
</script>
@endpush