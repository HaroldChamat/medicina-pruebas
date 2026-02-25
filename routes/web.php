<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\HistorialController;

Route::get('/Especialidad', [UserController::class, 'index_especialidad'])->name('Especialidad');
Route::get('/', [UserController::class, 'index_welcome'])->name('welcome');
Route::get('/C_usuario', [UserController::class, 'index'])->name('C_usuario');
Route::post('/usuario/store', [UserController::class, 'store'])->name('User.store');
Route::get('/Horario', [HorarioController::class, 'index'])->name('Horario');
Route::resource('horario', HorarioController::class)->only(['store', 'update']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Solo Admin ──────────────────────────────────────────────────────
Route::middleware(['cargo:Admin'])->group(function () {
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);
    Route::put('/citas/{id}', [CitaController::class, 'update']);
    Route::post('/usuario/especialidad', [EspecialidadController::class, 'actualizarEspecialidad']);
});

// ── Admin y Médico ───────────────────────────────────────────────────
Route::middleware(['cargo:Admin,Medico'])->group(function () {
    Route::get('/citas/{id}/edit', [CitaController::class, 'edit']);
    Route::get('/Informacion', [InformeController::class, 'index_paciente'])->name('informe.paciente');
    Route::get('/Informe/{cita}', [InformeController::class, 'create'])->name('informe.create');
    Route::post('/Informe/{cita}', [InformeController::class, 'store'])->name('informe.store');
    Route::get('/Historial/{paciente}', [HistorialController::class, 'index'])->name('historial.index');
});

// ── Admin y Paciente (crear cita) ────────────────────────────────────
Route::middleware(['cargo:Admin,Paciente'])->group(function () {
    Route::post('/citas', [CitaController::class, 'store']);
});

// ── Todos los roles autenticados ─────────────────────────────────────
Route::middleware(['cargo:Admin,Medico,Paciente'])->group(function () {
    Route::get('/citas', [CitaController::class, 'index'])->name('citas');
    Route::get('/citas/horas-disponibles', [CitaController::class, 'horasDisponibles']);
    Route::get('/informe/pdf/{cita}', [InformeController::class, 'pdf'])->name('informe.pdf');
    Route::post('/informe/email', [InformeController::class, 'enviarPorEmail']);
    Route::get('/Informe', [InformeController::class, 'index'])->name('informe.index');
});