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
                                    <div class="invalid-feedback" id="nameFeedback"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="Apellidos" class="form-label fw-semibold">Apellidos</label>
                                    <input type="text"
                                        class="form-control"
                                        id="Apellidos"
                                        name="Apellidos"
                                        placeholder="Apellidos">
                                    <div class="invalid-feedback" id="apellidosFeedback"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                                    <input type="text"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="ejemplo@correo.com"
                                        autocomplete="off">
                                    <div class="invalid-feedback" id="emailFeedback">Ingresa un correo electrónico válido (ejemplo@correo.com).</div>
                                </div>

                                <div class="mb-3">
                                    <label for="passwordField" class="form-label fw-semibold">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control"
                                            id="passwordField"
                                            name="password"
                                            placeholder="Ingrese su contraseña"
                                            required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePass">
                                            <i class="bi bi-eye-fill" id="eyeIconPass"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="passwordFeedback">La contraseña debe tener al menos 6 caracteres.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="Rut" class="form-label fw-semibold">RUT</label>
                                    <input type="text"
                                        class="form-control"
                                        id="Rut"
                                        name="Rut"
                                        placeholder="12345678-9"
                                        maxlength="12"
                                        autocomplete="off">
                                    <div class="invalid-feedback" id="rutFeedback">Formato inválido. Ejemplo: 12345678-9</div>
                                    <small class="text-muted">El guión se agrega automáticamente.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-semibold" style="min-width:56px;">
                                            <i class="bi bi-telephone me-1"></i>
                                            <span id="indicativoLabel">+56</span>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            id="telefono"
                                            name="telefono"
                                            placeholder="912345678"
                                            maxlength="20"
                                            autocomplete="off">
                                    </div>
                                    <div class="invalid-feedback" id="telefonoFeedback">Solo se permiten números y el signo +. Ejemplo: +56912345678</div>
                                    <small class="text-muted">Puedes incluir el indicativo del país. Ejemplo: +56912345678</small>
                                </div>

                                {{-- Si es admin, puede elegir cargo --}}
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

                                <button type="button"
                                        class="btn btn-primary w-100 fw-semibold"
                                        id="Ingresar_U">
                                    Registrar Usuario
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

    // ── TOGGLE CONTRASEÑA ────────────────────────────────────────────────
    $('#togglePass').on('click', function () {
        const tipo = $('#passwordField').attr('type') === 'password' ? 'text' : 'password';
        $('#passwordField').attr('type', tipo);
        $('#eyeIconPass').toggleClass('bi-eye-fill bi-eye-slash-fill');
    });

    // ── FORMATO AUTOMÁTICO DE RUT ────────────────────────────────────────
    function formatearRut(rut) {
        var limpio = rut.replace(/[^0-9kK]/g, '');
        if (limpio.length < 2) return limpio;
        var cuerpo = limpio.slice(0, -1);
        var dv     = limpio.slice(-1).toUpperCase();
        return cuerpo + '-' + dv;
    }

    function validarFormatoRut(rut) {
        return /^\d{7,8}-[\dkK]$/.test(rut);
    }

    $('#Rut').on('input', function () {
        var formatted = formatearRut(this.value);
        this.value = formatted;
        if (formatted && !validarFormatoRut(formatted)) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#rutFeedback').text('Formato inválido. Ejemplo: 12345678-9');
        } else if (formatted) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // ── VALIDACIÓN CORREO ELECTRÓNICO ────────────────────────────────────
    function validarEmail(email) {
        // Validación estándar: algo@algo.algo
        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
    }

    $('#email').on('input blur', function () {
        const val = $(this).val().trim();
        if (val.length === 0) {
            $(this).removeClass('is-valid is-invalid');
            return;
        }
        if (validarEmail(val)) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $('#emailFeedback').text('');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#emailFeedback').text('Ingresa un correo electrónico válido (ejemplo@correo.com).');
        }
    });

    // ── VALIDACIÓN TELÉFONO (solo + y números) ───────────────────────────
    $('#telefono').on('input', function () {
        // Solo permite + al inicio y dígitos
        let val = this.value;
        // Permitir solo: + al comienzo y números
        val = val.replace(/[^\d+]/g, '');
        // Solo un + permitido y solo al inicio
        val = val.replace(/(?!^)\+/g, '');
        this.value = val;

        const limpio = val.replace(/\D/g, '');
        if (val.length > 0 && limpio.length >= 7) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else if (val.length > 0) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#telefonoFeedback').text('El número debe tener al menos 7 dígitos.');
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // ── VALIDACIÓN CONTRASEÑA ────────────────────────────────────────────
    $('#passwordField').on('input', function () {
        const val = $(this).val();
        if (val.length === 0) {
            $(this).removeClass('is-valid is-invalid');
        } else if (val.length < 6) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#passwordFeedback').text('La contraseña debe tener al menos 6 caracteres.');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
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
        const password  = $('#passwordField').val().trim();

        let hayError = false;

        if (!name) {
            $('#name').addClass('is-invalid');
            $('#nameFeedback').text('El nombre es obligatorio.');
            hayError = true;
        } else {
            $('#name').removeClass('is-invalid').addClass('is-valid');
        }

        if (!Apellidos) {
            $('#Apellidos').addClass('is-invalid');
            $('#apellidosFeedback').text('Los apellidos son obligatorios.');
            hayError = true;
        } else {
            $('#Apellidos').removeClass('is-invalid').addClass('is-valid');
        }

        if (!email || !validarEmail(email)) {
            $('#email').removeClass('is-valid').addClass('is-invalid');
            $('#emailFeedback').text(
                !email
                    ? 'El correo electrónico es obligatorio.'
                    : 'Ingresa un correo electrónico válido (ejemplo@correo.com).'
            );
            hayError = true;
        }

        if (!Rut || !validarFormatoRut(Rut)) {
            $('#Rut').removeClass('is-valid').addClass('is-invalid');
            $('#rutFeedback').text(
                !Rut
                    ? 'El RUT es obligatorio.'
                    : 'Formato de RUT inválido. Ejemplo: 12345678-9'
            );
            hayError = true;
        }

        const telefonoLimpio = telefono.replace(/\D/g, '');
        if (!telefono || telefonoLimpio.length < 7) {
            $('#telefono').removeClass('is-valid').addClass('is-invalid');
            $('#telefonoFeedback').text(
                !telefono
                    ? 'El teléfono es obligatorio.'
                    : 'El número debe tener al menos 7 dígitos.'
            );
            hayError = true;
        }

        if (!id_cargo) {
            mostrarToast('Debes seleccionar un cargo.', 'warning');
            hayError = true;
        }

        if (!password || password.length < 6) {
            $('#passwordField').removeClass('is-valid').addClass('is-invalid');
            $('#passwordFeedback').text(
                !password
                    ? 'La contraseña es obligatoria.'
                    : 'La contraseña debe tener al menos 6 caracteres.'
            );
            hayError = true;
        }

        if (hayError) {
            mostrarToast('Por favor corrige los errores antes de continuar.', 'warning');
            return;
        }

        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        const esAdmin = $('#id_cargo option:selected').text().trim() === 'Admin';

        $.ajax({
            url: '/usuario/store',
            type: 'POST',
            dataType: 'json',
            data: {
                name, Apellidos, email, Rut, telefono, id_cargo, password,
                admin: esAdmin ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                mostrarToast('✅ Usuario registrado correctamente.', 'success');
                setTimeout(() => window.location.href = '/login', 1800);
            },
            error: function (xhr) {
                const errores = xhr.responseJSON?.errors;
                if (errores) {
                    // Mostrar errores específicos en los campos correspondientes
                    if (errores.email) {
                        $('#email').addClass('is-invalid');
                        $('#emailFeedback').text(errores.email[0]);
                    }
                    if (errores.Rut) {
                        $('#Rut').addClass('is-invalid');
                        $('#rutFeedback').text(errores.Rut[0]);
                        mostrarToast(errores.Rut[0] + ' Por favor ingresa otro RUT.', 'danger');
                    }
                    if (errores.telefono) {
                        $('#telefono').addClass('is-invalid');
                        $('#telefonoFeedback').text(errores.telefono[0]);
                    }
                    // Mostrar el primer error como toast general
                    const primerError = Object.values(errores).flat()[0];
                    mostrarToast(primerError, 'danger');
                } else {
                    mostrarToast(xhr.responseJSON?.message ?? 'Error al registrar el usuario.', 'danger');
                }
                $('#Ingresar_U').prop('disabled', false).html('Registrar Usuario');
            }
        });
    });

    // Limpiar estado inválido al escribir
    $('input, select').on('input change', function () {
        if ($(this).attr('id') !== 'Rut' && $(this).attr('id') !== 'email' && $(this).attr('id') !== 'telefono') {
            $(this).removeClass('is-invalid');
        }
    });

});
</script>

<style>
    #Rut ~ .invalid-feedback,
    #email ~ .invalid-feedback,
    #telefono ~ .invalid-feedback,
    #passwordField ~ .invalid-feedback { display: block; }
    .input-group .invalid-feedback { display: block; }
</style>
@endsection