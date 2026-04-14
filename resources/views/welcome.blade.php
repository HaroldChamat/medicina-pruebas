@extends('layouts.app')
@section('content')
    @if(!session()->has('cargo'))
    <div class="d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg, #0d3b6e 0%, #1a6fa8 60%, #2196b0 100%); 
           min-height: 100vh; 
           margin-top: -1px;">

        {{-- Ícono decorativo derecha --}}
        <div class="position-absolute top-0 end-0 p-5 d-none d-lg-block" style="opacity: 0.08;">
            <i class="bi bi-heart-pulse-fill" style="font-size: 18rem; color: white;"></i>
        </div>

        {{-- Ícono decorativo izquierda --}}
        <div class="position-absolute top-0 start-0 p-5 d-none d-lg-block" style="opacity: 0.08;">
            <i class="bi bi-heart-pulse-fill" style="font-size: 18rem; color: white; transform: scaleX(-1);"></i>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-hospital-fill text-white" style="font-size: 2.5rem;"></i>
                        <h4 class="text-white fw-bold mt-2">Centro Médico</h4>
                        <p class="text-white opacity-75 small">Sistema de gestión médica</p>
                    </div>

                    <div class="card border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-1 text-center" style="color: #0d3b6e;">
                                Acceso al sistema
                            </h5>
                            <p class="text-muted small text-center mb-4">
                                Ingresa tus credenciales para continuar
                            </p>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">
                                        <i class="bi bi-person me-1"></i> RUT
                                    </label>
                                    <input type="text" name="rut" id="rutLogin"
                                        class="form-control @error('rut') is-invalid @enderror"
                                        placeholder="12345678-9"
                                        value="{{ old('rut') }}"
                                        maxlength="12"
                                        autocomplete="off"
                                        required>
                                    <div class="invalid-feedback" id="rutLoginFeedback"></div>
                                    @error('rut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">
                                        <i class="bi bi-lock me-1"></i> Contraseña
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="passwordField"
                                            class="form-control"
                                            placeholder="Ingrese su contraseña" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                id="togglePassword">
                                            <i class="bi bi-eye-fill" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check p-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="remember" id="rememberMe">
                                        <label class="form-check-label small text-muted"
                                            for="rememberMe">Recordar sesión</label>
                                    </div>
                                </div>

                                <button type="submit"
                                        class="btn w-100 fw-bold py-2 text-white"
                                        style="background-color: #0d3b6e; border-radius: 8px;">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                                </button>
                            </form>

                            <p class="text-center small mt-4 mb-0 text-muted">
                                ¿Eres nuevo?
                                <a href="/C_usuario" class="fw-bold text-decoration-none"
                                style="color: #0d3b6e;">Crear cuenta</a>
                            </p>

                        </div>
                    </div>

                    <p class="text-center text-white opacity-50 small mt-3">
                        © {{ date('Y') }} Centro Médico
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ ADMIN ══ --}}
    @if(session('admin') === 1)
    <div class="container mb-4">
        <div class="p-4 rounded-3 text-white d-flex align-items-center justify-content-between"
             style="background: linear-gradient(135deg, #0d3b6e, #1a6fa8);">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-shield-check me-2"></i>Bienvenido, Administrador
                </h4>
                <small class="opacity-75">Tienes acceso completo al sistema.</small>
            </div>
            <span class="badge fs-6 px-3 py-2" style="background-color: rgba(255,255,255,0.2);">
                👑 ADMIN
            </span>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-primary-soft">
                        <i class="bi bi-people-fill fs-3 text-primary"></i>
                    </div>
                    <h6 class="fw-bold">Médicos</h6>
                    <p class="text-muted small mb-3">Gestión de médicos registrados.</p>
                    <a href="{{ route('admin.medicos') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        Ver médicos
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-success-soft">
                        <i class="bi bi-person-heart fs-3 text-success"></i>
                    </div>
                    <h6 class="fw-bold">Pacientes</h6>
                    <p class="text-muted small mb-3">Gestión de pacientes registrados.</p>
                    <a href="{{ route('admin.pacientes') }}" class="btn btn-outline-success btn-sm rounded-pill">
                        Ver pacientes
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-info-soft">
                        <i class="bi bi-calendar-week fs-3 text-info"></i>
                    </div>
                    <h6 class="fw-bold">Citas</h6>
                    <p class="text-muted small mb-3">Administrar citas del sistema.</p>
                    <a href="/citas" class="btn btn-outline-info btn-sm rounded-pill">Ver citas</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-warning-soft">
                        <i class="bi bi-person-plus fs-3 text-warning"></i>
                    </div>
                    <h6 class="fw-bold">Crear usuario</h6>
                    <p class="text-muted small mb-3">Registrar nuevos usuarios.</p>
                    <a href="/C_usuario" class="btn btn-outline-warning btn-sm rounded-pill">Crear</a>
                </div>
            </div>
        </div>
    </div>

        {{-- Modal Médicos --}}
        <div class="modal fade" id="modalMedicos" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">👨‍⚕️ Lista de Médicos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" id="buscadorMedicos" class="form-control"
                                placeholder="🔍 Buscar por nombre, apellido, RUT o email...">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light text-uppercase small">
                                    <tr>
                                        <th>#</th><th>Nombre</th><th>Apellidos</th>
                                        <th>Email</th><th>RUT</th><th>Teléfono</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($medicos as $medico)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $medico->name }}</td>
                                            <td>{{ $medico->Apellidos }}</td>
                                            <td>{{ $medico->email }}</td>
                                            <td><span class="badge bg-secondary">{{ $medico->Rut }}</span></td>
                                            <td>{{ $medico->telefono }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm btnEditarUsuario"
                                                    data-id="{{ $medico->id }}"
                                                    data-name="{{ $medico->name }}"
                                                    data-apellidos="{{ $medico->Apellidos }}"
                                                    data-email="{{ $medico->email }}"
                                                    data-telefono="{{ $medico->telefono }}"
                                                    data-tipo="Médico">✏️ Editar</button>
                                                <button class="btn btn-danger btn-sm btnEliminarUsuario"
                                                    data-id="{{ $medico->id }}"
                                                    data-name="{{ $medico->name }} {{ $medico->Apellidos }}">
                                                    🗑 Eliminar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Pacientes --}}
        <div class="modal fade" id="modalPacientes" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">🧑 Lista de Pacientes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" id="buscadorPacientes" class="form-control"
                                placeholder="🔍 Buscar por nombre, apellido, RUT o email...">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light text-uppercase small">
                                    <tr>
                                        <th>#</th><th>Nombre</th><th>Apellidos</th>
                                        <th>Email</th><th>RUT</th><th>Teléfono</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pacientes as $paciente)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $paciente->name }}</td>
                                            <td>{{ $paciente->Apellidos }}</td>
                                            <td>{{ $paciente->email }}</td>
                                            <td><span class="badge bg-secondary">{{ $paciente->Rut }}</span></td>
                                            <td>{{ $paciente->telefono }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm btnEditarUsuario"
                                                    data-id="{{ $paciente->id }}"
                                                    data-name="{{ $paciente->name }}"
                                                    data-apellidos="{{ $paciente->Apellidos }}"
                                                    data-email="{{ $paciente->email }}"
                                                    data-telefono="{{ $paciente->telefono }}"
                                                    data-tipo="Paciente">✏️ Editar</button>
                                                <button class="btn btn-danger btn-sm btnEliminarUsuario"
                                                    data-id="{{ $paciente->id }}"
                                                    data-name="{{ $paciente->name }} {{ $paciente->Apellidos }}">
                                                    🗑 Eliminar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Editar Usuario --}}
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="tituloEditarUsuario">Editar usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarUsuario">
                            @csrf
                            <input type="hidden" id="edit_user_id">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" id="edit_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Apellidos</label>
                                <input type="text" id="edit_apellidos" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" id="edit_telefono" class="form-control">
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Confirmar Eliminar --}}
        <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">⚠️ Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de eliminar a <strong id="nombreEliminar"></strong>?
                        Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-danger" id="btnConfirmarEliminar">Sí, eliminar</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- ══ MÉDICO ══ --}}
    @elseif(session('cargo') === 'Medico')
    <div class="container mb-4">
        <div class="p-4 rounded-3 text-white d-flex align-items-center justify-content-between"
             style="background: linear-gradient(135deg, #1a7a4a, #2ecc71);">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-person-badge me-2"></i>
                    Bienvenido, Dr. {{ $usuario->name }} {{ $usuario->Apellidos }}
                </h4>
                <small class="opacity-75">Aquí puedes gestionar tus citas e informes médicos.</small>
            </div>
            <span class="badge fs-6 px-3 py-2" style="background-color: rgba(255,255,255,0.2);">
                <i class="bi bi-heart-pulse me-1"></i> MÉDICO
            </span>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-primary-soft">
                        <i class="bi bi-calendar-check fs-3 text-primary"></i>
                    </div>
                    <h6 class="fw-bold">Mis citas</h6>
                    <p class="text-muted small mb-3">Ver y gestionar tus citas asignadas.</p>
                    <a href="/citas" class="btn btn-outline-primary btn-sm rounded-pill">Ver citas</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-info-soft">
                        <i class="bi bi-file-earmark-medical fs-3 text-info"></i>
                    </div>
                    <h6 class="fw-bold">Informes médicos</h6>
                    <p class="text-muted small mb-3">Crear y editar informes de tus pacientes.</p>
                    <a href="/Informacion" class="btn btn-outline-info btn-sm rounded-pill">Ver informes</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-secondary-soft">
                        <i class="bi bi-clock-history fs-3 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold">Mi horario</h6>
                    <p class="text-muted small mb-3">Consultar tu horario de atención.</p>
                    <a href="/Horario" class="btn btn-outline-secondary btn-sm rounded-pill">Ver horario</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ PACIENTE ══ --}}
    @elseif(session('cargo') === 'Paciente')
    <div class="container mb-4">
        <div class="p-4 rounded-3 text-white d-flex align-items-center justify-content-between"
             style="background: linear-gradient(135deg, #1565c0, #42a5f5);">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-person-circle me-2"></i>
                    Bienvenido/a, {{ $usuario->name }} {{ $usuario->Apellidos }}
                </h4>
                <small class="opacity-75">Puedes revisar tus citas, historial médico y solicitar nuevas atenciones.</small>
            </div>
            <span class="badge fs-6 px-3 py-2" style="background-color: rgba(255,255,255,0.2);">
                <i class="bi bi-person me-1"></i> PACIENTE
            </span>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-primary-soft">
                        <i class="bi bi-calendar-check fs-3 text-primary"></i>
                    </div>
                    <h6 class="fw-bold">Mis citas</h6>
                    <p class="text-muted small mb-3">Ver tus citas médicas y solicitar nuevas.</p>
                    <a href="/citas" class="btn btn-outline-primary btn-sm rounded-pill">
                        Ver mis citas
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 card-dashboard">
                    <div class="icon-dash mx-auto mb-3 bg-warning-soft">
                        <i class="bi bi-clock-history fs-3 text-warning"></i>
                    </div>
                    <h6 class="fw-bold">Mi historial médico</h6>
                    <p class="text-muted small mb-3">
                        Ver diagnósticos, tratamientos e informes PDF de tus citas finalizadas.
                    </p>
                    <a href="{{ route('historial.index', session('user_id')) }}"
                       class="btn btn-outline-warning btn-sm rounded-pill">
                        Ver historial
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif


