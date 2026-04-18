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
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">👨‍⚕️ Médico</label>
                            <select id="filtroMedico" class="form-select">
                                <option value="">Todos los médicos</option>
                                <optgroup label="Activos">
                                    @foreach($todosMedicos->where('activo', 1) as $medico)
                                        <option value="{{ $medico->id }}">
                                            {{ $medico->name }} {{ $medico->Apellidos }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Inactivos">
                                    @foreach($todosMedicos->where('activo', 0) as $medico)
                                        <option value="{{ $medico->id }}">
                                            ⚫ {{ $medico->name }} {{ $medico->Apellidos }} (inactivo)
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">🧑 Paciente</label>
                            <select id="filtroPaciente" class="form-select" disabled>
                                <option value="">Todos los pacientes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-circle-fill me-1 text-success" style="font-size:0.6rem;"></i>
                                Estado del médico
                            </label>
                            <select id="filtroEstadoMedico" class="form-select">
                                <option value="">Todos</option>
                                <option value="1">Solo activos</option>
                                <option value="0">Solo inactivos</option>
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
                            <th>Código</th>
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
                                    data-paciente-texto="{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}"
                                    data-activo-medico="{{ $cita->medico->activo }}">
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        {{ $cita->codigo_cita ?? 'CIT-' . $cita->id }}
                                    </span>
                                </td>
                                <td>
                                    {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}
                                    @if(session('admin') === 1 && !$cita->medico->activo)
                                        <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">inactivo</span>
                                    @endif
                                </td>
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
                                        @if(session('admin') === 1)
                                            <a href="#" data-id="{{ $cita->id }}" class="btn btn-primary btn-sm editar">
                                                Editar <i class="bi bi-eye ms-1"></i>
                                            </a>
                                        @endif

                                        @if(session('admin') === 1)
                                            <a href="#" data-id="{{ $cita->id }}" class="btn btn-danger btn-sm eliminar">
                                                Eliminar <i class="bi bi-x-lg ms-1"></i>
                                            </a>
                                        @endif

                                        @if(!$cita->enfermedad || !$cita->tratamiento)
                                            <a href="{{ route('informe.create', $cita->id) }}" class="btn btn-info btn-sm">
                                                Informe <i class="bi bi-file-earmark-plus ms-1"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('informe.edit', $cita->id) }}" class="btn btn-warning btn-sm">
                                                Editar informe <i class="bi bi-pencil ms-1"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- ── PAGINACIÓN ── --}}
                @if($Citas instanceof \Illuminate\Pagination\LengthAwarePaginator && $Citas->hasPages())
                    <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Mostrando {{ $Citas->firstItem() }}–{{ $Citas->lastItem() }}
                            de {{ $Citas->total() }} citas
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                {{-- Anterior --}}
                                <li class="page-item {{ $Citas->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link rounded-start" href="{{ $Citas->previousPageUrl() }}">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- Números de página --}}
                                @foreach($Citas->getUrlRange(1, $Citas->lastPage()) as $page => $url)
                                    <li class="page-item {{ $Citas->currentPage() === $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                {{-- Siguiente --}}
                                <li class="page-item {{ !$Citas->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link rounded-end" href="{{ $Citas->nextPageUrl() }}">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @endif


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
                        <input type="hidden" id="editar_medico_id">

                        <div class="mb-2">
                            <label class="form-label">Médico</label>
                            <input id="medico" class="form-control" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Paciente</label>
                            <input id="paciente" class="form-control" disabled>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Fecha</label>
                            <input id="editar_fecha" type="date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Hora de atención</label>
                            <select id="editar_hora" class="form-select" disabled>
                                <option value="">Seleccione una fecha primero</option>
                            </select>
                            <input type="hidden" id="fecha" name="Fecha_y_hora">
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
    let modalEditar;
    let modalCrear;

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
            error: function () {
                mostrarToast('Error al eliminar la cita', 'danger');
            }
        });
    });

    // ─── EDITAR ──────────────────────────────────────────────────────────
    $('.editar').on('click', function () {
        let citaId = $(this).data('id');

        $.ajax({
            url: '/citas/' + citaId + '/edit',
            type: 'GET',
            success: function (cita) {
                $('#cita_id').val(cita.id);
                $('#editar_medico_id').val(cita.medico_id);
                $('#medico').val(cita.medico.name + ' ' + cita.medico.Apellidos);
                $('#paciente').val(cita.paciente.name + ' ' + cita.paciente.Apellidos);
                $('#estado').val(cita.estado);

                console.log('Fecha_y_hora recibida:', cita.Fecha_y_hora);

                const fechaHora = cita.Fecha_y_hora ? cita.Fecha_y_hora.replace('T', ' ') : '';
                const partes    = fechaHora.split(' ');
                const fechaSola = partes[0] ?? '';
                const horaSola  = partes[1] ? partes[1].substring(0, 5) : '00:00';

                $('#editar_fecha').val(fechaSola);
                $('#editar_hora').html('<option value="">Cargando...</option>').prop('disabled', true);

                modalEditar = new bootstrap.Modal(document.getElementById('exampledit'));
                modalEditar.show();

                fetch(`/citas/horas-disponibles?medico_id=${cita.medico_id}&fecha=${fechaSola}&excluir_cita=${cita.id}`)
                    .then(r => r.json())
                    .then(horas => {
                        $('#editar_hora').html('<option value="">Seleccione una hora</option>');

                        const horasConActual = horas.includes(horaSola)
                            ? horas
                            : [horaSola, ...horas];

                        horasConActual.forEach(h => {
                            const selected = h === horaSola ? 'selected' : '';
                            $('#editar_hora').append(`<option value="${h}" ${selected}>${h}</option>`);
                        });

                        $('#editar_hora').prop('disabled', false);
                    })
                    .catch(() => {
                        $('#editar_hora').html('<option value="">Error al cargar horas</option>');
                        $('#editar_hora').prop('disabled', false);
                    });
            }
        });
    });

    // ─── RECARGAR HORAS AL CAMBIAR FECHA EN EDITAR ───────────────────────
    $(document).on('change', '#editar_fecha', function () {
        const citaId   = $('#cita_id').val();
        const medicoId = $('#editar_medico_id').val();
        const fecha    = $(this).val();

        if (!medicoId || !fecha) return;

        $('#editar_hora').html('<option value="">Cargando...</option>').prop('disabled', true);

        fetch(`/citas/horas-disponibles?medico_id=${medicoId}&fecha=${fecha}&excluir_cita=${citaId}`)
            .then(r => r.json())
            .then(horas => {
                $('#editar_hora').html('<option value="">Seleccione una hora</option>');
                horas.forEach(h => {
                    $('#editar_hora').append(`<option value="${h}">${h}</option>`);
                });
                $('#editar_hora').prop('disabled', false);
            })
            .catch(() => {
                $('#editar_hora').html('<option value="">Error al cargar horas</option>');
                $('#editar_hora').prop('disabled', false);
            });
    });

    // ─── GUARDAR EDICIÓN ─────────────────────────────────────────────────
    $('#formEditarCita').on('submit', function (e) {
        e.preventDefault();
        let citaId = $('#cita_id').val();

        const fechaVal = $('#editar_fecha').val();
        const horaVal  = $('#editar_hora').val();
        $('#fecha').val(fechaVal && horaVal ? `${fechaVal} ${horaVal}` : '');

        $.ajax({
            url: '/citas/' + citaId,
            method: 'POST',
            data: {
                _token:       $('meta[name="csrf-token"]').attr('content'),
                _method:      'PUT',
                Fecha_y_hora: $('#fecha').val(),
                estado:       $('#estado').val()
            },
            success: function () {
                mostrarToast('Cita actualizada correctamente', 'success');
                agregarNotificacion('Cita actualizada', 'info');
                setTimeout(() => { modalEditar.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar', 'danger');
            }
        });
    });

    // ─── CREAR CITA ──────────────────────────────────────────────────────
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
            error: function () {
                alert('Error al cancelar la cita');
            }
        });
    });

    // ─── FILTROS (solo Admin) ────────────────────────────────────────────
    // Mapa de medicoId → activo (1 o 0) para filtrar por estado
    const estadoMedicos = {
        @foreach($todosMedicos as $m)
        {{ $m->id }}: {{ $m->activo }},
        @endforeach
    };
 
    function aplicarFiltrosCitas() {
        const medicoSel  = $('#filtroMedico').val();
        const pacienteSel = $('#filtroPaciente').val();
        const estadoSel  = $('#filtroEstadoMedico').val();
 
        $('tbody tr').each(function () {
            const medicoFila   = $(this).data('medico')?.toString();
            const pacienteFila = $(this).data('paciente')?.toString();
            const activoMedico = estadoMedicos[medicoFila] !== undefined
                ? estadoMedicos[medicoFila].toString()
                : '1';
 
            const ok =
                (!medicoSel  || medicoFila   === medicoSel) &&
                (!pacienteSel || pacienteFila === pacienteSel) &&
                (!estadoSel  || activoMedico  === estadoSel);
 
            $(this).toggle(ok);
        });
    }
 
    $('#filtroMedico').on('change', function () {
        let medicoSeleccionado = $(this).val();
        let pacientes = new Map();
 
        $('tbody tr').each(function () {
            let medicoFila    = $(this).data('medico').toString();
            let pacienteFila  = $(this).data('paciente').toString();
            let textoPaciente = $(this).data('paciente-texto');
 
            if (!medicoSeleccionado || medicoFila === medicoSeleccionado) {
                pacientes.set(pacienteFila, textoPaciente);
            }
        });
 
        let selectPaciente = $('#filtroPaciente');
        selectPaciente.empty().append('<option value="">Todos los pacientes</option>');
        pacientes.forEach((nombre, id) => {
            selectPaciente.append(`<option value="${id}">${nombre}</option>`);
        });
        selectPaciente.prop('disabled', pacientes.size === 0).val('');
 
        aplicarFiltrosCitas();
    });
 
    $('#filtroPaciente').on('change', aplicarFiltrosCitas);
    $('#filtroEstadoMedico').on('change', aplicarFiltrosCitas);
 
    $('#btnLimpiarFiltros').on('click', function () {
        $('#filtroMedico').val('');
        $('#filtroPaciente').val('').prop('disabled', true);
        $('#filtroEstadoMedico').val('');
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
            id:          option.val(),
            fecha:       option.data('fecha'),
            medico:      option.data('medico'),
            paciente:    option.data('paciente'),
            enfermedad:  option.data('enfermedad'),
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
                _token:   $('meta[name="csrf-token"]').attr('content'),
                cita_id:  citaId,
                correo:   correo
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