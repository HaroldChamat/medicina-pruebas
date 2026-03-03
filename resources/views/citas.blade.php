@extends('layouts.app')
@section('content')



    {{-- Filtros: solo Admin --}}
    @if(session('admin') === 1)
        <div class="container mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fs-5 me-2">🔍</span>
                        <h5 class="mb-0">Filtros de búsqueda</h5>
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">👨‍⚕️ Médico</label>
                            <select id="filtroMedico" class="form-select">
                                <option value="">Todos los médicos</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico->id }}">
                                        {{ $medico->name }} {{ $medico->Apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">🧑 Paciente</label>
                            <select id="filtroPaciente" class="form-select" disabled>
                                <option value="">Todos los pacientes</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-outline-secondary" id="btnLimpiarFiltros">
                                ❌ Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <table class="table table-hover align-middle table-borderless">
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>ID</th>
                            <th>Medico</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            {{-- Columna acciones: Admin y Médico --}}
                            @if(session('admin') === 1 || session('cargo') === 'Medico')
                                <th>Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Citas as $cita)
                            <tr data-medico="{{ $cita->medico->id }}"
                                data-paciente="{{ $cita->paciente->id }}"
                                data-paciente-texto="{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}">
                                <td>{{ $cita->id }}</td>
                                <td>{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</td>
                                <td>
                                    {{-- Solo Admin y Médico pueden ver el historial --}}
                                    @if(session('admin') === 1 || session('cargo') === 'Medico')
                                        <a href="{{ route('historial.index', $cita->paciente->id) }}">
                                            {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                        </a>
                                    @else
                                        {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($cita->estado === 'Pendiente')
                                        <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                                    @elseif($cita->estado === 'Programada')
                                        <span class="badge bg-primary">📅 Programada</span>
                                    @elseif($cita->estado === 'Finalizada')
                                        <span class="badge bg-success">✅ Finalizada</span>
                                    @elseif($cita->estado === 'Cancelada')
                                        <span class="badge bg-danger">❌ Cancelada</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $cita->estado }}</span>
                                    @endif
                                </td>

                                @if(session('admin') === 1 || session('cargo') === 'Medico')
                                    <td>
                                        <a href="#" data-id="{{ $cita->id }}" class="btn btn-primary btn-sm editar">
                                            Editar
                                        </a>

                                        {{-- Eliminar: solo Admin --}}
                                        @if(session('admin') === 1)
                                            <a href="#" data-id="{{ $cita->id }}" class="btn btn-danger btn-sm eliminar">
                                                Eliminar
                                            </a>
                                        @endif

                                        {{-- Informe: Admin y Médico --}}
                                        @if(!$cita->enfermedad || !$cita->tratamiento)
                                            <a href="{{ route('informe.create', $cita->id) }}" class="btn btn-info btn-sm">
                                                Informe
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Informe enviado
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Botones de acciones globales --}}
                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">

                    {{-- Email: Admin y Médico --}}
                    @if(session('admin') === 1 || session('cargo') === 'Medico')
                        <button class="btn btn-outline-primary" id="btnEmailGlobal">
                            📧 Enviar correo
                        </button>
                    @endif

                    {{-- WhatsApp: solo Admin --}}
                    @if(session('admin') === 1)
                        <button class="btn btn-outline-success" id="btnWhatsappGlobal">
                            💬 WhatsApp
                        </button>
                    @endif

                    {{-- PDF: todos los roles --}}
                    <button class="btn btn-outline-danger" id="btnPdfGlobal">
                        📄 Descargar PDF
                    </button>

                </div>

                {{-- Nueva cita: Admin y Paciente (no Médico) --}}
                @if(session('admin') === 1 || session('cargo') === 'Paciente')
                    <div class="text-end mt-4">
                        <button class="btn btn-success btn-lg" id="btnAgregarCita">
                            ➕ Nueva cita
                        </button>
                    </div>
                @endif

                <button type="button" class="btn btn-outline-info btn-sm mt-3"
                        data-bs-toggle="modal" data-bs-target="#modalInstrucciones">
                    📘 Instrucciones
                </button>

            </div>
        </div>
    </div>


    {{-- ===== MODAL EDITAR ===== --}}
    <div class="modal fade" id="exampledit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCita">
                        @csrf
                        <input type="hidden" id="cita_id" name="id">

                        <div class="mb-2">
                            <label class="form-label">Médico</label>
                            <input id="medico" class="form-control" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Paciente</label>
                            <input id="paciente" class="form-control" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Fecha y hora</label>
                            <input id="fecha" name="Fecha_y_hora" type="datetime-local" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Estado</label>
                            <select id="estado" name="estado" class="form-select">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Programada">Programada</option>
                                <option value="Finalizada">Finalizada</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>

                        <div class="modal-footer px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- ===== MODAL CREAR CITA ===== --}}
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearCita">
                    @csrf
                    <div class="modal-body">

                        {{-- Médico --}}
                        <div class="mb-3">
                            <label class="form-label">Médico</label>
                            <select name="medico_id" id="medico_id" class="form-select" required>
                                <option value="" disabled selected>Seleccione un médico</option>
                                @foreach($medicos as $m)
                                    <option value="{{ $m->id }}">
                                        {{ $m->name }} {{ $m->Apellidos }}
                                        @if($m->especialidad) — {{ $m->especialidad->Nombre_especialidad }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Paciente: Admin ve select, Paciente ve su nombre fijo --}}
                        <div class="mb-3">
                            <label class="form-label">Paciente</label>
                            @if(session('admin') === 1)
                                <select name="paciente_id" class="form-select" required>
                                    <option value="" disabled selected>Seleccione un paciente</option>
                                    @foreach($pacientes as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->name }} {{ $p->Apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{-- Paciente solo puede crear cita para sí mismo --}}
                                <input type="hidden" name="paciente_id" value="{{ session('user_id') }}">
                                @php
                                    $pacienteActual = $pacientes->firstWhere('id', session('user_id'));
                                @endphp
                                <input type="text" class="form-control" disabled
                                       value="{{ $pacienteActual ? $pacienteActual->name . ' ' . $pacienteActual->Apellidos : 'Paciente' }}">
                            @endif
                        </div>

                        {{-- Fecha --}}
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" id="fecha_cita" class="form-control" required>
                            <input type="hidden" name="Fecha_y_hora" id="Fecha_y_hora">
                        </div>

                        {{-- Hora disponible (se carga por AJAX) --}}
                        <div class="mb-3">
                            <label class="form-label">Hora de atención</label>
                            <select id="hora_atencion" class="form-select" disabled>
                                <option value="">Seleccione médico y fecha primero</option>
                            </select>
                        </div>

                        {{-- Estado fijo en Pendiente --}}
                        <input type="hidden" name="estado" value="Pendiente">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ===== MODAL CONFIRMAR CANCELACIÓN ===== --}}
    <div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar cancelación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas <strong>cancelar y eliminar</strong> esta cita?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button class="btn btn-danger" id="btnConfirmarCancelacion">Sí, cancelar cita</button>
                </div>
            </div>
        </div>
    </div>


    {{-- ===== MODAL WHATSAPP ===== --}}
    <div class="modal fade" id="modalWhatsapp" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Enviar informe por WhatsApp</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="mb-1">Cita</label>
                    <select id="selectCitaWhatsapp" class="form-select mb-3">
                        <option value="" disabled selected>Seleccione una cita</option>
                        @foreach($Citas as $cita)
                            <option value="{{ $cita->id }}"
                                data-fecha="{{ $cita->Fecha_y_hora }}"
                                data-medico="{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}"
                                data-paciente="{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}"
                                data-enfermedad="{{ $cita->enfermedad->descripcion ?? '' }}"
                                data-tratamiento="{{ $cita->tratamiento->descripcion ?? '' }}">
                                #{{ $cita->id }} — {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                            </option>
                        @endforeach
                    </select>
                    <label class="mb-1">Número de teléfono</label>
                    <input type="text" id="telefonoWhatsapp" class="form-control"
                           placeholder="Ej: 56912345678">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" id="btnEnviarWhatsapp">Enviar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- ===== MODAL PDF ===== --}}
    <div class="modal fade" id="modalPdf" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Descargar informe PDF</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="mb-1">Cita</label>
                    <select id="selectCitaPdf" class="form-select">
                        <option value="" disabled selected>Seleccione una cita</option>
                        @foreach($Citas as $cita)
                            <option value="{{ $cita->id }}">
                                #{{ $cita->id }} — {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger" id="btnDescargarPdf">Descargar PDF</button>
                </div>
            </div>
        </div>
    </div>


    {{-- ===== MODAL EMAIL ===== --}}
    <div class="modal fade" id="modalEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Enviar informe por correo</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="mb-1">Cita</label>
                    <select id="selectCitaEmail" class="form-select mb-3">
                        <option value="" disabled selected>Seleccione una cita</option>
                        @foreach($Citas as $cita)
                            <option value="{{ $cita->id }}">
                                #{{ $cita->id }} — {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                            </option>
                        @endforeach
                    </select>
                    <label class="mb-1">Correo electrónico</label>
                    <input type="email" id="correoEmail" class="form-control"
                           placeholder="ejemplo@correo.com">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" id="btnEnviarEmail">Enviar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- ===== MODAL INSTRUCCIONES ===== --}}
    <div class="modal fade" id="modalInstrucciones" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">📘 Instrucciones del sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(session('admin') === 1)
                        <h6 class="fw-bold text-primary">👑 Administrador</h6>
                        <ul>
                            <li>Gestionar médicos y pacientes</li>
                            <li>Crear, editar y eliminar citas</li>
                            <li>Asignar especialidades a médicos</li>
                            <li>Enviar informes por correo, WhatsApp o PDF</li>
                            <li>Ver y filtrar todas las citas del sistema</li>
                        </ul>
                    @elseif(session('cargo') === 'Medico')
                        <h6 class="fw-bold text-success">👨‍⚕️ Médico</h6>
                        <ul>
                            <li>Ver sus citas asignadas</li>
                            <li>Editar el estado de una cita</li>
                            <li>Crear informes médicos</li>
                            <li>Consultar historial de pacientes</li>
                            <li>Enviar informes por correo o WhatsApp</li>
                        </ul>
                    @elseif(session('cargo') === 'Paciente')
                        <h6 class="fw-bold text-warning">🧑 Paciente</h6>
                        <ul>
                            <li>Ver sus propias citas médicas</li>
                            <li>Solicitar nuevas citas</li>
                            <li>Descargar sus informes médicos en PDF</li>
                        </ul>
                    @else
                        <p class="text-muted">No hay una sesión activa.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
