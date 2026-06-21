<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SerenazgoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KilometrajeController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DniController;
use App\Http\Controllers\HikvisionCameraController;

// Redirección raíz al login
Route::get('/', function () {
    return redirect('/login');
});

// Ruta temporal de prueba HikCentral (sin autenticación)
Route::get('/api/test-hikcentral', [HikvisionCameraController::class, 'getDashboardStats']);

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas de recuperación de contraseña (públicas)
Route::post('/password/recovery', [UserController::class, 'recovery'])->name('password.recovery');
Route::post('/password/reset', [UserController::class, 'validateRecovery'])->name('password.reset');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::view('/manual-usuario', 'manual')->name('manual');

    // Gestión de Usuarios y Administración (Solo Admin)
    Route::middleware('admin')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('usuarios.toggle-active');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');

        // Módulo Serenazgo
        Route::get('/serenazgo', [SerenazgoController::class, 'index'])->name('serenazgo.index');
        Route::post('/serenazgo', [SerenazgoController::class, 'store'])->name('serenazgo.store');
        Route::put('/serenazgo/{serenazgo}', [SerenazgoController::class, 'update'])->name('serenazgo.update');
        Route::patch('/serenazgo/{serenazgo}/toggle-status', [SerenazgoController::class, 'toggleStatus'])->name('serenazgo.toggle-status');
        Route::delete('/serenazgo/{serenazgo}', [SerenazgoController::class, 'destroy'])->name('serenazgo.destroy');

        // Módulo Vehículos
        Route::patch('/vehiculos/{vehiculo}/toggle-status', [VehiculoController::class, 'toggleStatus'])->name('vehiculos.toggle-status');
        Route::resource('vehiculos', VehiculoController::class);

        // Módulo Cámaras
        Route::patch('/camaras/{camara}/toggle-status', [App\Http\Controllers\CamaraController::class, 'toggleStatus'])->name('camaras.toggle-status');
        Route::resource('camaras', App\Http\Controllers\CamaraController::class);

        // Módulo Configuración
        Route::get('/configuracion', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::post('/configuracion', [App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');

        // Seguridad: Solo administradores pueden eliminar reportes históricos
        Route::delete('/reportes/{reporte}', [ReporteController::class, 'destroy'])->name('reportes.destroy');

        // Módulo Copias de Seguridad
        Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [App\Http\Controllers\BackupController::class, 'create'])->name('backups.store');
        Route::get('/backups/{name}/download', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{name}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('/backups/restore/{name}', [App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
        Route::post('/backups/restore-upload', [App\Http\Controllers\BackupController::class, 'restoreUpload'])->name('backups.restore.upload');
    });
    
    // IA Corrección
    Route::post('/api/correct-text', [AIController::class, 'correctText'])->name('ai.correct');
    Route::get('/api/ai-status', [AIController::class, 'checkStatus'])->name('ai.status');
    

    // Módulo Reportes
    Route::get('/reportes/buscar', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/nuevo', [ReporteController::class, 'create'])->name('reportes.nuevo');
    Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');
    Route::get('/reportes/{reporte}/pdf', [ReporteController::class, 'generarPDF'])->name('reportes.pdf');
    Route::get('/reportes/{reporte}/excel', [ReporteController::class, 'generarExcel'])->name('reportes.excel');
    Route::post('/reportes/{reporte}/reasignar', [ReporteController::class, 'reasignarResponsable'])->name('reportes.reasignar');
    
    // Asignaciones
    Route::post('/asignaciones/update-codes', [AsignacionController::class, 'updateCodes'])->name('asignaciones.updateCodes');

    // Módulo Kilometrajes
    Route::get('/kilometrajes', [KilometrajeController::class, 'index'])->name('kilometrajes.index');
    Route::post('/kilometrajes', [KilometrajeController::class, 'store'])->name('kilometrajes.store');
    Route::get('/kilometrajes/last', [KilometrajeController::class, 'last'])->name('kilometrajes.last');

    
    // Reporte WhatsApp
    Route::post('/reporte/whatsapp', [ReporteController::class, 'generarReporteWhatsApp'])->name('reporte.whatsapp');
    
    // API Borrador - Sincronización en tiempo real
    Route::get('/api/draft/unidades', [DraftController::class, 'index'])->name('draft.index');
    Route::post('/api/draft/unidades', [DraftController::class, 'store'])->name('draft.store');
    Route::post('/api/draft/kilometraje', [DraftController::class, 'updateKilometraje'])->name('draft.kilometraje');
    Route::delete('/api/draft/clear', [DraftController::class, 'clear'])->name('draft.clear');
    
    // API Borrador Global - Sincronización en tiempo real entre sesiones
    Route::post('/api/reportes/draft', [DraftController::class, 'saveReportDraft'])->name('reportes.draft.save');
    Route::get('/api/reportes/draft', [DraftController::class, 'getReportDrafts'])->name('reportes.draft.get');
    Route::delete('/api/reportes/draft', [DraftController::class, 'clearReportDraft'])->name('reportes.draft.clear');
    Route::post('/api/reportes/draft/monitor/start', [DraftController::class, 'startMonitoringSession'])->name('reportes.draft.monitor.start');
    Route::post('/api/reportes/draft/monitor/stop', [DraftController::class, 'stopMonitoringSession'])->name('reportes.draft.monitor.stop');

    // API Unidades Reportes
    Route::post('/api/reporte/{id}/unidades-reportes', [ReporteController::class, 'saveUnidadesReportes'])->name('reportes.unidades.save');

    // API Búsqueda de Personal
    Route::get('/api/serenazgo/search', [SerenazgoController::class, 'searchJson'])->name('api.serenazgo.search');

    // API Megáfonos (Dinámico)
    Route::get('/api/megafonos', function() {
        return response()->json(\App\Models\Megafono::orderBy('nombre')->get());
    })->name('api.megafonos');
    
    Route::post('/api/consultar-dni', [DniController::class, 'consultar'])->name('api.consultar.dni');
    Route::get('/api/test-dni/{dni}', [DniController::class, 'consultar']);
    
    // Server time API
    Route::get('/api/server-time', function () {
        return response()->json(['timestamp' => now()->timestamp, 'datetime' => now()->toISOString()]);
    })->name('api.server-time');

    // API HikCentral - Estado de Cámaras
    Route::get('/api/hikcentral/status', [HikvisionCameraController::class, 'getStatus'])->name('api.hikcentral.status');

});
