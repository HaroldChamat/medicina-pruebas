@extends('layouts.app')
@section('content')

    @if(!session()->has('cargo'))
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-3 fw-semibold">Acceso al sistema</h5>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="rut" class="form-control"
                                           placeholder="Ingrese RUT" required>
                                </div>
                                @error('rut')
                                    <div class="text-danger small mb-2">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn btn-primary w-100 mt-3">Ingresar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ ADMIN ══ --}}
    @if(session('admin') === 1)
        <div class="container mb-4">
            <div class="alert alert-success d-flex align-items-center justify-content-between shadow-sm">
                <div>
                    <strong>Bienvenido, Administrador</strong><br>
                    <small>Tienes acceso completo al sistema.</small>
                </div>
                <span class="badge bg-success fs-6">👑 ADMIN</span>
            </div>
        </div>

        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-primary mb-2">👨‍⚕️ Médicos</h5>
                            <p class="card-text text-muted mb-4">Gestión de médicos registrados.</p>
                            <button type="button" class="btn btn-outline-primary mt-auto w-100"
                                    data-bs-toggle="modal" data-bs-target="#modalMedicos">
                                Ver y gestionar médicos
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-success mb-2">🧑 Pacientes</h5>
                            <p class="card-text text-muted mb-4">Gestión de pacientes registrados.</p>
                            <button type="button" class="btn btn-outline-success mt-auto w-100"
                                    data-bs-toggle="modal" data-bs-target="#modalPacientes">
                                Ver y gestionar pacientes
                            </button>
                        </div>
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
            <div class="alert alert-success d-flex align-items-center justify-content-between shadow-sm">
                <div>
                    <strong>Bienvenido, Dr. {{ $usuario->name }} {{ $usuario->Apellidos }}</strong><br>
                    <small>Aquí puedes gestionar tus citas e informes médicos.</small>
                </div>
                <span class="badge bg-success fs-6">👨‍⚕️ MÉDICO</span>
            </div>
        </div>
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column">
                            <div class="fs-1 mb-2">📅</div>
                            <h5 class="fw-bold">Mis citas</h5>
                            <p class="text-muted small">Ver y gestionar tus citas asignadas.</p>
                            <a href="/citas" class="btn btn-outline-primary mt-auto">Ver citas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column">
                            <div class="fs-1 mb-2">📋</div>
                            <h5 class="fw-bold">Informes médicos</h5>
                            <p class="text-muted small">Crear y editar informes de tus pacientes.</p>
                            <a href="/Informacion" class="btn btn-outline-info mt-auto">Ver informes</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column">
                            <div class="fs-1 mb-2">🕐</div>
                            <h5 class="fw-bold">Mi horario</h5>
                            <p class="text-muted small">Consultar tu horario de atención.</p>
                            <a href="/Horario" class="btn btn-outline-secondary mt-auto">Ver horario</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ══ PACIENTE ══ --}}
    @elseif(session('cargo') === 'Paciente')
        <div class="container mb-4">
            <div class="alert alert-info text-center shadow-sm">
                <strong>Bienvenido/a, {{ $usuario->name }} {{ $usuario->Apellidos }}</strong>
                <hr>
                <small>Puedes revisar tus citas, tu historial médico y solicitar nuevas atenciones.</small>
            </div>
        </div>
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column">
                            <div class="fs-1 mb-2">📅</div>
                            <h5 class="fw-bold">Mis citas</h5>
                            <p class="text-muted small">Ver tus citas médicas y solicitar nuevas.</p>
                            <a href="/citas" class="btn btn-outline-primary mt-auto">Ver mis citas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column">
                            <div class="fs-1 mb-2">📄</div>
                            <h5 class="fw-bold">Mis informes</h5>
                            <p class="text-muted small">Descargar tus informes médicos en PDF.</p>
                            <a href="/Informe" class="btn btn-outline-danger mt-auto">Ver informes</a>
                        </div>
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

    let modalEditar   = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    let modalEliminar = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    let userIdEliminar = null;

    // Abrir editar
    $(document).on('click', '.btnEditarUsuario', function () {
        let btn = $(this);
        $('#edit_user_id').val(btn.data('id'));
        $('#edit_name').val(btn.data('name'));
        $('#edit_apellidos').val(btn.data('apellidos'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_telefono').val(btn.data('telefono'));
        $('#tituloEditarUsuario').text('Editar ' + btn.data('tipo'));

        let modalActual = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
        if (modalActual) {
            modalActual.hide();
            setTimeout(() => modalEditar.show(), 400);
        } else {
            modalEditar.show();
        }
    });

    // Guardar edición
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
            success: function () { modalEditar.hide(); location.reload(); },
            error: function (xhr) { alert(xhr.responseJSON?.message ?? 'Error al guardar'); }
        });
    });

    // Abrir eliminar
    $(document).on('click', '.btnEliminarUsuario', function () {
        userIdEliminar = $(this).data('id');
        $('#nombreEliminar').text($(this).data('name'));

        let modalActual = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
        if (modalActual) {
            modalActual.hide();
            setTimeout(() => modalEliminar.show(), 400);
        } else {
            modalEliminar.show();
        }
    });

    // Confirmar eliminar
    $('#btnConfirmarEliminar').on('click', function () {
        $.ajax({
            url: '/usuario/' + userIdEliminar,
            method: 'POST',
            data: {
                _token:  $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE',
            },
            success: function () { modalEliminar.hide(); location.reload(); },
            error: function (xhr) { alert(xhr.responseJSON?.message ?? 'Error al eliminar'); }
        });
    });

});
</script>
@endif
@endsection