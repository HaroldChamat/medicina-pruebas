@extends('layouts.app')

    @section('content')

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">

                            <h4 class="text-center fw-bold mb-4">
                                Registro de Usuario
                            </h4>

                            <form action="{{ route('User.store') }}" method="POST" id="formUser">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nombre</label>
                                    <input type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        placeholder="Nombre">
                                </div>

                                <div class="mb-3">
                                    <label for="Apellidos" class="form-label fw-semibold">Apellidos</label>
                                    <input type="text"
                                        class="form-control"
                                        id="Apellidos"
                                        name="Apellidos"
                                        placeholder="Apellidos">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="example@gmail.com">
                                </div>

                               <div class="mb-3">
                                    <label for="passwordField" class="form-label small fw-semibold">Contraseña</label>
                                    <input type="password" 
                                        class="form-control" 
                                        id="passwordField" 
                                        name="password" 
                                        placeholder="Ingrese su contraseña" 
                                        required>
                                </div>

                                <div class="mb-3">
                                <label for="Rut" class="form-label fw-semibold">Rut</label>
                                <input type="text"
                                    class="form-control"
                                    id="Rut"
                                    name="Rut"
                                    placeholder="12345678-9">
                                <div class="invalid-feedback" id="rutFeedback">Formato inválido</div>
                            </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                    <input type="number"
                                        class="form-control"
                                        id="telefono"
                                        name="telefono"
                                        placeholder="Teléfono">
                                </div>

                                {{-- Si es admin, puede elegir cargo. Si no, se asigna Paciente automáticamente --}}
                                @if(session('admin') === 1)
                                    <div class="mb-4">
                                        <label for="id_cargo" class="form-label fw-semibold">Cargo</label>
                                        <select name="id_cargo" id="id_cargo" class="form-select" required>
                                            <option value="" disabled selected>Seleccione un cargo</option>
                                            @foreach($cargos as $cargo)
                                                <option value="{{ $cargo->id }}">{{ $cargo->Nombre_cargo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    @php $cargoPaciente = $cargos->firstWhere('Nombre_cargo', 'Paciente') @endphp
                                    <input type="hidden" name="id_cargo" value="{{ $cargoPaciente->id }}">
                                @endif

                                <button type="submit"
                                        class="btn btn-primary w-100 fw-semibold"
                                        id="Ingresar_U">
                                    Ingresar Usuario
                                </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

@section('javascript')
<script>
$(document).ready(function () {

    // ── VALIDACIÓN EN TIEMPO REAL ────────────────────────────────────────
    function validarRut(rut) {
        return /^\d{7,8}-[\dkK]$/.test(rut);
    }

    $('#Rut').on('input', function () {
        const val = $(this).val();
        if (val && !validarRut(val)) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#rutFeedback').text('Formato inválido. Ej: 12345678-9');
        } else if (val) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $('#rutFeedback').text('');
        }
    });

    $('#email').on('input', function () {
        const val = $(this).val();
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
        $(this).toggleClass('is-valid', valid).toggleClass('is-invalid', val.length > 0 && !valid);
    });

    $('#telefono').on('input', function () {
        const val = $(this).val();
        const valid = val.length >= 8 && val.length <= 12;
        $(this).toggleClass('is-valid', valid).toggleClass('is-invalid', val.length > 0 && !valid);
    });

    // ── ENVÍO DEL FORMULARIO ─────────────────────────────────────────────
    $('#Ingresar_U').on('click', function (e) {
        e.preventDefault();

        const name      = $('#name').val().trim();
        const Apellidos = $('#Apellidos').val().trim();
        const email     = $('#email').val().trim();
        const Rut       = $('#Rut').val().trim();
        const telefono  = $('#telefono').val().trim();
        const id_cargo  = $('#id_cargo').val() || $('input[name="id_cargo"]').val();

        // Validación básica
        if (!name || !Apellidos || !email || !Rut || !telefono || !id_cargo) {
            mostrarToast('Por favor completa todos los campos obligatorios.', 'warning');
            // Marcar campos vacíos
            [['#name', name], ['#Apellidos', Apellidos], ['#email', email],
             ['#Rut', Rut], ['#telefono', telefono]].forEach(([id, val]) => {
                if (!val) $(id).addClass('is-invalid');
            });
            return;
        }

        if (!validarRut(Rut)) {
            mostrarToast('El RUT ingresado no tiene formato válido.', 'danger');
            $('#Rut').addClass('is-invalid');
            return;
        }

        // Deshabilitar botón mientras se envía
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url: '/usuario/store',
            type: 'POST',
            dataType: 'json',
            data: { name, Apellidos, email, Rut, telefono, id_cargo, admin: 0 },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                mostrarToast('✅ Usuario registrado correctamente', 'success');
                agregarNotificacion('Nuevo usuario registrado: ' + name + ' ' + Apellidos, 'success');
                setTimeout(() => window.location.href = '/login', 1800);
            },
            error: function (xhr) {
                const errores = xhr.responseJSON?.errors;
                if (errores) {
                    const msgs = Object.values(errores).flat().join('<br>');
                    mostrarToast(msgs, 'danger');
                } else {
                    mostrarToast(xhr.responseJSON?.message ?? 'Error al registrar el usuario', 'danger');
                }
                $('#Ingresar_U').prop('disabled', false).html('Ingresar Usuario');
            }
        });
    });

    // Quitar clase inválida al escribir
    $('input, select').on('input change', function () {
        $(this).removeClass('is-invalid');
    });
});
</script>

{{-- Feedback de RUT --}}
<style>
    #Rut ~ .invalid-feedback { display: block; }
</style>
@endsection