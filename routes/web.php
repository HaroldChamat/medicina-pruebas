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
    Route::put('/usuario/{id}', [UserController::class, 'update'])->name('User.update');
    Route::delete('/usuario/{id}', [UserController::class, 'destroy'])->name('User.destroy');
    Route::get('/Especialidad', [UserController::class, 'index_especialidad'])->name('Especialidad');
    Route::post('/citas', [CitaController::class, 'store']);
    Route::get('/citas/{id}/edit', [CitaController::class, 'edit']);
    Route::put('/citas/{id}', [CitaController::class, 'update']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);
    Route::get('/admin/medicos', [UserController::class, 'index_medicos'])->name('admin.medicos');
    Route::get('/admin/pacientes', [UserController::class, 'index_pacientes'])->name('admin.pacientes');

    // Tickets: acciones exclusivas de Admin
    Route::post('/tickets/{ticket}/tomar', [TicketController::class, 'tomar'])->name('tickets.tomar');
    Route::post('/tickets/{ticket}/cerrar', [TicketController::class, 'cerrar'])->name('tickets.cerrar');
    
    //Desactivar o activar usuarios
    Route::post('/usuario/{id}/desactivar', [UserController::class, 'desactivar'])->name('User.desactivar');
    Route::post('/usuario/{id}/activar',    [UserController::class, 'activar'])->name('User.activar');
});

// ── Solo Médico ──────────────────────────────────────────────────────────────
Route::middleware(['cargo:Medico'])->group(function () {
    // Tickets: solo el médico puede crear
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
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

    // Tickets: ver lista, detalle, mensajes y archivos
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/mensaje', [TicketController::class, 'mensaje'])->name('tickets.mensaje');
    Route::post('/tickets/{ticket}/archivo', [TicketController::class, 'subirArchivo'])->name('tickets.archivo');
});

// ── Ver informe: Admin, Médico y Paciente ────────────────────────────────────
Route::middleware(['cargo:Admin,Medico,Paciente'])->group(function () {
    Route::get('/Informe/{cita}/ver', [InformeController::class, 'show'])->name('informe.show');
});

// ── Admin y Paciente ────────────────────────────────────────────────────────
Route::middleware(['cargo:Admin,Paciente'])->group(function () {
    Route::post('/citas', [CitaController::class, 'store']);
});

// ── Todos los roles autenticados ─────────────────────────────────────────────
Route::middleware(['cargo:Admin,Medico,Paciente'])->group(function () {
    Route::get('/citas', [CitaController::class, 'index'])->name('citas');
    Route::get('/citas/horas-disponibles', [CitaController::class, 'horasDisponibles']);
    Route::get('/informe/pdf/{cita}', [InformeController::class, 'pdf'])->name('informe.pdf');
    Route::post('/informe/email', [InformeController::class, 'enviarPorEmail']);
    Route::get('/Historial/{paciente}', [HistorialController::class, 'index'])->name('historial.index');
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