@endsection

@section('javascript')
@parent
@if(session('admin') === 1)
<script>
$(document).ready(function () {

    let userIdEliminar = null;

    // ── Función helper para limpiar todos los modales ────────────────
    function limpiarModales(callback) {
        const modalesAbiertos = document.querySelectorAll('.modal.show');
        
        if (modalesAbiertos.length === 0) {
            // No hay modales abiertos, ejecutar callback directo
            if (callback) callback();
            return;
        }

        modalesAbiertos.forEach(m => {
            const instancia = bootstrap.Modal.getInstance(m);
            if (instancia) instancia.hide();
        });

        // Esperar a que terminen las animaciones
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.querySelectorAll('.modal.show').forEach(el => {
                el.classList.remove('show');
                el.style.display = 'none';
            });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            if (callback) callback();
        }, 350);
    }

    // ── Abrir modal médicos ──────────────────────────────────────────
    $('#btnVerMedicos').on('click', function () {
        new bootstrap.Modal(document.getElementById('modalMedicos')).show();
    });

    // ── Abrir modal pacientes ────────────────────────────────────────
    $('#btnVerPacientes').on('click', function () {
        new bootstrap.Modal(document.getElementById('modalPacientes')).show();
    });

    // ── Abrir editar ─────────────────────────────────────────────────
    $(document).on('click', '.btnEditarUsuario', function () {
        let btn = $(this);
        $('#edit_user_id').val(btn.data('id'));
        $('#edit_name').val(btn.data('name'));
        $('#edit_apellidos').val(btn.data('apellidos'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_telefono').val(btn.data('telefono'));
        $('#tituloEditarUsuario').text('Editar ' + btn.data('tipo'));

        limpiarModales(() => {
            new bootstrap.Modal(document.getElementById('modalEditarUsuario')).show();
        });
    });

    // ── Guardar edición ──────────────────────────────────────────────
    $('#formEditarUsuario').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/usuario/' + $('#edit_user_id').val(),
            method: 'POST',
            data: {
                _token:    $('meta[name="csrf-token"]').attr('content'),
                _method:   'PUT',
                name:      $('#edit_name').val(),
                Apellidos: $('#edit_apellidos').val(),
                email:     $('#edit_email').val(),
                telefono:  $('#edit_telefono').val(),
            },
            success: function () {
                limpiarModales(() => location.reload());
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message ?? 'Error al guardar');
            }
        });
    });

    // ── Abrir eliminar ───────────────────────────────────────────────
    $(document).on('click', '.btnEliminarUsuario', function () {
        userIdEliminar = $(this).data('id');
        $('#nombreEliminar').text($(this).data('name'));

        limpiarModales(() => {
            new bootstrap.Modal(document.getElementById('modalConfirmarEliminar')).show();
        });
    });

    // ── Confirmar eliminar ───────────────────────────────────────────
    $('#btnConfirmarEliminar').on('click', function () {
        $.ajax({
            url: '/usuario/' + userIdEliminar,
            method: 'POST',
            data: {
                _token:  $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE',
            },
            success: function () {
                limpiarModales(() => location.reload());
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message ?? 'Error al eliminar');
            }
        });
    });

    // ── Buscador médicos ─────────────────────────────────────────────
    $('#buscadorMedicos').on('keyup', function () {
        const texto = $(this).val().toLowerCase();
        $('#modalMedicos tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(texto));
        });
    });

    // ── Buscador pacientes ───────────────────────────────────────────
    $('#buscadorPacientes').on('keyup', function () {
        const texto = $(this).val().toLowerCase();
        $('#modalPacientes tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(texto));
        });
    });

    // ── Limpiar buscadores al cerrar ─────────────────────────────────
    $('#modalMedicos').on('hidden.bs.modal', function () {
        $('#buscadorMedicos').val('');
        $('#modalMedicos tbody tr').show();
    });
    $('#modalPacientes').on('hidden.bs.modal', function () {
        $('#buscadorPacientes').val('');
        $('#modalPacientes tbody tr').show();
    });

});
</script>
@endif

