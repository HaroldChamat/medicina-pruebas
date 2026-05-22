@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-heart me-2"></i> Lista de Pacientes
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">Gestión de pacientes registrados en el sistema.</p>
        </div>
        <a href="/login" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Buscador --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <input type="text" id="buscador" class="form-control"
                   placeholder="🔍 Buscar por nombre, apellido, RUT o correo...">
        </div>
    </div>

    {{-- Contador --}}
    <p class="small mb-2" style="color:rgba(255,255,255,0.8);">
        Mostrando <strong id="contadorVisible">{{ $pacientes->count() }}</strong> paciente(s)
    </p>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-uppercase small text-white"
                           style="background-color: #0d3b6e;">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>RUT</th>
                            <th>Teléfono</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        @foreach($pacientes as $paciente)
                            <tr data-busqueda="{{ strtolower($paciente->name . ' ' . $paciente->Apellidos . ' ' . $paciente->Rut . ' ' . $paciente->email) }}">
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>{{ $paciente->name }}</td>
                                <td>{{ $paciente->Apellidos }}</td>
                                <td>{{ $paciente->email }}</td>
                                <td><span class="badge bg-secondary">{{ $paciente->Rut }}</span></td>
                                <td>{{ $paciente->telefono }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm btnEditar"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }}"
                                        data-apellidos="{{ $paciente->Apellidos }}"
                                        data-email="{{ $paciente->email }}"
                                        data-telefono="{{ $paciente->telefono }}"
                                        data-rut="{{ $paciente->Rut }}">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm btnEliminar"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }} {{ $paciente->Apellidos }}">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL EDITAR ===== --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil me-2"></i> Editar Paciente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    @csrf
                    <input type="hidden" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" id="edit_name" class="form-control" required>
                        <div class="invalid-feedback" id="fb_name"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Apellidos</label>
                        <input type="text" id="edit_apellidos" class="form-control" required>
                        <div class="invalid-feedback" id="fb_apellidos"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo electrónico</label>
                        <input type="text" id="edit_email" class="form-control" required autocomplete="off">
                        <div class="invalid-feedback" id="fb_email">
                            Ingresa un correo electrónico válido (ejemplo@correo.com).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">RUT</label>
                        <input type="text" id="edit_rut" class="form-control"
                               maxlength="12" autocomplete="off" placeholder="12345678-9">
                        <div class="invalid-feedback" id="fb_rut">
                            Formato inválido. Ejemplo: 12345678-9
                        </div>
                        <small class="text-muted">El guión se agrega automáticamente.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-semibold">
                                <i class="bi bi-telephone me-1"></i>
                            </span>
                            <input type="text" id="edit_telefono" class="form-control"
                                   placeholder="+56912345678" maxlength="20" autocomplete="off">
                        </div>
                        <div class="invalid-feedback" id="fb_telefono">
                            Solo se permiten números y el signo +. Ejemplo: +56912345678
                        </div>
                        <small class="text-muted">Puedes incluir el indicativo del país. Ejemplo: +56912345678</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nueva contraseña
                            <span class="text-muted fw-normal small">(dejar en blanco para no cambiar)</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="edit_password" class="form-control"
                                   placeholder="Nueva contraseña" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="toggleEditPass">
                                <i class="bi bi-eye-fill" id="eyeEditPass"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="fb_password">
                            La contraseña debe tener al menos 6 caracteres.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill">
                            <i class="bi bi-save me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL CONFIRMAR ELIMINAR ===== --}}
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de eliminar a <strong id="nombreEliminar"></strong>?</p>
                <div class="alert alert-warning small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Esta acción no se puede deshacer. Las citas con informes médicos
                    se conservarán en el sistema.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger rounded-pill" id="btnConfirmarEliminar">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    let userIdEliminar = null;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // ── TOGGLE CONTRASEÑA ────────────────────────────────────────────────
    $('#toggleEditPass').on('click', function () {
        const tipo = $('#edit_password').attr('type') === 'password' ? 'text' : 'password';
        $('#edit_password').attr('type', tipo);
        $('#eyeEditPass').toggleClass('bi-eye-fill bi-eye-slash-fill');
    });

    // ── HELPERS DE VALIDACIÓN ────────────────────────────────────────────
    function formatearRut(rut) {
        const limpio = rut.replace(/[^0-9kK]/g, '');
        if (limpio.length < 2) return limpio;
        return limpio.slice(0, -1) + '-' + limpio.slice(-1).toUpperCase();
    }

    function validarFormatoRut(rut) {
        return /^\d{7,8}-[\dkK]$/.test(rut);
    }

    function validarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
    }

    // ── BUSCADOR ─────────────────────────────────────────────────────────
    $('#buscador').on('keyup', function () {
        const texto = $(this).val().toLowerCase().trim();
        let visible = 0;
        $('#tablaBody tr').each(function () {
            const coincide = !texto || $(this).data('busqueda').includes(texto);
            $(this).toggle(coincide);
            if (coincide) visible++;
        });
        $('#contadorVisible').text(visible);
    });

    // ── FORMATO RUT AL ESCRIBIR ──────────────────────────────────────────
    $('#edit_rut').on('input', function () {
        const formatted = formatearRut(this.value);
        this.value = formatted;
        if (formatted && !validarFormatoRut(formatted)) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#fb_rut').text('Formato inválido. Ejemplo: 12345678-9');
        } else if (formatted) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // ── VALIDACIÓN CORREO AL ESCRIBIR ────────────────────────────────────
    $('#edit_email').on('input blur', function () {
        const val = $(this).val().trim();
        if (!val) {
            $(this).removeClass('is-valid is-invalid');
            return;
        }
        if (validarEmail(val)) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $('#fb_email').text('');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#fb_email').text('Ingresa un correo electrónico válido (ejemplo@correo.com).');
        }
    });

    // ── VALIDACIÓN TELÉFONO (solo + y números) ───────────────────────────
    $('#edit_telefono').on('input', function () {
        let val = this.value;
        val = val.replace(/[^\d+]/g, '');
        val = val.replace(/(?!^)\+/g, '');
        this.value = val;
        const limpio = val.replace(/\D/g, '');
        if (val.length > 0 && limpio.length >= 7) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else if (val.length > 0) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#fb_telefono').text('El número debe tener al menos 7 dígitos.');
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // ── VALIDACIÓN CONTRASEÑA AL ESCRIBIR ────────────────────────────────
    $('#edit_password').on('input', function () {
        const val = $(this).val();
        if (!val) {
            $(this).removeClass('is-valid is-invalid');
        } else if (val.length < 6) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#fb_password').text('La contraseña debe tener al menos 6 caracteres.');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });

    // ── ABRIR MODAL EDITAR ───────────────────────────────────────────────
    $(document).on('click', '.btnEditar', function () {
        const btn = $(this);

        $('#edit_id').val(btn.data('id'));
        $('#edit_name').val(btn.data('name'));
        $('#edit_apellidos').val(btn.data('apellidos'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_rut').val(btn.data('rut'));
        $('#edit_telefono').val(btn.data('telefono'));
        $('#edit_password').val('');

        // Limpiar estados de validación al abrir
        $('#formEditar input').removeClass('is-valid is-invalid');
        $('#formEditar .invalid-feedback').text('');

        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });

    // ── GUARDAR EDICIÓN ──────────────────────────────────────────────────
    $('#formEditar').on('submit', function (e) {
        e.preventDefault();

        const name      = $('#edit_name').val().trim();
        const apellidos = $('#edit_apellidos').val().trim();
        const email     = $('#edit_email').val().trim();
        const rut       = $('#edit_rut').val().trim();
        const telefono  = $('#edit_telefono').val().trim();
        const password  = $('#edit_password').val();

        let hayError = false;

        // Validar nombre
        if (!name) {
            $('#edit_name').addClass('is-invalid');
            $('#fb_name').text('El nombre es obligatorio.');
            hayError = true;
        } else {
            $('#edit_name').removeClass('is-invalid').addClass('is-valid');
        }

        // Validar apellidos
        if (!apellidos) {
            $('#edit_apellidos').addClass('is-invalid');
            $('#fb_apellidos').text('Los apellidos son obligatorios.');
            hayError = true;
        } else {
            $('#edit_apellidos').removeClass('is-invalid').addClass('is-valid');
        }

        // Validar correo
        if (!email || !validarEmail(email)) {
            $('#edit_email').removeClass('is-valid').addClass('is-invalid');
            $('#fb_email').text(
                !email
                    ? 'El correo electrónico es obligatorio.'
                    : 'Ingresa un correo electrónico válido (ejemplo@correo.com).'
            );
            hayError = true;
        }

        // Validar RUT si fue ingresado
        if (rut && !validarFormatoRut(rut)) {
            $('#edit_rut').removeClass('is-valid').addClass('is-invalid');
            $('#fb_rut').text('Formato de RUT inválido. Ejemplo: 12345678-9');
            hayError = true;
        }

        // Validar teléfono si fue ingresado
        if (telefono) {
            const limpio = telefono.replace(/\D/g, '');
            if (limpio.length < 7) {
                $('#edit_telefono').removeClass('is-valid').addClass('is-invalid');
                $('#fb_telefono').text('El número debe tener al menos 7 dígitos.');
                hayError = true;
            }
        }

        // Validar contraseña si fue ingresada
        if (password && password.length < 6) {
            $('#edit_password').removeClass('is-valid').addClass('is-invalid');
            $('#fb_password').text('La contraseña debe tener al menos 6 caracteres.');
            hayError = true;
        }

        if (hayError) {
            mostrarToast('Por favor corrige los errores antes de guardar.', 'warning');
            return;
        }

        const datos = {
            _token:    csrfToken,
            _method:   'PUT',
            name:      name,
            Apellidos: apellidos,
            email:     email,
            Rut:       rut,
            telefono:  telefono,
        };

        if (password) {
            datos.password = password;
        }

        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url: '/usuario/' + $('#edit_id').val(),
            method: 'POST',
            data: datos,
            success: function () {
                mostrarToast('Paciente actualizado correctamente.', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                const errores = xhr.responseJSON?.errors;
                if (errores) {
                    if (errores.email) {
                        $('#edit_email').removeClass('is-valid').addClass('is-invalid');
                        $('#fb_email').text(errores.email[0]);
                    }
                    if (errores.Rut) {
                        $('#edit_rut').removeClass('is-valid').addClass('is-invalid');
                        $('#fb_rut').text(errores.Rut[0]);
                        mostrarToast(errores.Rut[0] + ' Por favor ingresa otro RUT.', 'danger');
                        $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar cambios');
                        return;
                    }
                    if (errores.telefono) {
                        $('#edit_telefono').removeClass('is-valid').addClass('is-invalid');
                        $('#fb_telefono').text(errores.telefono[0]);
                    }
                    const primerError = Object.values(errores).flat()[0];
                    mostrarToast(primerError, 'danger');
                } else {
                    mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar los cambios.', 'danger');
                }
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar cambios');
            }
        });
    });

    // ── ABRIR MODAL ELIMINAR ─────────────────────────────────────────────
    $(document).on('click', '.btnEliminar', function () {
        userIdEliminar = $(this).data('id');
        $('#nombreEliminar').text($(this).data('name'));
        new bootstrap.Modal(document.getElementById('modalEliminar')).show();
    });

    // ── CONFIRMAR ELIMINAR ───────────────────────────────────────────────
    $('#btnConfirmarEliminar').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...');

        $.ajax({
            url: '/usuario/' + userIdEliminar,
            method: 'POST',
            data: {
                _token:  csrfToken,
                _method: 'DELETE',
            },
            success: function () {
                mostrarToast('Paciente eliminado correctamente.', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al eliminar el paciente.', 'danger');
                $btn.prop('disabled', false).html('<i class="bi bi-trash me-1"></i> Sí, eliminar');
            }
        });
    });

});
</script>
@endsection