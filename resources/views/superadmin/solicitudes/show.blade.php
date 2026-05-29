@extends('superadmin.superadmin')

@section('title', 'Solicitud #' . $solicitud->id)

@push('styles')
<style>
    :root {
        --sg-primary: #4f46e5;
        --sg-primary-hover: #4338ca;
        --sg-border: #e5e7eb;
        --sg-bg: #f9fafb;
        --sg-card: #ffffff;
        --sg-text: #111827;
        --sg-muted: #6b7280;
        --sg-danger: #dc2626;
        --sg-success: #16a34a;
    }

    .sg-wrapper { max-width: 860px; margin: 2rem auto; padding: 0 1rem; }

    .sg-back {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .875rem;
        font-weight: 600;
        color: var(--sg-muted);
        text-decoration: none;
        margin-bottom: 1.25rem;
        transition: color .15s;
    }

    .sg-back:hover { color: var(--sg-primary); }

    /* Card principal */
    .sg-card {
        background: var(--sg-card);
        border: 1px solid var(--sg-border);
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.07);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .sg-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--sg-border);
        background: var(--sg-bg);
        flex-wrap: wrap;
        gap: .75rem;
    }

    .sg-card-header h2 { font-size: 1.1rem; font-weight: 800; color: var(--sg-text); margin: 0; }

    .sg-body { padding: 1.5rem; }

    /* Grid info */
    .sg-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem 2.5rem;
        margin-bottom: 1.5rem;
    }

    .sg-field label {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--sg-muted);
        display: block;
        margin-bottom: .3rem;
    }

    .sg-field p { font-size: .9375rem; color: var(--sg-text); font-weight: 500; margin: 0; }

    /* Centro arrows */
    .sg-centros-flow {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .875rem 1.25rem;
        background: var(--sg-bg);
        border: 1px solid var(--sg-border);
        border-radius: 10px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .sg-centro-box {
        flex: 1;
        min-width: 160px;
        padding: .75rem 1rem;
        border-radius: 8px;
        text-align: center;
    }

    .sg-centro-box.actual   { background: #fee2e2; border: 1.5px solid #fca5a5; }
    .sg-centro-box.nuevo    { background: #dcfce7; border: 1.5px solid #86efac; }
    .sg-centro-box label   { font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--sg-muted); display: block; margin-bottom: .3rem; }
    .sg-centro-box p       { font-size: .9rem; font-weight: 700; color: var(--sg-text); margin: 0; }

    .sg-arrow { font-size: 1.5rem; color: var(--sg-muted); flex-shrink: 0; }

    /* Motivo */
    .sg-motivo-box {
        background: var(--sg-bg);
        border: 1px solid var(--sg-border);
        border-radius: 8px;
        padding: 1rem 1.25rem;
        font-size: .9375rem;
        color: var(--sg-text);
        line-height: 1.65;
        white-space: pre-wrap;
        margin-bottom: 1.5rem;
    }

    /* Archivos */
    .sg-archivos-list { display: flex; flex-direction: column; gap: .5rem; margin-top: .75rem; }

    .sg-archivo-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .65rem 1rem;
        background: var(--sg-bg);
        border: 1px solid var(--sg-border);
        border-radius: 8px;
        text-decoration: none;
        color: var(--sg-text);
        font-size: .875rem;
        font-weight: 500;
        transition: border-color .15s, background .15s;
    }

    .sg-archivo-item:hover {
        border-color: var(--sg-primary);
        background: #ede9fe;
        color: var(--sg-primary);
    }

    .sg-archivo-item .arch-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* Divider */
    .sg-divider { border: none; border-top: 1px solid var(--sg-border); margin: 1.5rem 0; }

    /* Nota admin existente */
    .sg-nota-admin {
        background: #ede9fe;
        border: 1px solid #c4b5fd;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        font-size: .9rem;
        color: #4c1d95;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .8rem;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 700;
    }

    .badge-pendiente { background: #fef9c3; color: #713f12; }
    .badge-aceptada  { background: #dcfce7; color: #15803d; }
    .badge-rechazada { background: #fee2e2; color: #b91c1c; }
    .badge-alta      { background: #fee2e2; color: #b91c1c; }
    .badge-media     { background: #fef9c3; color: #713f12; }
    .badge-baja      { background: #dcfce7; color: #15803d; }

    /* Panel de acción */
    .sg-action-panel {
        background: var(--sg-card);
        border: 2px solid var(--sg-primary);
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.07);
        overflow: hidden;
    }

    .sg-action-header {
        padding: 1rem 1.5rem;
        background: #ede9fe;
        border-bottom: 1px solid #c4b5fd;
    }

    .sg-action-header h3 { font-size: 1rem; font-weight: 800; color: #4c1d95; margin: 0; }

    .sg-action-body { padding: 1.5rem; }

    .sg-textarea {
        width: 100%;
        padding: .7rem 1rem;
        border: 1.5px solid var(--sg-border);
        border-radius: 8px;
        font-size: .9375rem;
        resize: vertical;
        min-height: 110px;
        outline: none;
        transition: border-color .18s;
        box-sizing: border-box;
    }

    .sg-textarea:focus { border-color: var(--sg-primary); }

    .sg-action-btns {
        display: flex;
        gap: .75rem;
        margin-top: 1.25rem;
        flex-wrap: wrap;
    }

    .btn-aceptar {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .65rem 1.6rem;
        background: var(--sg-success);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .9375rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .btn-aceptar:hover { background: #15803d; }
    .btn-aceptar:active { transform: scale(.97); }
    .btn-aceptar:disabled { opacity: .6; cursor: not-allowed; }

    .btn-rechazar {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .65rem 1.6rem;
        background: var(--sg-danger);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .9375rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .btn-rechazar:hover { background: #b91c1c; }
    .btn-rechazar:active { transform: scale(.97); }
    .btn-rechazar:disabled { opacity: .6; cursor: not-allowed; }

    /* Spinner */
    .btn-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: none;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Estado ya gestionado */
    .sg-managed-notice {
        background: var(--sg-bg);
        border: 1px solid var(--sg-border);
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        text-align: center;
        color: var(--sg-muted);
        font-size: .9rem;
    }

    /* Toast */
    #sg-toast {
        position: fixed;
        top: 1.25rem;
        right: 1.25rem;
        z-index: 9999;
        padding: .875rem 1.25rem;
        border-radius: 10px;
        font-size: .9rem;
        font-weight: 600;
        color: #fff;
        box-shadow: 0 4px 18px rgba(0,0,0,.18);
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity .25s, transform .25s;
        pointer-events: none;
        max-width: 360px;
    }

    #sg-toast.show { opacity: 1; transform: translateY(0); }
    #sg-toast.success { background: var(--sg-success); }
    #sg-toast.error   { background: var(--sg-danger); }

    @media (max-width: 600px) {
        .sg-grid { grid-template-columns: 1fr; }
        .sg-body, .sg-action-body { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="sg-wrapper">

    <a href="{{ route('superadmin.solicitudes.index') }}" class="sg-back">
        ← Volver a solicitudes
    </a>

    {{-- ── Card detalle ── --}}
    <div class="sg-card">
        <div class="sg-card-header">
            <h2>Solicitud #{{ $solicitud->id }} — {{ $solicitud->asunto }}</h2>
            <div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;">
                @if($solicitud->prioridad)
                    <span class="badge badge-{{ $solicitud->prioridad }}">
                        {{ ucfirst($solicitud->prioridad) }}
                    </span>
                @endif
                <span class="badge badge-{{ $solicitud->estado }}">
                    {{ $solicitud->estado_icon }} {{ ucfirst($solicitud->estado) }}
                </span>
            </div>
        </div>

        <div class="sg-body">

            {{-- Flujo de centros --}}
            <div class="sg-centros-flow">
                <div class="sg-centro-box actual">
                    <label>Centro actual</label>
                    <p>{{ $solicitud->centroActual->nombre ?? 'Sin asignar' }}</p>
                </div>
                <span class="sg-arrow">→</span>
                <div class="sg-centro-box nuevo">
                    <label>Centro solicitado</label>
                    <p>{{ $solicitud->centroSolicitado->nombre ?? '—' }}</p>
                </div>
            </div>

            {{-- Info general --}}
            <div class="sg-grid">
                <div class="sg-field">
                    <label>Solicitante</label>
                    <p>{{ $solicitud->solicitante->name ?? '—' }}
                        <span style="font-size:.8rem; color:var(--sg-muted); font-weight:400;">
                            ({{ $solicitud->solicitante->cargo->Nombre_cargo ?? '' }})
                        </span>
                    </p>
                </div>
                <div class="sg-field">
                    <label>Paciente</label>
                    <p>{{ $solicitud->paciente->name ?? '—' }}</p>
                </div>
                <div class="sg-field">
                    <label>Fecha de envío</label>
                    <p>{{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($solicitud->gestionado_en)
                <div class="sg-field">
                    <label>Gestionada el</label>
                    <p>{{ $solicitud->gestionado_en->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            <hr class="sg-divider">

            {{-- Motivo --}}
            <div class="sg-field" style="margin-bottom:1.5rem;">
                <label style="font-size:.72rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--sg-muted); display:block; margin-bottom:.5rem;">
                    Motivo
                </label>
                <div class="sg-motivo-box">{{ $solicitud->motivo }}</div>
            </div>

            {{-- Archivos adjuntos --}}
            @if($solicitud->archivos->isNotEmpty())
            <div class="sg-field" style="margin-bottom:1.5rem;">
                <label style="font-size:.72rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--sg-muted); display:block; margin-bottom:.5rem;">
                    Documentos adjuntos ({{ $solicitud->archivos->count() }})
                </label>
                <div class="sg-archivos-list">
                    @foreach($solicitud->archivos as $archivo)
                    <a href="{{ asset('storage/' . $archivo->ruta) }}"
                       target="_blank"
                       class="sg-archivo-item"
                       download="{{ $archivo->nombre_original }}">
                        <span>📄</span>
                        <span class="arch-name">{{ $archivo->nombre_original }}</span>
                        <span style="font-size:.75rem; color:var(--sg-muted); flex-shrink:0;">
                            Subido por {{ $archivo->emisor->name ?? '—' }} · Descargar ↓
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Nota previa del superadmin --}}
            @if($solicitud->nota_superadmin && $solicitud->estado !== 'pendiente')
            <div class="sg-field">
                <label style="font-size:.72rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--sg-muted); display:block; margin-bottom:.5rem;">
                    Nota registrada
                </label>
                <div class="sg-nota-admin">{{ $solicitud->nota_superadmin }}</div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Panel de acción ── --}}
    @if($solicitud->estado === 'pendiente')
    <div class="sg-action-panel">
        <div class="sg-action-header">
            <h3>⚡ Gestionar solicitud</h3>
        </div>
        <div class="sg-action-body">
            <label style="font-size:.8125rem; font-weight:600; display:block; margin-bottom:.5rem;">
                Nota / comentario (opcional)
            </label>
            <textarea class="sg-textarea"
                      id="nota_superadmin"
                      placeholder="Escribe una nota o justificación para el solicitante..."></textarea>

            <div class="sg-action-btns">
                <button type="button" class="btn-aceptar" id="btn-aceptar">
                    <span class="btn-spinner" id="spin-aceptar"></span>
                    ✅ Aceptar y cambiar centro
                </button>
                <button type="button" class="btn-rechazar" id="btn-rechazar">
                    <span class="btn-spinner" id="spin-rechazar"></span>
                    ❌ Rechazar solicitud
                </button>
            </div>
        </div>
    </div>
    @else
    <div class="sg-managed-notice">
        Esta solicitud ya fue <strong>{{ $solicitud->estado }}</strong>
        @if($solicitud->gestionado_en)
            el {{ $solicitud->gestionado_en->format('d/m/Y') }}
        @endif.
    </div>
    @endif

</div>

{{-- Toast --}}
<div id="sg-toast"></div>
@endsection

@push('scripts')
<script>
(function () {

    function showToast(msg, type = 'success') {
        const t = document.getElementById('sg-toast');
        t.textContent = msg;
        t.className = `show ${type}`;
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.className = ''; }, 4500);
    }

    function setLoading(accion, loading) {
        const btn    = document.getElementById(`btn-${accion}`);
        const spin   = document.getElementById(`spin-${accion}`);
        if (!btn) return;
        btn.disabled = loading;
        spin.style.display = loading ? 'inline-block' : 'none';
    }

    async function gestionar(accion) {
        const nota = document.getElementById('nota_superadmin').value.trim();

        const url = '{{ route('superadmin.solicitudes.gestionar', $solicitud) }}';

        setLoading(accion === 'aceptada' ? 'aceptar' : 'rechazar', true);

        try {
            const resp = await fetch(url, {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ accion, nota_superadmin: nota }),
            });

            const data = await resp.json();

            if (!resp.ok) {
                showToast(data.message || 'Error al gestionar.', 'error');
                return;
            }

            showToast(
                accion === 'aceptada'
                    ? '✅ Solicitud aceptada. El centro médico del paciente ha sido actualizado.'
                    : '❌ Solicitud rechazada.',
                'success'
            );

            setTimeout(() => {
                window.location.href = '{{ route('superadmin.solicitudes.index') }}';
            }, 1800);

        } catch {
            showToast('Error de conexión. Intenta de nuevo.', 'error');
        } finally {
            setLoading('aceptar', false);
            setLoading('rechazar', false);
        }
    }

    document.getElementById('btn-aceptar')?.addEventListener('click', () => gestionar('aceptada'));
    document.getElementById('btn-rechazar')?.addEventListener('click', () => gestionar('rechazada'));

})();
</script>
@endpush