<script>
// ── FORMATO AUTOMÁTICO DE RUT ────────────────────────────────────────────────
function formatearRut(rut) {
    var limpio = rut.replace(/[^0-9kK]/g, '');
    if (limpio.length < 2) return limpio;

    var cuerpo = limpio.slice(0, -1);
    var dv     = limpio.slice(-1).toUpperCase();
    return cuerpo + '-' + dv;
}

document.getElementById('rutLogin')?.addEventListener('input', function () {
    var val = formatearRut(this.value);
    this.value = val;
});

// Toggle contraseña login 
const passwordField = document.getElementById('passwordField');
const togglePassword = document.getElementById('togglePassword');
const eyeIcon = document.getElementById('eyeIcon');

if (togglePassword) {
    togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        eyeIcon.classList.toggle('bi-eye-fill');
        eyeIcon.classList.toggle('bi-eye-slash-fill');
    });
}
</script>

<style>
    .card-dashboard {
        border-radius: 14px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-dashboard:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    .icon-dash {
        width: 64px; height: 64px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .bg-primary-soft   { background-color: #e8f0fb; }
    .bg-success-soft   { background-color: #e6f9f0; }
    .bg-info-soft      { background-color: #e3f6fc; }
    .bg-warning-soft   { background-color: #fff8e1; }
    .bg-danger-soft    { background-color: #fdecea; }
    .bg-secondary-soft { background-color: #f0f0f0; }

</style>

@endsection