@parent

<script>
$(document).ready(function () {

    let citaCancelarId = null;
    let modalConfirmar;

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ─── ELIMINAR ────────────────────────────────────────────────────────
    $('.eliminar').on('click', function () {
        let citaId = $(this).data('id');
        if (!confirm('¿Seguro que deseas eliminar esta cita?')) return;

        $.ajax({
            url: '/citas/' + citaId,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function () {
            mostrarToast('Cita eliminada correctamente', 'success');
            agregarNotificacion('Cita eliminada', 'danger');
            setTimeout(() => location.reload(), 1500);
        },
        error: function (xhr) {
            mostrarToast('Error al eliminar la cita', 'danger');
        }

        });
    });

    // ─── EDITAR ──────────────────────────────────────────────────────────
    let modalEditar;

    $('.editar').on('click', function () {
        let citaId = $(this).data('id');

        $.ajax({
            url: '/citas/' + citaId + '/edit',
            type: 'GET',
            success: function (cita) {
                $('#cita_id').val(cita.id);
                // ✅ corregido: el campo es 'name' no 'Nombre'
                $('#medico').val(cita.medico.name + ' ' + cita.medico.Apellidos);
                $('#paciente').val(cita.paciente.name + ' ' + cita.paciente.Apellidos);
                $('#fecha').val(cita.Fecha_y_hora.replace(' ', 'T'));
                $('#estado').val(cita.estado);

                modalEditar = new bootstrap.Modal(document.getElementById('exampledit'));
                modalEditar.show();
            }
        });
    });

    // ─── GUARDAR EDICIÓN ─────────────────────────────────────────────────
    $('#formEditarCita').on('submit', function (e) {
        e.preventDefault();
        let citaId = $('#cita_id').val();

        $.ajax({
            url: '/citas/' + citaId,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                Fecha_y_hora: $('#fecha').val(),
                estado: $('#estado').val()
            },
            success: function () {
            mostrarToast('Cita actualizada correctamente', 'success');
            agregarNotificacion('Cita #' + $('#cita_id').val() + ' actualizada', 'info');
            setTimeout(() => { modalEditar.hide(); location.reload(); }, 1500);
        },
        error: function (xhr) {
            mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar', 'danger');
        }
        });
    });

    // ─── CREAR CITA ──────────────────────────────────────────────────────
    let modalCrear;

    $('#btnAgregarCita').on('click', function () {
        modalCrear = new bootstrap.Modal(document.getElementById('modalCrear'));
        modalCrear.show();
    });

    $('#formCrearCita').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/citas',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
            mostrarToast('Cita creada correctamente', 'success');
            agregarNotificacion('Nueva cita agendada', 'success');
            setTimeout(() => { modalCrear.hide(); location.reload(); }, 1500);
        },
        error: function (xhr) {
            mostrarToast(xhr.responseJSON?.message ?? 'Error al crear la cita', 'danger');
        }
        });
    });

    // ─── CONFIRMAR CANCELACIÓN ───────────────────────────────────────────
    $('#estado').on('change', function () {
        if ($(this).val() === 'Cancelada') {
            citaCancelarId = $('#cita_id').val();
            modalConfirmar = new bootstrap.Modal(
                document.getElementById('modalConfirmarCancelacion')
            );
            modalConfirmar.show();
        }
    });

    $('#btnConfirmarCancelacion').on('click', function () {
        $.ajax({
            url: '/citas/' + citaCancelarId,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function () {
                modalConfirmar.hide();
                modalEditar.hide();
                location.reload();
            },
            error: function (xhr) {
                alert('Error al cancelar la cita');
            }
        });
    });

    // ─── FILTROS (solo Admin) ────────────────────────────────────────────
    $('#filtroMedico').on('change', function () {
        let medicoSeleccionado = $(this).val();
        let pacientes = new Map();

        $('tbody tr').each(function () {
            let medicoFila   = $(this).data('medico').toString();
            let pacienteFila = $(this).data('paciente').toString();
            let textoPaciente = $(this).data('paciente-texto');

            if (!medicoSeleccionado || medicoFila === medicoSeleccionado) {
                $(this).show();
                pacientes.set(pacienteFila, textoPaciente);
            } else {
                $(this).hide();
            }
        });

        let selectPaciente = $('#filtroPaciente');
        selectPaciente.empty().append('<option value="">Todos los pacientes</option>');
        pacientes.forEach((nombre, id) => {
            selectPaciente.append(`<option value="${id}">${nombre}</option>`);
        });
        selectPaciente.prop('disabled', pacientes.size === 0).val('');
    });

    $('#filtroPaciente').on('change', function () {
        let pacienteSeleccionado = $(this).val();
        let medicoSeleccionado   = $('#filtroMedico').val();

        $('tbody tr').each(function () {
            let medicoFila   = $(this).data('medico').toString();
            let pacienteFila = $(this).data('paciente').toString();

            let mostrar =
                (!medicoSeleccionado || medicoFila === medicoSeleccionado) &&
                (!pacienteSeleccionado || pacienteFila === pacienteSeleccionado);

            $(this).toggle(mostrar);
        });
    });

    $('#btnLimpiarFiltros').on('click', function () {
        $('#filtroMedico').val('');
        $('#filtroPaciente').val('').prop('disabled', true);
        $('tbody tr').show();
    });

    // ─── WHATSAPP ────────────────────────────────────────────────────────
    let modalWhatsapp;
    let dataWhatsapp = {};

    $('#btnWhatsappGlobal').on('click', function () {
        modalWhatsapp = new bootstrap.Modal(document.getElementById('modalWhatsapp'));
        modalWhatsapp.show();
    });

    $('#selectCitaWhatsapp').on('change', function () {
        let option = $(this).find(':selected');
        dataWhatsapp = {
            id: option.val(),
            fecha: option.data('fecha'),
            medico: option.data('medico'),
            paciente: option.data('paciente'),
            enfermedad: option.data('enfermedad'),
            tratamiento: option.data('tratamiento')
        };
    });

    $('#btnEnviarWhatsapp').on('click', function () {
        if (!dataWhatsapp.id) { alert('Seleccione una cita'); return; }

        let telefono = $('#telefonoWhatsapp').val().replace(/\D/g, '');
        if (!telefono) { alert('Ingrese un número válido'); return; }

        let mensaje =
`📋 *Informe Médico*
------------------------
🆔 Cita: ${dataWhatsapp.id}
📅 Fecha: ${dataWhatsapp.fecha}
👨‍⚕️ Médico: ${dataWhatsapp.medico}
🧑 Paciente: ${dataWhatsapp.paciente}

🦠 Enfermedad:
${dataWhatsapp.enfermedad || 'No registrada'}

💊 Tratamiento:
${dataWhatsapp.tratamiento || 'No registrado'}

⚠️ Documento informativo`;

        window.open(`https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`, '_blank');
        modalWhatsapp.hide();
        $('#telefonoWhatsapp').val('');
        $('#selectCitaWhatsapp').val('');
        dataWhatsapp = {};
    });

    // ─── PDF ─────────────────────────────────────────────────────────────
    let modalPdf;

    $('#btnPdfGlobal').on('click', function () {
        modalPdf = new bootstrap.Modal(document.getElementById('modalPdf'));
        modalPdf.show();
    });

    $('#btnDescargarPdf').on('click', function () {
        let citaId = $('#selectCitaPdf').val();
        if (!citaId) { alert('Seleccione una cita'); return; }
        window.open('/informe/pdf/' + citaId, '_blank');
        modalPdf.hide();
        $('#selectCitaPdf').val('');
    });

    // ─── EMAIL ───────────────────────────────────────────────────────────
    let modalEmail;

    $('#btnEmailGlobal').on('click', function () {
        modalEmail = new bootstrap.Modal(document.getElementById('modalEmail'));
        modalEmail.show();
    });

    $('#btnEnviarEmail').on('click', function () {
        let citaId = $('#selectCitaEmail').val();
        let correo = $('#correoEmail').val();

        if (!citaId || !correo) {
            alert('Seleccione una cita y escriba un correo');
            return;
        }

        $.ajax({
            url: '/informe/email',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                cita_id: citaId,
                correo: correo
            },
            success: function () {
            mostrarToast('Correo enviado correctamente', 'success');
            agregarNotificacion('Informe enviado por correo', 'info');
            modalEmail.hide();
        },
        error: function () {
            mostrarToast('Error al enviar el correo', 'danger');
        }
        });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const medico       = document.getElementById('medico_id');
    const fecha        = document.getElementById('fecha_cita');
    const horaSel      = document.getElementById('hora_atencion');
    const fechaHoraInput = document.getElementById('Fecha_y_hora');

    function cargarHoras() {
        horaSel.innerHTML = '<option value="">Cargando...</option>';
        horaSel.disabled  = true;

        if (!medico.value || !fecha.value) {
            horaSel.innerHTML = '<option value="">Seleccione médico y fecha</option>';
            return;
        }

        fetch(`/citas/horas-disponibles?medico_id=${medico.value}&fecha=${fecha.value}`)
            .then(r => r.json())
            .then(horas => {
                horaSel.innerHTML = '<option value="">Seleccione una hora</option>';

                if (horas.length === 0) {
                    horaSel.innerHTML = '<option value="">No hay horas disponibles</option>';
                    return;
                }

                horas.forEach(hora => {
                    const opt = document.createElement('option');
                    opt.value = hora;
                    opt.textContent = hora;
                    horaSel.appendChild(opt);
                });

                horaSel.disabled = false;
            })
            .catch(() => {
                horaSel.innerHTML = '<option value="">Error al cargar horas</option>';
            });
    }

    medico.addEventListener('change', cargarHoras);
    fecha.addEventListener('change', cargarHoras);

    horaSel.addEventListener('change', () => {
        fechaHoraInput.value = horaSel.value ? `${fecha.value} ${horaSel.value}` : '';
    });
});
</script>

@endsection