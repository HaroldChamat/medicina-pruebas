<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\HistorialController;

// ── Rutas públicas ──────────────────────────────────────────────────────────
Route::get('/', function () {
    if (session()->has('cargo')) {
        return redirect()->route('welcome');
    }
    $especialidades = \App\Models\Especialidad::all();
    $totalEspecialidades = $especialidades->count();
    $totalMedicos = \App\Models\User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->count();
    $totalPacientes = \App\Models\User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))->count(); // ← AGREGA ESTA
    return view('inicio', compact('especialidades', 'totalEspecialidades', 'totalMedicos', 'totalPacientes')); // ← agrega $totalPacientes aquí también
})->name('inicio');
Route::get('/login', [UserController::class, 'index_welcome'])->name('welcome');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/C_usuario', [UserController::class, 'index'])->name('C_usuario');
Route::post('/usuario/store', [UserController::class, 'store'])->name('User.store');

// ── Solo Admin ──────────────────────────────────────────────────────────────
Route::middleware(['cargo:Admin'])->group(function () {
    // Usuarios
    Route::put('/usuario/{id}', [UserController::class, 'update'])->name('User.update');
    Route::delete('/usuario/{id}', [UserController::class, 'destroy'])->name('User.destroy');

    // Especialidades
    Route::get('/Especialidad', [UserController::class, 'index_especialidad'])->name('Especialidad');
    Route::post('/usuario/especialidad', [EspecialidadController::class, 'actualizarEspecialidad']);

    // Citas: solo Admin puede crear y eliminar
    Route::post('/citas', [CitaController::class, 'store']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);

    //Vistas de las gestiones de Medicos y Pacientes
    Route::get('/admin/medicos', [UserController::class, 'index_medicos'])->name('admin.medicos');
    Route::get('/admin/pacientes', [UserController::class, 'index_pacientes'])->name('admin.pacientes');
});

// ── Admin y Médico ───────────────────────────────────────────────────────────
Route::middleware(['cargo:Admin,Medico'])->group(function () {
    Route::get('/citas/{id}/edit', [CitaController::class, 'edit']);
    Route::put('/citas/{id}', [CitaController::class, 'update']);   // ← movido aquí, antes solo era Admin

    Route::get('/Informacion', [InformeController::class, 'index_paciente'])->name('informe.paciente');
    Route::get('/Informe/{cita}', [InformeController::class, 'create'])->name('informe.create');
    Route::post('/Informe/{cita}', [InformeController::class, 'store'])->name('informe.store');
    Route::put('/Informe/{cita}', [InformeController::class, 'update'])->name('informe.update');
    Route::get('/Informe', [InformeController::class, 'index'])->name('informe.index');
    

    // Horarios
    Route::get('/Horario', [HorarioController::class, 'index'])->name('Horario');
    Route::resource('horario', HorarioController::class)->only(['store', 'update']);
});

// ── Admin y Paciente (solicitar cita) ────────────────────────────────────────
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