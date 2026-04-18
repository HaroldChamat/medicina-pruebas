@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-people-fill me-2"></i> Lista de Médicos
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">Gestión de médicos registrados en el sistema.</p>
        </div>
        <a href="/login" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Buscador y filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-search me-1"></i> Buscar
                    </label>
                    <input type="text" id="buscador" class="form-control"
                           placeholder="Nombre, apellido, RUT o email...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-funnel me-1"></i> Estado
                    </label>
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" selected>Solo activos</option>
                        <option value="0">Solo inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="btnLimpiarFiltros">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Contador --}}
    <p class="small mb-2" style="color:rgba(255,255,255,0.8);">
        Mostrando <strong id="contadorVisible">{{ $medicos->count() }}</strong> médico(s)
        — <span class="text-warning">{{ $medicos->where('activo', 0)->count() }} inactivo(s)</span>
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
                            <th>Estado</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>RUT</th>
                            <th>Teléfono</th>
                            <th>Especialidad</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        @foreach($medicos as $medico)
                            <tr data-nombre="{{ strtolower($medico->name . ' ' . $medico->Apellidos . ' ' . $medico->Rut . ' ' . $medico->email) }}"
                                data-activo="{{ $medico->activo }}"
                                class="{{ $medico->activo ? '' : 'table-secondary opacity-75' }}">
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>
                                    @if($medico->activo)
                                        <span class="badge bg-success rounded-pill">
                                            <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">
                                            <i class="bi bi-circle me-1" style="font-size:0.5rem;"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $medico->activo ? '' : 'text-muted' }}">
                                        {{ $medico->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $medico->activo ? '' : 'text-muted' }}">
                                        {{ $medico->Apellidos }}
                                    </span>
                                </td>
                                <td>{{ $medico->email }}</td>
                                <td><span class="badge bg-secondary">{{ $medico->Rut }}</span></td>
                                <td>{{ $medico->telefono }}</td>
                                <td>
                                    @if($medico->especialidades->count() > 0)
                                        @foreach($medico->especialidades as $esp)
                                            <span class="badge bg-light text-dark border">
                                                {{ $esp->Nombre_especialidad }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Sin especialidad</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm btnEditar"
                                        data-id="{{ $medico->id }}"
                                        data-name="{{ $medico->name }}"
                                        data-apellidos="{{ $medico->Apellidos }}"
                                        data-email="{{ $medico->email }}"
                                        data-telefono="{{ $medico->telefono }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    @if($medico->activo)
                                        <button class="btn btn-warning btn-sm btnDesactivar"
                                            data-id="{{ $medico->id }}"
                                            data-name="{{ $medico->name }} {{ $medico->Apellidos }}"
                                            title="Desactivar médico">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-success btn-sm btnActivar"
                                            data-id="{{ $medico->id }}"
                                            data-name="{{ $medico->name }} {{ $medico->Apellidos }}"
                                            title="Reactivar médico">
                                            <i class="bi bi-play-circle"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil me-2"></i> Editar Médico
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
                        <button type="button" class="btn btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmar Desactivar --}}
<div class="modal fade" id="modalDesactivar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pause-circle me-2"></i> Desactivar médico
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Desactivar a <strong id="nombreDesactivar"></strong>?</p>
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    El médico quedará inactivo: no aparecerá en selects de citas, horarios ni especialidades,
                    pero sus informes médicos anteriores se conservan con su nombre visible.
                    Puedes reactivarlo en cualquier momento.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-warning rounded-pill" id="btnConfirmarDesactivar">
                    <i class="bi bi-pause-circle me-1"></i> Sí, desactivar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmar Activar --}}
<div class="modal fade" id="modalActivar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-play-circle me-2"></i> Reactivar médico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Reactivar a <strong id="nombreActivar"></strong>?
                Volverá a aparecer en todas las secciones del sistema.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success rounded-pill" id="btnConfirmarActivar">
                    <i class="bi bi-play-circle me-1"></i> Sí, reactivar
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

    let userIdAccion = null;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // ── FILTROS ──────────────────────────────────────────────────────────
    function aplicarFiltros() {
        const texto  = $('#buscador').val().toLowerCase().trim();
        const estado = $('#filtroEstado').val();
        let visible  = 0;

        $('#tablaBody tr').each(function () {
            const nombre  = $(this).data('nombre') || '';
            const activo  = $(this).data('activo').toString();

            const coincideTexto  = !texto  || nombre.includes(texto);
            const coincideEstado = !estado || activo === estado;

            if (coincideTexto && coincideEstado) {
                $(this).show();
                visible++;
            } else {
                $(this).hide();
            }
        });

        $('#contadorVisible').text(visible);
    }

    $('#buscador').on('keyup', aplicarFiltros);
    $('#filtroEstado').on('change', aplicarFiltros);

    $('#btnLimpiarFiltros').on('click', function () {
        $('#buscador').val('');
        $('#filtroEstado').val('');
        aplicarFiltros();
    });

    // Aplicar filtro inicial (solo activos por defecto)
    aplicarFiltros();

    // ── EDITAR ───────────────────────────────────────────────────────────
    $(document).on('click', '.btnEditar', function () {
        const btn = $(this);
        $('#edit_id').val(btn.data('id'));
        $('#edit_name').val(btn.data('name'));
        $('#edit_apellidos').val(btn.data('apellidos'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_telefono').val(btn.data('telefono'));
        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });

    $('#formEditar').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/usuario/' + $('#edit_id').val(),
            method: 'POST',
            data: {
                _token:    csrfToken,
                _method:   'PUT',
                name:      $('#edit_name').val(),
                Apellidos: $('#edit_apellidos').val(),
                email:     $('#edit_email').val(),
                telefono:  $('#edit_telefono').val(),
            },
            success: function () {
                mostrarToast('Médico actualizado correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            }
        });
    });

    // ── DESACTIVAR ───────────────────────────────────────────────────────
    $(document).on('click', '.btnDesactivar', function () {
        userIdAccion = $(this).data('id');
        $('#nombreDesactivar').text($(this).data('name'));
        new bootstrap.Modal(document.getElementById('modalDesactivar')).show();
    });

    $('#btnConfirmarDesactivar').on('click', function () {
        $.ajax({
            url: '/usuario/' + userIdAccion + '/desactivar',
            method: 'POST',
            data: { _token: csrfToken },
            success: function () {
                mostrarToast('Médico desactivado correctamente', 'warning');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error', 'danger');
            }
        });
    });

    // ── ACTIVAR ──────────────────────────────────────────────────────────
    $(document).on('click', '.btnActivar', function () {
        userIdAccion = $(this).data('id');
        $('#nombreActivar').text($(this).data('name'));
        new bootstrap.Modal(document.getElementById('modalActivar')).show();
    });

    $('#btnConfirmarActivar').on('click', function () {
        $.ajax({
            url: '/usuario/' + userIdAccion + '/activar',
            method: 'POST',
            data: { _token: csrfToken },
            success: function () {
                mostrarToast('Médico reactivado correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error', 'danger');
            }
        });
    });

});
</script>
@endsection