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
    
    // 1. RUTA DE ESPECIALIDADES (Prioritaria y única para evitar conflictos)
    Route::put('/asignar-especialidad-medico', [EspecialidadController::class, 'actualizarEspecialidad'])->name('especialidad.update');
    Route::post('/especialidad', [EspecialidadController::class, 'store'])->name('especialidad.store');

    // 2. Gestión de usuarios y otros
    Route::put('/usuario/{id}', [UserController::class, 'update'])->name('User.update');
    Route::delete('/usuario/{id}', [UserController::class, 'destroy'])->name('User.destroy');
    Route::get('/Especialidad', [UserController::class, 'index_especialidad'])->name('Especialidad');
    
    Route::post('/citas', [CitaController::class, 'store']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);
    Route::get('/admin/medicos', [UserController::class, 'index_medicos'])->name('admin.medicos');
    Route::get('/admin/pacientes', [UserController::class, 'index_pacientes'])->name('admin.pacientes');
});

// ── Admin y Médico ───────────────────────────────────────────────────────────
Route::middleware(['cargo:Admin,Medico'])->group(function () {
    Route::get('/citas/{id}/edit', [CitaController::class, 'edit']);
    Route::put('/citas/{id}', [CitaController::class, 'update']);
    Route::get('/Informacion', [InformeController::class, 'index_paciente'])->name('informe.paciente');
    Route::get('/Informe', [InformeController::class, 'index'])->name('informe.index');
    Route::get('/Informe/{cita}/editar', [InformeController::class, 'edit'])->name('informe.edit');
    Route::get('/Informe/{cita}', [InformeController::class, 'create'])->name('informe.create');
    Route::post('/Informe/{cita}', [InformeController::class, 'store'])->name('informe.store');
    Route::put('/Informe/{cita}', [InformeController::class, 'update'])->name('informe.update');
    Route::get('/Horario', [HorarioController::class, 'index'])->name('Horario');
    Route::resource('horario', HorarioController::class)->only(['store', 'update']);
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
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{cita}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{cita}', [ChatController::class, 'store'])->name('chat.store');

    // ... resto de rutas (horas-disponibles, pdf, email, historial)
    Route::get('/citas/horas-disponibles', [CitaController::class, 'horasDisponibles']);
    Route::get('/informe/pdf/{cita}', [InformeController::class, 'pdf'])->name('informe.pdf');
    // Mantenemos la lógica de historial e informes
    Route::post('/informe/email', [InformeController::class, 'enviarPorEmail']);
    Route::get('/Historial/{paciente}', [HistorialController::class, 'index'])->name('historial.index');
    
    // Ruta para marcar leídos
    Route::post('/chat/{cita}/leer', function (\App\Models\Cita $cita) 
    {
        \App\Models\Mensaje::where('cita_id', $cita->id)
            ->where('receptor_id', session('user_id'))
            ->where('leido', false)
            ->update(['leido' => true]);
        return response()->json(['ok' => true]);
    });
});

//---- Ruta para obtener notificaciones-----//

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