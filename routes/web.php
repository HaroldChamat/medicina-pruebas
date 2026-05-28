<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\superadmin\SuperadminLoginController;
use App\Http\Controllers\superadmin\SuperadminDashboardController;
use App\Http\Controllers\superadmin\SuperadminCentroController;
use App\Http\Controllers\superadmin\SuperadminAdminController;
use App\Http\Controllers\superadmin\SuperadminUsuarioController;
use App\Http\Controllers\SolicitudCambioCentroController;
use App\Http\Controllers\SuperadminPacienteController;
use App\Http\Controllers\SuperadminSolicitudController;

// ── Rutas públicas ──────────────────────────────────────────────────────────
Route::get('/', function () {
    if (session()->has('cargo')) {
        return redirect()->route('welcome');
    }
    $especialidades = \App\Models\Especialidad::all();
    $totalEspecialidades = $especialidades->count();
    $totalMedicos = \App\Models\User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->count();
    $totalPacientes = \App\Models\User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))->count();
    return view('inicio', compact('especialidades', 'totalEspecialidades', 'totalMedicos', 'totalPacientes'));
})->name('inicio');

Route::get('/login', [UserController::class, 'index_welcome'])->name('welcome');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/C_usuario', [UserController::class, 'index'])->name('C_usuario');
Route::post('/usuario/store', [UserController::class, 'store'])->name('User.store');

// ── Solo Admin ──────────────────────────────────────────────────────────────
Route::middleware(['cargo:Admin'])->group(function () {
    Route::put('/asignar-especialidad-medico', [EspecialidadController::class, 'actualizarEspecialidad'])->name('especialidad.update');
    Route::post('/especialidad', [EspecialidadController::class, 'store'])->name('especialidad.store');
    Route::put('/especialidad/{id}', [EspecialidadController::class, 'update'])->name('especialidad.updateNombre');
    Route::delete('/especialidad/{id}', [EspecialidadController::class, 'destroy'])->name('especialidad.destroy');
    Route::put('/usuario/{id}', [UserController::class, 'update'])->name('User.update');
    Route::delete('/usuario/{id}', [UserController::class, 'destroy'])->name('User.destroy');
    Route::get('/Especialidad', [UserController::class, 'index_especialidad'])->name('Especialidad');
    Route::get('/citas/{id}/edit', [CitaController::class, 'edit']);
    Route::put('/citas/{id}', [CitaController::class, 'update']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);
    Route::get('/admin/medicos', [UserController::class, 'index_medicos'])->name('admin.medicos');
    Route::get('/admin/pacientes', [UserController::class, 'index_pacientes'])->name('admin.pacientes');

    // Tickets: acciones exclusivas de Admin
    Route::post('/tickets/{ticket}/tomar', [TicketController::class, 'tomar'])->name('tickets.tomar');
    Route::post('/tickets/{ticket}/cerrar', [TicketController::class, 'cerrar'])->name('tickets.cerrar');

    // Activar/desactivar usuarios
    Route::post('/usuario/{id}/desactivar', [UserController::class, 'desactivar'])->name('User.desactivar');
    Route::post('/usuario/{id}/activar',    [UserController::class, 'activar'])->name('User.activar');

    // Historial: Admin puede ver el historial de cualquier paciente
    Route::get('/Historial/{paciente}', [HistorialController::class, 'index'])->name('historial.index');
});

// Centros médicos
Route::get('/admin/centros-medicos', [\App\Http\Controllers\CentroMedicoController::class, 'index'])->name('admin.centros');
Route::post('/admin/centros-medicos', [\App\Http\Controllers\CentroMedicoController::class, 'store'])->name('admin.centros.store');
Route::put('/admin/centros-medicos/{id}', [\App\Http\Controllers\CentroMedicoController::class, 'update'])->name('admin.centros.update');
Route::delete('/admin/centros-medicos/{id}', [\App\Http\Controllers\CentroMedicoController::class, 'destroy'])->name('admin.centros.destroy');

// Prestaciones
Route::get('/admin/prestaciones', [\App\Http\Controllers\PrestacionController::class, 'index'])->name('admin.prestaciones');
Route::post('/admin/prestaciones', [\App\Http\Controllers\PrestacionController::class, 'store'])->name('admin.prestaciones.store');
Route::put('/admin/prestaciones/{id}', [\App\Http\Controllers\PrestacionController::class, 'update'])->name('admin.prestaciones.update');
Route::delete('/admin/prestaciones/{id}', [\App\Http\Controllers\PrestacionController::class, 'destroy'])->name('admin.prestaciones.destroy');
Route::post('/admin/prestaciones/asignar-medico', [\App\Http\Controllers\PrestacionController::class, 'asignarMedico'])->name('admin.prestaciones.asignar');
Route::delete('/admin/prestaciones/medico/{id}', [\App\Http\Controllers\PrestacionController::class, 'eliminarDeMedico'])->name('admin.prestaciones.eliminarDeMedico');

