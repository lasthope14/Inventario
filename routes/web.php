<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DocumentAnalysisController;
use App\Http\Controllers\MovimientoMasivoController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Ruta directa para análisis de documentos (para usar en cPanel)
Route::get('/duplicados', [DocumentAnalysisController::class, 'index'])->name('duplicados');

// Rutas de verificación de correo electrónico
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Enlace de verificación enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Rutas protegidas que requieren autenticación y verificación de correo
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    


    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Rutas de importación
    Route::get('/inventarios/importar', [ImportController::class, 'showImportForm'])->name('inventarios.import.form');
    Route::post('/inventarios/analizar', [ImportController::class, 'analyzeFile'])->name('inventarios.analyze');
    Route::post('/inventarios/importar', [ImportController::class, 'import'])->name('inventarios.import');
    Route::get('/inventarios/descargar-plantilla', [ImportController::class, 'downloadTemplate'])
        ->name('inventarios.template.download');

    // Rutas para gestión de logs de importación
    Route::delete('/importlogs/{log}', [ImportController::class, 'destroy'])
        ->name('importlogs.destroy');
    Route::post('/importlogs/{log}/revert', [ImportController::class, 'revert'])
        ->name('importlogs.revert');

    // Rutas específicas de inventarios (DEBEN IR ANTES del resource)
    Route::get('/inventarios/categoria/{categoria?}', [InventarioController::class, 'showCategoria'])->name('inventarios.categoria');
    Route::get('/inventarios/categorias', [InventarioController::class, 'showCategorias'])->name('inventarios.categorias');
    
    // Rutas AJAX y API
    Route::get('/inventarios/elementos-por-categoria/{categoria}', [InventarioController::class, 'getElementosPorCategoria']);
    Route::get('/inventarios/filtros-ajax', [InventarioController::class, 'getFiltrosAjax'])->name('inventarios.filtros-ajax');
    Route::get('/inventarios/filtros-cascada', [InventarioController::class, 'getFiltrosCascada'])->name('inventarios.filtros-cascada');
    Route::get('/inventarios/autocomplete', [InventarioController::class, 'autocomplete'])->name('inventarios.autocomplete');
    Route::get('/inventarios/categoria/{categoria}/autocomplete', [InventarioController::class, 'autocompleteCategoria'])->name('inventarios.categoria.autocomplete');
    Route::get('/api/marcas-por-elemento', [InventarioController::class, 'getMarcasPorElemento'])->name('api.marcas-por-elemento');
    Route::get('/api/proveedores-por-elemento-marca', [InventarioController::class, 'getProveedoresPorElementoMarca'])->name('api.proveedores-por-elemento-marca');
    Route::get('/api/ubicaciones-por-elemento', [InventarioController::class, 'getUbicacionesPorElemento'])->name('api.ubicaciones-por-elemento');
    Route::get('/api/estados-por-elemento', [InventarioController::class, 'getEstadosPorElemento'])->name('api.estados-por-elemento');

// API unificada para filtros (Opción 2 - Más eficiente)
Route::get('/api/filtros-unificados', [InventarioController::class, 'getFiltrosUnificados'])->name('api.filtros-unificados');

// Ruta resource de inventarios
    Route::resource('inventarios', InventarioController::class)->parameters([
        'inventarios' => 'inventario'
    ]);
    
    // Ruta de prueba para contenedores
    Route::get('/inventarios/{inventario}/test-containers', [InventarioController::class, 'testContainers'])->name('inventarios.test-containers');

    // Rutas específicas de movimientos (DEBEN IR ANTES del resource)
    Route::get('/movimientos/export-pdf', [MovimientoController::class, 'exportPdf'])->name('movimientos.export-pdf');
    Route::get('/movimientos/revertibles', [MovimientoMasivoController::class, 'movimientosRevertibles'])->name('movimientos.revertibles');
    Route::get('/movimientos/masivo/inventario-data', [MovimientoMasivoController::class, 'getInventarioData'])->name('movimientos.masivo.inventario-data');
    Route::post('/movimientos/{movimiento}/revertir', [MovimientoMasivoController::class, 'revertir'])->name('movimientos.revertir');

    // Rutas de movimientos masivos
    Route::get('/movimientos-masivos', [MovimientoMasivoController::class, 'index'])->name('movimientos.masivo');
    Route::post('/movimientos-masivos', [MovimientoMasivoController::class, 'store'])->name('movimientos.masivo.store');

    // Ruta resource de movimientos (DEBE IR DESPUÉS de las rutas específicas)
    Route::resource('movimientos', MovimientoController::class)->parameters([
        'movimientos' => 'movimiento'
    ]);

    // Rutas de Mantenimientos
    Route::resource('mantenimientos', MantenimientoController::class)->parameters([
        'mantenimientos' => 'mantenimiento'
    ]);
    Route::patch('/mantenimientos/{mantenimiento}/posponer', [MantenimientoController::class, 'posponerMantenimiento'])
        ->name('mantenimientos.posponer');
    Route::patch('/mantenimientos/{mantenimiento}/marcar-realizado', [MantenimientoController::class, 'marcarRealizado'])
        ->name('mantenimientos.marcar-realizado');

    // Resources con parámetros personalizados
    Route::resource('categorias', CategoriaController::class)->parameters([
        'categorias' => 'categoria'
    ]);
    
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    
    Route::resource('ubicaciones', UbicacionController::class)->parameters([
        'ubicaciones' => 'ubicacion'
    ]);
    
    Route::resource('empleados', EmpleadoController::class)->parameters([
        'empleados' => 'empleado'
    ]);
    
    Route::resource('documentos', DocumentoController::class)->parameters([
        'documentos' => 'documento'
    ]);

    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])
        ->name('documentos.download');

    // Rutas para administradores
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        
        // Rutas de análisis de documentos
        Route::get('/document-analysis', [DocumentAnalysisController::class, 'index'])->name('documents.analysis');
        Route::post('/documents/clean-orphans', [DocumentAnalysisController::class, 'cleanOrphans'])->name('documents.clean-orphans');
        Route::get('/documents/generate-report', [DocumentAnalysisController::class, 'generateReport'])->name('documents.generate-report');
        Route::get('/documents/missing-details/{nombre}', [DocumentAnalysisController::class, 'showMissingDetails'])->name('documents.missing-details');
        
        // Rutas de importación masiva de documentos
        Route::get('/documents/import-form', [DocumentAnalysisController::class, 'showImportForm'])->name('documents.import-form');
        Route::get('/documents/generate-template', [DocumentAnalysisController::class, 'generateTemplate'])->name('documents.generate-template');
        Route::get('/documents/generate-images-template', [DocumentAnalysisController::class, 'generateImagesTemplate'])->name('documents.generate-images-template');
        Route::post('/documents/import', [DocumentAnalysisController::class, 'importDocuments'])->name('documents.import');
        Route::post('/documents/import-images', [DocumentAnalysisController::class, 'importImages'])->name('documents.import-images');
        
        // Ruta de prueba para logging
        Route::get('/documents/test-logging', [DocumentAnalysisController::class, 'testLogging'])->name('documents.test-logging');
    });

    // Rutas para notificaciones
    Route::get('/check-notifications', [NotificationController::class, 'checkNewNotifications']);
    Route::get('/get-notifications', [NotificationController::class, 'getNotifications']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    Route::post('/mark-all-notifications-as-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');
});

// Incluye las rutas de autenticación
require __DIR__.'/auth.php';