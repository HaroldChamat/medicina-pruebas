@extends('layouts.app')
@section('content')

<div class="container mt-4" style="max-width: 780px;">

    {{-- Encabezado --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-building-gear me-2"></i> Solicitud de Cambio de Centro
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                Completa el formulario para enviar la solicitud al superadministrador.
            </p>
        </div>
        <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Mis solicitudes
        </a>
    </div>

    {{-- Aviso --}}
    <div class="alert d-flex align-items-center gap-2 mb-4"
         style="background:#fffbeb; border:1px solid #fcd34d; color:#92400e; border-radius:10px;">
        <i class="bi bi-info-circle-fill fs-5" style="color:#d97706; flex-shrink:0;"></i>
        <span class="small">
            Tu solicitud será revisada por el superadministrador.
            Recibirás una notificación cuando sea gestionada.
        </span>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="card-body p-4">

            <form id="sol-form" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- ── SECCIÓN 1: Información general ── --}}
                <p class="text-muted small fw-bold text-uppercase mb-3"
                   style="letter-spacing:.08em; border-bottom:1px solid #e9ecef; padding-bottom:.5rem;">
                    Información general
                </p>

                <div class="row g-3">

                    {{-- Solicitante (siempre lectura) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nombre del solicitante</label>
                        <input class="form-control bg-light" type="text"
                               value="{{ session('nombre') }}" disabled readonly>
                    </div>

                    @if(session('cargo') !== 'Paciente')
                    {{-- Médico / Admin: selector de paciente --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">
                            Paciente <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="paciente_id" name="paciente_id">
                            <option value="">— Selecciona un paciente —</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}"
                                        data-centro="{{ $p->centro_medico_id ?? '' }}"
                                        data-centro-nombre="{{ $p->centroMedico->nombre ?? 'Sin centro asignado' }}">
                                    {{ $p->name }} {{ $p->Apellidos }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-danger small mt-1" id="err-paciente_id"></div>
                    </div>
                    @else
                    {{-- Paciente: solo su nombre (no hay selector) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nombre del paciente</label>
                        <input class="form-control bg-light" type="text"
                               value="{{ session('nombre') }}" disabled readonly>
                    </div>
                    @endif

                </div>

                {{-- ── BANNER: Centro médico actual ── --}}
                <div id="banner-centro-actual" class="mt-3 rounded-3 px-3 py-2 d-flex align-items-center gap-2"
                     style="background:#eff6ff; border:1px solid #bfdbfe; @if(session('cargo') !== 'Paciente') display:none!important; @endif">
                    <i class="bi bi-building-fill flex-shrink-0" style="color:#3b82f6;"></i>
                    <span class="small">
                        @if(session('cargo') === 'Paciente')
                            {{-- Paciente: siempre muestra su propio centro --}}
                            @php
                                $pacienteActual = \App\Models\User::with('centroMedico')->find(session('user_id'));
                            @endphp
                            Te encuentras actualmente en:
                            <strong id="txt-centro-actual">
                                {{ $pacienteActual?->centroMedico?->nombre ?? 'Sin centro asignado' }}
                            </strong>
                        @else
                            {{-- Médico / Admin: se rellena dinámicamente al seleccionar paciente --}}
                            El paciente se encuentra actualmente en:
                            <strong id="txt-centro-actual">—</strong>
                        @endif
                    </span>
                </div>

                {{-- Centro destino --}}
                <div class="mt-3">
                    <label class="form-label fw-semibold small">
                        Centro médico de destino <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="centro_solicitado_id" name="centro_solicitado_id">
                        <option value="">— Selecciona el centro de destino —</option>
                        @foreach($centros as $c)
                            @php
                                // Para pacientes: deshabilitar el centro actual directamente en el servidor
                                $esCentroActual = (session('cargo') === 'Paciente')
                                    && ($pacienteActual?->centro_medico_id == $c->id);
                            @endphp
                            <option value="{{ $c->id }}"
                                    @if($esCentroActual) disabled @endif
                                    class="centro-option"
                                    data-centro-id="{{ $c->id }}">
                                {{ $c->nombre }}
                                @if($esCentroActual) (tu centro actual) @endif
                                — {{ $c->direccion }}
                            </option>
                        @endforeach
                    </select>
                    <div class="text-danger small mt-1" id="err-centro_solicitado_id"></div>
                </div>

                @if(session('cargo') !== 'Paciente')
                <hr class="my-4">

                {{-- ── SECCIÓN 2: Prioridad ── --}}
                <p class="text-muted small fw-bold text-uppercase mb-3"
                   style="letter-spacing:.08em; border-bottom:1px solid #e9ecef; padding-bottom:.5rem;">
                    Prioridad
                </p>

                <div class="d-flex gap-3 flex-wrap mb-1">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                        <input type="radio" name="prioridad" value="alta" class="d-none prioridad-radio">
                        <span class="badge rounded-pill px-3 py-2 prioridad-badge"
                              data-val="alta"
                              style="border:2px solid #dee2e6; background:#f8f9fa; color:#6c757d; font-size:.875rem; cursor:pointer;">
                            🔴 Alta
                        </span>
                    </label>
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                        <input type="radio" name="prioridad" value="media" class="d-none prioridad-radio">
                        <span class="badge rounded-pill px-3 py-2 prioridad-badge"
                              data-val="media"
                              style="border:2px solid #dee2e6; background:#f8f9fa; color:#6c757d; font-size:.875rem; cursor:pointer;">
                            🟡 Media
                        </span>
                    </label>
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                        <input type="radio" name="prioridad" value="baja" class="d-none prioridad-radio">
                        <span class="badge rounded-pill px-3 py-2 prioridad-badge"
                              data-val="baja"
                              style="border:2px solid #dee2e6; background:#f8f9fa; color:#6c757d; font-size:.875rem; cursor:pointer;">
                            🟢 Baja
                        </span>
                    </label>
                </div>
                <div class="text-danger small" id="err-prioridad"></div>
                @endif

                <hr class="my-4">

                {{-- ── SECCIÓN 3: Detalle ── --}}
                <p class="text-muted small fw-bold text-uppercase mb-3"
                   style="letter-spacing:.08em; border-bottom:1px solid #e9ecef; padding-bottom:.5rem;">
                    Detalle de la solicitud
                </p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Asunto <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" type="text" id="asunto" name="asunto"
                               maxlength="200"
                               placeholder="Ej: Cambio por traslado de residencia">
                        <div class="text-danger small mt-1" id="err-asunto"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Motivo <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="motivo" name="motivo"
                                  rows="5" maxlength="3000"
                                  placeholder="Describe detalladamente el motivo de la solicitud..."></textarea>
                        <div class="text-danger small mt-1" id="err-motivo"></div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ── SECCIÓN 4: Archivos ── --}}
                <p class="text-muted small fw-bold text-uppercase mb-3"
                   style="letter-spacing:.08em; border-bottom:1px solid #e9ecef; padding-bottom:.5rem;">
                    Documentos adjuntos <span class="fw-normal text-muted">(opcional)</span>
                </p>

                <div id="dropzone"
                     class="text-center rounded-3 py-4 px-3 mb-3"
                     style="border: 2px dashed #ced4da; cursor:pointer; position:relative; transition: border-color .2s, background .2s;">
                    <input type="file" name="archivos[]" id="archivos"
                           multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                           style="position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;">
                    <i class="bi bi-paperclip fs-2 text-muted d-block mb-2"></i>
                    <p class="small text-muted mb-0">
                        <strong class="text-primary">Haz clic para seleccionar</strong> o arrastra los archivos aquí<br>
                        PDF, imágenes, Word — máx. 5 MB por archivo
                    </p>
                </div>

                <div id="file-list" class="d-flex flex-column gap-2"></div>

                {{-- Acciones --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('solicitudes.index') }}"
                       class="btn btn-outline-secondary rounded-pill px-4">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold"
                            id="btn-submit" style="background:#0d3b6e; border-color:#0d3b6e;">
                        <span id="spinner-btn" class="spinner-border spinner-border-sm me-2 d-none"></span>
                        <span id="btn-text">
                            <i class="bi bi-send-fill me-1"></i> Enviar solicitud
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    // ── Datos de centros para manipulación dinámica ───────────────────────────
    @php
        $centrosJs = $centros->map(function($c) {
            return ['id' => $c->id, 'nombre' => $c->nombre, 'direccion' => $c->direccion];
        })->values();
    @endphp
    const todosCentros = @json($centrosJs);

    @if(session('cargo') === 'Paciente')
    // Para paciente: el centro actual es fijo y ya viene del servidor
    const centroActualPacienteId = {{ $pacienteActual?->centro_medico_id ?? 'null' }};
    @endif

    // ── Función: actualizar el selector de destino excluyendo un centro ───────
    function actualizarSelectDestino(centroExcluirId, centroExcluirNombre) {
        const $select = $('#centro_solicitado_id');
        const valorActual = $select.val();

        $select.empty().append('<option value="">— Selecciona el centro de destino —</option>');

        todosCentros.forEach(function (c) {
            const esActual = c.id == centroExcluirId;
            $select.append(
                $('<option>', {
                    value:    c.id,
                    text:     c.nombre + (esActual ? ' (centro actual)' : '') + ' — ' + c.direccion,
                    disabled: esActual,
                    'data-centro-id': c.id,
                })
            );
        });

        // Restaurar selección previa si aún es válida
        if (valorActual && valorActual != centroExcluirId) {
            $select.val(valorActual);
        } else {
            $select.val('');
        }
    }

    @if(session('cargo') !== 'Paciente')
    // ── Médico / Admin: al seleccionar paciente, actualizar banner y selector ─
    $('#paciente_id').on('change', function () {
        const $opt          = $(this).find(':selected');
        const centroId      = $opt.data('centro');
        const centroNombre  = $opt.data('centro-nombre') || 'Sin centro asignado';
        const $banner       = $('#banner-centro-actual');

        if (!$(this).val()) {
            // Sin paciente seleccionado: ocultar banner y restaurar todos los centros
            $banner.hide();
            actualizarSelectDestino(null, null);
            return;
        }

        // Mostrar banner con el centro del paciente seleccionado
        $('#txt-centro-actual').text(centroNombre);
        $banner.css('display', 'flex');

        // Excluir el centro actual del selector de destino
        actualizarSelectDestino(centroId, centroNombre);
    });

    // Al cargar: si ya hay un paciente seleccionado (unlikely pero posible), inicializar
    if ($('#paciente_id').val()) {
        $('#paciente_id').trigger('change');
    }
    @else
    // ── Paciente: inicializar selector excluyendo su centro actual al cargar ──
    actualizarSelectDestino(centroActualPacienteId, '{{ $pacienteActual?->centroMedico?->nombre ?? "" }}');
    @endif

    // ── Prioridad radio-buttons estilizados ───────────────────────────────────
    const estilosPrioridad = {
        alta:  { bg: '#fee2e2', border: '#ef4444', color: '#b91c1c' },
        media: { bg: '#fef9c3', border: '#eab308', color: '#713f12' },
        baja:  { bg: '#dcfce7', border: '#22c55e', color: '#15803d' },
    };

    $('.prioridad-badge').on('click', function () {
        const val = $(this).data('val');

        $('.prioridad-badge').css({
            background: '#f8f9fa',
            border: '2px solid #dee2e6',
            color: '#6c757d'
        });
        $('.prioridad-radio').prop('checked', false);

        const est = estilosPrioridad[val];
        $(this).css({
            background: est.bg,
            border: `2px solid ${est.border}`,
            color: est.color
        });
        $(this).closest('label').find('.prioridad-radio').prop('checked', true);
        $('#err-prioridad').text('');
    });

    // ── Dropzone / archivos ───────────────────────────────────────────────────
    let selectedFiles = [];
    const dropzone = document.getElementById('dropzone');

    ['dragover', 'dragenter'].forEach(ev =>
        dropzone.addEventListener(ev, e => {
            e.preventDefault();
            dropzone.style.borderColor = '#0d3b6e';
            dropzone.style.background = '#eff6ff';
        })
    );
    ['dragleave', 'drop'].forEach(ev =>
        dropzone.addEventListener(ev, () => {
            dropzone.style.borderColor = '#ced4da';
            dropzone.style.background = '';
        })
    );
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        addFiles(Array.from(e.dataTransfer.files));
    });

    document.getElementById('archivos').addEventListener('change', function () {
        addFiles(Array.from(this.files));
        this.value = '';
    });

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function addFiles(files) {
        files.forEach(f => {
            if (f.size > 5 * 1024 * 1024) {
                mostrarToast(`"${f.name}" supera los 5 MB.`, 'warning');
                return;
            }
            if (!selectedFiles.find(x => x.name === f.name && x.size === f.size)) {
                selectedFiles.push(f);
            }
        });
        renderFileList();
    }

    function removeFile(idx) {
        selectedFiles.splice(idx, 1);
        renderFileList();
    }

    function renderFileList() {
        const list = $('#file-list');
        list.empty();
        selectedFiles.forEach((f, i) => {
            list.append(`
                <div class="d-flex align-items-center gap-2 p-2 rounded-3"
                     style="background:#f8f9fa; border:1px solid #e9ecef; font-size:.85rem;">
                    <i class="bi bi-file-earmark text-primary"></i>
                    <span class="flex-grow-1 text-truncate">${f.name}</span>
                    <span class="text-muted">${formatBytes(f.size)}</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove" data-i="${i}">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `);
        });
        $('.btn-remove').on('click', function () {
            removeFile(+$(this).data('i'));
        });
    }

    // ── Envío del formulario ──────────────────────────────────────────────────
    $('#sol-form').on('submit', async function (e) {
        e.preventDefault();

        // Limpiar errores
        $('[id^="err-"]').text('');

        // Validación extra: centro destino no puede ser el actual
        const centroDest = $('#centro_solicitado_id').val();
        @if(session('cargo') === 'Paciente')
        if (centroDest && centroDest == centroActualPacienteId) {
            $('#err-centro_solicitado_id').text('No puedes seleccionar tu centro médico actual como destino.');
            return;
        }
        @else
        const centroActualSeleccionado = $('#paciente_id').find(':selected').data('centro');
        if (centroDest && centroDest == centroActualSeleccionado) {
            $('#err-centro_solicitado_id').text('El centro de destino no puede ser el centro actual del paciente.');
            return;
        }
        @endif

        const fd = new FormData(this);
        selectedFiles.forEach(f => fd.append('archivos[]', f));

        $('#btn-submit').prop('disabled', true);
        $('#spinner-btn').removeClass('d-none');
        $('#btn-text').html('<i class="bi bi-send-fill me-1"></i> Enviando...');

        try {
            const resp = await fetch('{{ route('solicitudes.store') }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });

            const data = await resp.json();

            if (!resp.ok) {
                if (resp.status === 422 && data.errors) {
                    Object.entries(data.errors).forEach(([k, v]) => {
                        $('#err-' + k.replace('.', '_')).text(v[0]);
                    });
                    mostrarToast('Por favor corrige los errores.', 'warning');
                } else {
                    mostrarToast(data.message || 'Error al enviar la solicitud.', 'danger');
                }
                return;
            }

            mostrarToast('✅ Solicitud enviada correctamente.', 'success');
            setTimeout(() => window.location.href = '{{ route('solicitudes.index') }}', 1400);

        } catch (err) {
            mostrarToast('Error de conexión. Intenta de nuevo.', 'danger');
        } finally {
            $('#btn-submit').prop('disabled', false);
            $('#spinner-btn').addClass('d-none');
            $('#btn-text').html('<i class="bi bi-send-fill me-1"></i> Enviar solicitud');
        }
    });

});
</script>
@endsection