// ── Rutas públicas del superadmin (login) ────────────────────────────────────
Route::prefix('superadmin')->name('superadmin.')->group(function () {
 
    Route::get('/login', [SuperadminLoginController::class, 'showLogin'])
        ->name('login');
 
    Route::post('/login', [SuperadminLoginController::class, 'login'])
        ->name('login.post');
 
    Route::get('/logout', [SuperadminLoginController::class, 'logout'])
        ->name('logout');
 
    // ── Rutas protegidas (requieren sesión superadmin) ──────────────────────
    Route::middleware('superadmin')->group(function () {
 
        // Dashboard
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])
            ->name('dashboard');
 
        // ── Centros Médicos ─────────────────────────────────────────────────
        Route::get('/centros', [SuperadminCentroController::class, 'index'])
            ->name('centros.index');
 
        Route::post('/centros', [SuperadminCentroController::class, 'store'])
            ->name('centros.store');
 
        Route::put('/centros/{id}', [SuperadminCentroController::class, 'update'])
            ->name('centros.update');
 
        // Preview antes de eliminar (paso 1)
        Route::get('/centros/{id}/preview-destroy', [SuperadminCentroController::class, 'previewDestroy'])
            ->name('centros.preview-destroy');
 
        // Eliminación real (paso 2, requiere confirm=CONFIRMAR)
        Route::delete('/centros/{id}', [SuperadminCentroController::class, 'destroy'])
            ->name('centros.destroy');
 
        // Vista detallada de un centro
        Route::get('/centros/{id}', [SuperadminCentroController::class, 'show'])
            ->name('centros.show');
 
        // ── Administradores ─────────────────────────────────────────────────
        Route::get('/admins', [SuperadminAdminController::class, 'index'])
            ->name('admins.index');
 
        Route::post('/admins', [SuperadminAdminController::class, 'store'])
            ->name('admins.store');
 
        Route::put('/admins/{id}', [SuperadminAdminController::class, 'update'])
            ->name('admins.update');
 
        Route::delete('/admins/{id}', [SuperadminAdminController::class, 'destroy'])
            ->name('admins.destroy');
 
        // ── Usuarios (médicos, pacientes, citas) ────────────────────────────
        Route::get('/usuarios/medicos', [SuperadminUsuarioController::class, 'medicos'])
            ->name('medicos.index');

        Route::get('/usuarios/pacientes', [SuperadminUsuarioController::class, 'pacientes'])
            ->name('pacientes.index');
 
        Route::get('/usuarios/citas', [SuperadminUsuarioController::class, 'citas'])
            ->name('usuarios.citas');

        // ── Gestión de solicitudes ──────────────────────────────────────────
        Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
    
            // Listado con filtros
            Route::get('/', [SuperadminSolicitudController::class, 'index'])
                ->name('index');
    
            // Detalle / ticket individual
            Route::get('/{solicitud}', [SuperadminSolicitudController::class, 'show'])
                ->name('show');
    
            // Aceptar o rechazar (AJAX/JSON)
            Route::post('/{solicitud}/gestionar', [SuperadminSolicitudController::class, 'gestionar'])
                ->name('gestionar');
            // Gestión de solicitudes de cambio de centro
            Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
                Route::get('/', [SuperadminSolicitudController::class, 'index'])
                    ->name('index');
                Route::get('/{solicitud}', [SuperadminSolicitudController::class, 'show'])
                    ->name('show');
                Route::post('/{solicitud}/gestionar', [SuperadminSolicitudController::class, 'gestionar'])
                    ->name('gestionar');
            });
            
            // Gestión de pacientes (superadmin)
            Route::prefix('pacientes')->name('pacientes.')->group(function () {
                Route::get('/', [SuperadminPacienteController::class, 'index'])
                    ->name('index');
                Route::put('/{paciente}', [SuperadminPacienteController::class, 'update'])
                    ->name('update');
                Route::post('/{paciente}/cambiar-centro', [SuperadminPacienteController::class, 'cambiarCentro'])
                    ->name('cambiar-centro');
                Route::delete('/{paciente}', [SuperadminPacienteController::class, 'destroy'])
                    ->name('destroy');
            });
            
            // Gestión de médicos (superadmin) - nueva ruta con nombre correcto
            Route::get('/medicos', [SuperadminUsuarioController::class, 'medicos'])
                ->name('medicos.index');
            
        });
    
        // ── Cambio directo de centro desde la vista de pacientes ───────────
        // POST /superadmin/pacientes/{paciente}/cambiar-centro
        Route::post('pacientes/{paciente}/cambiar-centro', [SuperadminSolicitudController::class, 'cambiarCentroDirecto'])
            ->name('pacientes.cambiar-centro');
        });
    });
   
 

// Horas disponibles para prestaciones (accesible para todos los autenticados)
Route::get('/horas-disponibles-prestacion', [\App\Http\Controllers\PrestacionController::class, 'horasDisponibles']);

