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
                        <div class="col-md-3">
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
                            <select id="filtroPaciente" class="form-select">
                                <option value="">Todos los pacientes</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">
                                        {{ $paciente->name }} {{ $paciente->Apellidos }}
                                    </option>
                                @endforeach
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
                        <div class="col-md-3 d-grid">
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
                            <th>Médico</th>
                            <th>Paciente</th>
                            <th>Prestación</th>
                            <th>Fecha</th>
                            <th>Estado</th>
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
                                    @if(session('admin') === 1 || session('cargo') === 'Medico')
                                        <a href="{{ route('historial.index', $cita->paciente->id) }}">
                                            {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                        </a>
                                    @else
                                        {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                    @endif
                                </td>
                                <td>
                                    @if($cita->prestacion)
                                        <span class="badge bg-info text-dark">{{ $cita->prestacion->nombre }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
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
                                @elseif(session('cargo') === 'Paciente')
                                    <td>
                                        @if(in_array($cita->estado, ['Pendiente', 'Programada']))
                                            <button class="btn btn-danger btn-sm btnCancelarPaciente"
                                                    data-id="{{ $cita->id }}">
                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                            </button>
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
                                <li class="page-item {{ $Citas->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link rounded-start" href="{{ $Citas->previousPageUrl() }}">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                @foreach($Citas->getUrlRange(1, $Citas->lastPage()) as $page => $url)
                                    <li class="page-item {{ $Citas->currentPage() === $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach
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
                    @if(session('admin') === 1 || session('cargo') === 'Medico')
                        <button class="btn btn-outline-primary" id="btnEmailGlobal">
                            📧 Enviar correo
                        </button>
                    @endif
                    @if(session('admin') === 1)
                        <button class="btn btn-outline-success" id="btnWhatsappGlobal">
                            💬 WhatsApp
                        </button>
                    @endif
                    <button class="btn btn-outline-danger" id="btnPdfGlobal">
                        📄 Descargar PDF
                    </button>
                </div>

                @if(session('cargo') === 'Paciente')
                    <div class="text-end mt-4">
                        <button class="btn btn-success btn-lg" id="btnAgregarCita">
                            ➕ Solicitar hora
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


    {{-- ===== MODAL EDITAR (solo Admin) ===== --}}
    @if(session('admin') === 1)
    <div class="modal fade" id="exampledit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0d3b6e;">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Editar cita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCita">
                        @csrf
                        <input type="hidden" id="cita_id" name="id">
                        <input type="hidden" id="editar_medico_id">
                        <input type="hidden" id="editar_prestacion_id">

                        {{-- Médico (solo lectura) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-person-badge me-1"></i> Médico
                            </label>
                            <input id="medico" class="form-control" disabled
                                   style="background:#f8f9fa; font-weight:600;">
                        </div>

                        {{-- Paciente (solo lectura) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-person me-1"></i> Paciente
                            </label>
                            <input id="paciente" class="form-control" disabled
                                   style="background:#f8f9fa;">
                        </div>

                        {{-- Prestación (solo lectura) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-clipboard2-pulse me-1"></i> Prestación
                            </label>
                            <input id="prestacion_nombre" class="form-control" disabled
                                   style="background:#f8f9fa;">
                        </div>

                        {{-- Fecha --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-calendar me-1"></i> Fecha
                            </label>
                            <input id="editar_fecha" type="date" class="form-control" required>
                            <div id="diasDisponiblesEditar" class="form-text text-muted"></div>
                        </div>

                        {{-- Hora disponible según prestación --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-clock me-1"></i> Hora de atención
                            </label>
                            <select id="editar_hora" class="form-select" disabled>
                                <option value="">Seleccione una fecha primero</option>
                            </select>
                            <input type="hidden" id="fecha" name="Fecha_y_hora">
                            <div id="infoHorasEditar" class="form-text text-muted d-none">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="textoInfoHoras"></span>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-flag me-1"></i> Estado
                            </label>
                            <select id="estado" name="estado" class="form-select">
                                <option value="Pendiente">⏳ Pendiente</option>
                                <option value="Programada">📅 Programada</option>
                                <option value="Finalizada">✅ Finalizada</option>
                                <option value="Cancelada">❌ Cancelada</option>
                            </select>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                    data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- ===== MODAL CREAR CITA (solo Paciente) ===== --}}
    @if(session('cargo') === 'Paciente')
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0d3b6e;">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="bi bi-calendar-plus me-2"></i> Solicitar hora médica
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearCita">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1"></i> Médico
                            </label>
                            <select name="medico_id" id="medico_id" class="form-select" required>
                                <option value="" disabled selected>Seleccione un médico</option>
                                @foreach($medicos as $m)
                                    <option value="{{ $m->id }}">
                                        {{ $m->name }} {{ $m->Apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-clipboard2-pulse me-1"></i> Tipo de atención
                            </label>
                            <select name="prestacion_id" id="prestacion_id" class="form-select" required disabled>
                                <option value="">Seleccione un médico primero</option>
                            </select>
                        </div>

                        <input type="hidden" name="paciente_id" value="{{ session('user_id') }}">
                        <input type="hidden" name="estado" value="Pendiente">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar me-1"></i> Fecha
                            </label>
                            <input type="date" id="fecha_cita" class="form-control" required disabled>
                            <small class="text-muted" id="diasDisponibles"></small>
                            <input type="hidden" name="Fecha_y_hora" id="Fecha_y_hora">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-clock me-1"></i> Hora disponible
                            </label>
                            <select id="hora_atencion" class="form-select" disabled>
                                <option value="">Seleccione prestación y fecha primero</option>
                            </select>
                        </div>

                        <div id="infoCupos" class="alert alert-info small d-none">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="textoCupos"></span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success" id="btnConfirmarCita" disabled>
                            <i class="bi bi-check-circle me-1"></i> Confirmar hora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


    {{-- ===== MODAL CONFIRMAR CANCELACIÓN (Paciente) ===== --}}
    @if(session('cargo') === 'Paciente')
    <div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar cancelación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas <strong>cancelar</strong> esta cita?
                    Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">No, mantener</button>
                    <button class="btn btn-danger" id="btnConfirmarCancelacion">Sí, cancelar cita</button>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- ===== MODAL WHATSAPP ===== --}}
    @if(session('admin') === 1)
    <div class="modal fade" id="modalWhatsapp" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Enviar informe por WhatsApp</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
    @endif


    {{-- ===== MODAL PDF ===== --}}
    <div class="modal fade" id="modalPdf" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Descargar informe PDF</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
    @if(session('admin') === 1 || session('cargo') === 'Medico')
    <div class="modal fade" id="modalEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Enviar informe por correo</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
    @endif


    {{-- ===== MODAL INSTRUCCIONES ===== --}}
    <div class="modal fade" id="modalInstrucciones" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">📘 Instrucciones del sistema</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(session('admin') === 1)
                        <h6 class="fw-bold text-primary">👑 Administrador</h6>
                        <ul>
                            <li>Gestionar médicos y pacientes</li>
                            <li>Editar y eliminar citas existentes</li>
                            <li>Asignar especialidades a médicos</li>
                            <li>Enviar informes por correo, WhatsApp o PDF</li>
                            <li>Ver y filtrar todas las citas del sistema</li>
                        </ul>
                    @elseif(session('cargo') === 'Medico')
                        <h6 class="fw-bold text-success">👨‍⚕️ Médico</h6>
                        <ul>
                            <li>Ver sus citas asignadas</li>
                            <li>Crear informes médicos para sus pacientes</li>
                            <li>Ver el historial médico de sus pacientes</li>
                            <li>Enviar informes por correo o WhatsApp</li>
                        </ul>
                    @elseif(session('cargo') === 'Paciente')
                        <h6 class="fw-bold text-warning">🧑 Paciente</h6>
                        <ul>
                            <li>Ver sus propias citas médicas</li>
                            <li><strong>Solicitar nuevas horas médicas online</strong></li>
                            <li><strong>Cancelar sus citas pendientes o programadas</strong></li>
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
    let modalEditar;
    let modalCrear;
    let modalConfirmarCancelacion;

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ─── SOLO ADMIN: Eliminar ────────────────────────────────────────────────
    $('.eliminar').on('click', function () {
        let citaId = $(this).data('id');
        if (!confirm('¿Seguro que deseas eliminar esta cita?')) return;

        $.ajax({
            url: '/citas/' + citaId,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
            success: function () {
                mostrarToast('Cita eliminada correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function () {
                mostrarToast('Error al eliminar la cita', 'danger');
            }
        });
    });

    // ─── SOLO ADMIN: Editar ──────────────────────────────────────────────────
    $('.editar').on('click', function () {
        let citaId = $(this).data('id');

        $.ajax({
            url: '/citas/' + citaId + '/edit',
            type: 'GET',
            success: function (cita) {
                // Datos básicos
                $('#cita_id').val(cita.id);
                $('#editar_medico_id').val(cita.medico_id);
                $('#editar_prestacion_id').val(cita.prestacion_id);
                $('#medico').val(cita.medico.name + ' ' + cita.medico.Apellidos);
                $('#paciente').val(cita.paciente.name + ' ' + cita.paciente.Apellidos);

                // Nombre de la prestación
                if (cita.prestacion) {
                    $('#prestacion_nombre').val(cita.prestacion.nombre);
                } else {
                    $('#prestacion_nombre').val('Sin prestación asignada');
                }

                $('#estado').val(cita.estado);

                // Fecha y hora actuales
                const fechaHora = cita.Fecha_y_hora ? cita.Fecha_y_hora.replace('T', ' ') : '';
                const partes    = fechaHora.split(' ');
                const fechaSola = partes[0] ?? '';
                const horaSola  = partes[1] ? partes[1].substring(0, 5) : '00:00';

                $('#editar_fecha').val(fechaSola);
                $('#editar_hora').html('<option value="">Cargando horas...</option>').prop('disabled', true);
                $('#infoHorasEditar').addClass('d-none');

                modalEditar = new bootstrap.Modal(document.getElementById('exampledit'));
                modalEditar.show();

                // Cargar horas disponibles según prestación
                cargarHorasEditar(cita.medico_id, cita.prestacion_id, fechaSola, horaSola);
            }
        });
    });

    // ─── Función para cargar horas en el modal editar ────────────────────────
    function cargarHorasEditar(medicoId, prestacionId, fecha, horaActual) {
        if (!medicoId || !fecha) {
            $('#editar_hora').html('<option value="">Sin fecha seleccionada</option>').prop('disabled', true);
            return;
        }

        let url = `/citas/horas-disponibles?medico_id=${medicoId}&fecha=${fecha}`;
        if (prestacionId) {
            url += `&prestacion_id=${prestacionId}`;
        }

        fetch(url)
            .then(r => r.json())
            .then(horas => {
                $('#editar_hora').html('<option value="">Seleccione una hora</option>');

                // Incluir la hora actual si no está en las disponibles
                const horasConActual = (horas.includes(horaActual) || !horaActual)
                    ? horas
                    : [horaActual, ...horas];

                if (horasConActual.length === 0) {
                    $('#editar_hora').html('<option value="">No hay horas disponibles</option>');
                    $('#infoHorasEditar').removeClass('d-none');
                    $('#textoInfoHoras').text('No hay slots disponibles para esta fecha.');
                    return;
                }

                horasConActual.forEach(h => {
                    const selected = h === horaActual ? 'selected' : '';
                    const esActual = h === horaActual ? ' (actual)' : '';
                    $('#editar_hora').append(
                        `<option value="${h}" ${selected}>${h}${esActual}</option>`
                    );
                });

                $('#editar_hora').prop('disabled', false);

                if (horas.length > 0) {
                    $('#infoHorasEditar').removeClass('d-none');
                    $('#textoInfoHoras').text(`${horas.length} hora(s) disponible(s) según la prestación.`);
                }
            })
            .catch(() => {
                $('#editar_hora').html('<option value="">Error al cargar horas</option>');
            });
    }

    // ─── SOLO ADMIN: Recargar horas al cambiar fecha en editar ───────────────
    $(document).on('change', '#editar_fecha', function () {
        const medicoId     = $('#editar_medico_id').val();
        const prestacionId = $('#editar_prestacion_id').val();
        const fecha        = $(this).val();
        if (!medicoId || !fecha) return;

        $('#editar_hora').html('<option value="">Cargando...</option>').prop('disabled', true);
        cargarHorasEditar(medicoId, prestacionId, fecha, null);
    });

    // ─── SOLO ADMIN: Guardar edición ─────────────────────────────────────────
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
                setTimeout(() => { modalEditar.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar', 'danger');
            }
        });
    });

    // ─── SOLO PACIENTE: Flujo para solicitar hora ────────────────────────────
    @if(session('cargo') === 'Paciente')

    let prestacionesMedico = {};

    $('#btnAgregarCita').on('click', function () {
        modalCrear = new bootstrap.Modal(document.getElementById('modalCrear'));
        $('#medico_id').val('');
        $('#prestacion_id').html('<option value="">Seleccione un médico primero</option>').prop('disabled', true);
        $('#fecha_cita').val('').prop('disabled', true);
        $('#hora_atencion').html('<option value="">Seleccione prestación y fecha primero</option>').prop('disabled', true);
        $('#Fecha_y_hora').val('');
        $('#infoCupos').addClass('d-none');
        $('#btnConfirmarCita').prop('disabled', true);
        $('#diasDisponibles').text('');
        modalCrear.show();
    });

    $('#medico_id').on('change', function () {
        const medicoId = $(this).val();
        if (!medicoId) return;

        $('#prestacion_id').html('<option value="">Cargando...</option>').prop('disabled', true);
        $('#fecha_cita').val('').prop('disabled', true);
        $('#hora_atencion').html('<option value="">...</option>').prop('disabled', true);
        $('#btnConfirmarCita').prop('disabled', true);
        $('#diasDisponibles').text('');

        const medicoData = @json($medicos->load('medicoPrestaciones.prestacion'));
        const medico = medicoData.find(m => m.id == medicoId);

        if (!medico || !medico.medico_prestaciones || medico.medico_prestaciones.length === 0) {
            $('#prestacion_id').html('<option value="">Este médico no tiene prestaciones asignadas</option>');
            return;
        }

        prestacionesMedico[medicoId] = medico.medico_prestaciones;

        let opciones = '<option value="" disabled selected>Seleccione una atención</option>';
        medico.medico_prestaciones.forEach(mp => {
            opciones += `<option value="${mp.id_prestacion}"
                data-mp-id="${mp.id}"
                data-hora-entrada="${mp.hora_entrada}"
                data-hora-salida="${mp.hora_salida}"
                data-hora-atencion="${mp.hora_atencion}"
                data-cant-online="${mp.cant_online}">
                ${mp.prestacion.nombre}
                (${mp.hora_entrada.substring(0,5)} - ${mp.hora_salida.substring(0,5)}, ${mp.hora_atencion} min/pac.)
            </option>`;
        });

        $('#prestacion_id').html(opciones).prop('disabled', false);
    });

    $('#prestacion_id').on('change', function () {
        const medicoId     = $('#medico_id').val();
        const prestacionId = $(this).val();
        if (!medicoId || !prestacionId) return;

        $('#fecha_cita').val('').prop('disabled', false);
        $('#hora_atencion').html('<option value="">Seleccione una fecha</option>').prop('disabled', true);
        $('#btnConfirmarCita').prop('disabled', true);

        const $opt = $(this).find(':selected');
        const cantOnline = $opt.data('cant-online');
        $('#infoCupos').removeClass('d-none');
        $('#textoCupos').text(`Cupos online disponibles por horario: ${cantOnline}`);

        const medicoData = @json($medicos);
        const medico = medicoData.find(m => m.id == medicoId);
        if (medico && medico.horario && medico.horario.dias_semana) {
            const dias = medico.horario.dias_semana.map(d =>
                d.charAt(0).toUpperCase() + d.slice(1)
            ).join(', ');
            $('#diasDisponibles').text(`Días de atención: ${dias}`);
        }
    });

    $('#fecha_cita').on('change', function () {
        const medicoId     = $('#medico_id').val();
        const prestacionId = $('#prestacion_id').val();
        const fecha        = $(this).val();

        if (!medicoId || !prestacionId || !fecha) return;

        $('#hora_atencion').html('<option value="">Cargando horas...</option>').prop('disabled', true);
        $('#btnConfirmarCita').prop('disabled', true);

        fetch(`/citas/horas-disponibles?medico_id=${medicoId}&prestacion_id=${prestacionId}&fecha=${fecha}`)
            .then(r => r.json())
            .then(horas => {
                if (horas.length === 0) {
                    $('#hora_atencion').html('<option value="">No hay horas disponibles para esta fecha</option>');
                    return;
                }
                $('#hora_atencion').html('<option value="" disabled selected>Seleccione una hora</option>');
                horas.forEach(hora => {
                    $('#hora_atencion').append(`<option value="${hora}">${hora}</option>`);
                });
                $('#hora_atencion').prop('disabled', false);
            })
            .catch(() => {
                $('#hora_atencion').html('<option value="">Error al cargar horas</option>');
            });
    });

    $('#hora_atencion').on('change', function () {
        const hora  = $(this).val();
        const fecha = $('#fecha_cita').val();
        if (hora && fecha) {
            $('#Fecha_y_hora').val(`${fecha} ${hora}`);
            $('#btnConfirmarCita').prop('disabled', false);
        } else {
            $('#btnConfirmarCita').prop('disabled', true);
        }
    });

    $('#formCrearCita').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#btnConfirmarCita');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url: '/citas',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                mostrarToast('¡Hora solicitada correctamente!', 'success');
                setTimeout(() => { modalCrear.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al solicitar la hora', 'danger');
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Confirmar hora');
            }
        });
    });

    $(document).on('click', '.btnCancelarPaciente', function () {
        citaCancelarId = $(this).data('id');
        modalConfirmarCancelacion = new bootstrap.Modal(
            document.getElementById('modalConfirmarCancelacion')
        );
        modalConfirmarCancelacion.show();
    });

    $('#btnConfirmarCancelacion').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Cancelando...');

        $.ajax({
            url: '/citas/' + citaCancelarId + '/cancelar',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function () {
                mostrarToast('Cita cancelada correctamente', 'warning');
                setTimeout(() => { modalConfirmarCancelacion.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al cancelar la cita', 'danger');
                $btn.prop('disabled', false).html('Sí, cancelar cita');
            }
        });
    });

    @endif

    // ─── FILTROS (solo Admin) ────────────────────────────────────────────────
    @if(session('admin') === 1)
    const estadoMedicos = {
        @foreach($todosMedicos as $m)
        {{ $m->id }}: {{ $m->activo }},
        @endforeach
    };

    function aplicarFiltrosCitas() {
        const medicoSel   = $('#filtroMedico').val();
        const pacienteSel = $('#filtroPaciente').val();
        const estadoSel   = $('#filtroEstadoMedico').val();

        $('tbody tr').each(function () {
            const medicoFila   = $(this).data('medico')?.toString();
            const pacienteFila = $(this).data('paciente')?.toString();
            const activoMedico = estadoMedicos[medicoFila] !== undefined
                ? estadoMedicos[medicoFila].toString() : '1';

            const ok =
                (!medicoSel   || medicoFila   === medicoSel) &&
                (!pacienteSel || pacienteFila === pacienteSel) &&
                (!estadoSel   || activoMedico  === estadoSel);

            $(this).toggle(ok);
        });
    }

    // El filtro de médico ya NO controla al de paciente —
    // ambos funcionan de forma independiente
    $('#filtroMedico').on('change', aplicarFiltrosCitas);
    $('#filtroPaciente').on('change', aplicarFiltrosCitas);
    $('#filtroEstadoMedico').on('change', aplicarFiltrosCitas);

    $('#btnLimpiarFiltros').on('click', function () {
        $('#filtroMedico').val('');
        $('#filtroPaciente').val('');
        $('#filtroEstadoMedico').val('');
        $('tbody tr').show();
    });
    @endif

    // ─── WHATSAPP ────────────────────────────────────────────────────────────
    @if(session('admin') === 1)
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
    @endif

    // ─── PDF ─────────────────────────────────────────────────────────────────
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

    // ─── EMAIL ───────────────────────────────────────────────────────────────
    @if(session('admin') === 1 || session('cargo') === 'Medico')
    let modalEmail;
    $('#btnEmailGlobal').on('click', function () {
        modalEmail = new bootstrap.Modal(document.getElementById('modalEmail'));
        modalEmail.show();
    });
    $('#btnEnviarEmail').on('click', function () {
        let citaId = $('#selectCitaEmail').val();
        let correo = $('#correoEmail').val();
        if (!citaId || !correo) { alert('Seleccione una cita y escriba un correo'); return; }

        $.ajax({
            url: '/informe/email',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), cita_id: citaId, correo: correo },
            success: function () {
                mostrarToast('Correo enviado correctamente', 'success');
                modalEmail.hide();
            },
            error: function () {
                mostrarToast('Error al enviar el correo', 'danger');
            }
        });
    });
    @endif

});
</script>

@endsection