// ── Solo Médico ──────────────────────────────────────────────────────────────
Route::middleware(['cargo:Medico'])->group(function () {
    // Tickets: solo el médico puede crear
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Historial: el médico puede ver el historial de sus pacientes
    Route::get('/Historial/{paciente}', [HistorialController::class, 'index'])->name('historial.index');
});

// ── Solo Paciente ────────────────────────────────────────────────────────────
// El paciente es quien pide o cancela sus propias horas online
Route::middleware(['cargo:Paciente'])->group(function () {
    Route::post('/citas', [CitaController::class, 'store']);
    Route::post('/citas/{id}/cancelar', [CitaController::class, 'cancelarPaciente'])->name('citas.cancelar');
});

// ── Admin y Médico ───────────────────────────────────────────────────────────
Route::middleware(['cargo:Admin,Medico'])->group(function () {
    Route::get('/Informacion', [InformeController::class, 'index_paciente'])->name('informe.paciente');
    Route::get('/Informe', [InformeController::class, 'index'])->name('informe.index');
    Route::get('/Informe/{cita}/editar', [InformeController::class, 'edit'])->name('informe.edit');
    Route::get('/Informe/{cita}', [InformeController::class, 'create'])->name('informe.create');
    Route::post('/Informe/{cita}', [InformeController::class, 'store'])->name('informe.store');
    Route::put('/Informe/{cita}', [InformeController::class, 'update'])->name('informe.update');
    Route::get('/Horario', [HorarioController::class, 'index'])->name('Horario');
    Route::resource('horario', HorarioController::class)->only(['store', 'update']);

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/mensaje', [TicketController::class, 'mensaje'])->name('tickets.mensaje');
    Route::post('/tickets/{ticket}/archivo', [TicketController::class, 'subirArchivo'])->name('tickets.archivo');
});

// ── Ver informe: Admin, Médico y Paciente ────────────────────────────────────
Route::middleware(['cargo:Admin,Medico,Paciente'])->group(function () {
    Route::get('/Informe/{cita}/ver', [InformeController::class, 'show'])->name('informe.show');
});

// ── Todos los roles autenticados ─────────────────────────────────────────────
Route::middleware(['cargo:Admin,Medico,Paciente'])->group(function () {
    Route::get('/citas', [CitaController::class, 'index'])->name('citas');
    Route::get('/citas/horas-disponibles', [CitaController::class, 'horasDisponibles']);
    Route::get('/informe/pdf/{cita}', [InformeController::class, 'pdf'])->name('informe.pdf');
    Route::post('/informe/email', [InformeController::class, 'enviarPorEmail']);
    Route::get('/solicitudes', [SolicitudCambioCentroController::class, 'index'])
    ->name('solicitudes.index');
 
    Route::get('/solicitudes/crear', [SolicitudCambioCentroController::class, 'create'])
        ->name('solicitudes.create');
    
    Route::post('/solicitudes', [SolicitudCambioCentroController::class, 'store'])
        ->name('solicitudes.store');
    
    Route::get('/solicitudes/{solicitud}', [SolicitudCambioCentroController::class, 'show'])
        ->name('solicitudes.show');
});

// ── Chat: solo Admin y Paciente ──────────────────────────────────────────────
Route::middleware(['cargo:Admin,Paciente'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{cita}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{cita}', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/chat/{cita}/leer', function (\App\Models\Cita $cita) {
        \App\Models\Mensaje::where('cita_id', $cita->id)
            ->where('receptor_id', session('user_id'))
            ->where('leido', false)
            ->update(['leido' => true]);
        return response()->json(['ok' => true]);
    });
});

// ── Notificaciones ───────────────────────────────────────────────────────────
Route::get('/notificaciones', function () {
    $userId = session('user_id');
    if (!$userId) return response()->json([]);
    return \App\Models\Notificacion::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();
})->name('notificaciones.index');

Route::post('/notificaciones/leer', function () {
    $userId = session('user_id');
    if (!$userId) return response()->json(['ok' => false]);
    \App\Models\Notificacion::where('user_id', $userId)->update(['leida' => true]);
    return response()->json(['ok' => true]);
})->name('notificaciones.leer');

// ── Contadores de no leídos ──────────────────────────────────────────────────
Route::get('/contadores', function () {
    $userId = session('user_id');
    if (!$userId) return response()->json(['mensajes' => 0, 'tickets' => 0]);

    $mensajes = \App\Models\Mensaje::where('receptor_id', $userId)
        ->where('leido', false)
        ->count();

    $tickets = \App\Models\TicketMensaje::where('leido', false)
        ->whereHas('ticket', function ($q) use ($userId) {
            $q->where('medico_id', $userId)
              ->orWhere('admin_id', $userId);
        })
        ->where('emisor_id', '!=', $userId)
        ->count();

    return response()->json(['mensajes' => $mensajes, 'tickets' => $tickets]);
})->middleware('cargo:Admin,Medico,Paciente')->name('contadores');