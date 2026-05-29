<style>
    /* ── Modal cambio directo de centro ── */
    .mcc-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .mcc-overlay.active { display: flex; }

    .mcc-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 20px 60px rgba(0,0,0,.22);
        overflow: hidden;
        animation: mcc-in .2s ease;
    }

    @keyframes mcc-in {
        from { transform: translateY(16px) scale(.97); opacity: 0; }
        to   { transform: none; opacity: 1; }
    }

    .mcc-header {
        padding: 1.25rem 1.5rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .mcc-header h3 { font-size: 1rem; font-weight: 800; color: #111827; margin: 0; }

    .mcc-close {
        background: none;
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        color: #6b7280;
        line-height: 1;
        padding: .2rem;
        transition: color .15s;
    }

    .mcc-close:hover { color: #111827; }

    .mcc-body { padding: 1.5rem; }

    .mcc-patient-info {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem 1rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        margin-bottom: 1.25rem;
    }

    .mcc-patient-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #1a56db;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: .875rem;
        flex-shrink: 0;
    }

    .mcc-patient-name { font-weight: 700; font-size: .9375rem; color: #1e3a5f; }
    .mcc-patient-centro { font-size: .8rem; color: #3b82f6; margin-top: .1rem; }

    .mcc-label {
        font-size: .8125rem;
        font-weight: 600;
        color: #111827;
        display: block;
        margin-bottom: .4rem;
    }

    .mcc-select {
        width: 100%;
        padding: .6rem .875rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: .9375rem;
        color: #111827;
        background: #f9fafb;
        outline: none;
        transition: border-color .18s;
        box-sizing: border-box;
    }

    .mcc-select:focus { border-color: #4f46e5; background: #fff; }

    .mcc-error {
        font-size: .78rem;
        color: #dc2626;
        margin-top: .3rem;
        display: none;
    }

    .mcc-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: .65rem;
    }

    .btn-mcc-cancel {
        padding: .55rem 1.2rem;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        border-radius: 8px;
        font-size: .9rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: background .15s;
    }

    .btn-mcc-cancel:hover { background: #f9fafb; }

    .btn-mcc-confirm {
        padding: .55rem 1.3rem;
        border: none;
        background: #4f46e5;
        color: #fff;
        border-radius: 8px;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .btn-mcc-confirm:hover { background: #4338ca; }
    .btn-mcc-confirm:disabled { opacity: .6; cursor: not-allowed; }

    .mcc-spinner {
        width: 15px;
        height: 15px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: none;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Toast */
    #mcc-toast {
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

    #mcc-toast.show { opacity: 1; transform: translateY(0); }
    #mcc-toast.success { background: #16a34a; }
    #mcc-toast.error   { background: #dc2626; }

    /* Botón disparador (usar donde necesites) */
    .btn-cambiar-centro {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .45rem 1rem;
        border-radius: 8px;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        font-size: .8375rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: border-color .15s, background .15s, color .15s;
    }

    .btn-cambiar-centro:hover {
        border-color: #4f46e5;
        background: #ede9fe;
        color: #4f46e5;
    }
</style>

{{-- Botón disparador --}}
<button type="button"
        class="btn-cambiar-centro"
        id="btn-open-mcc"
        data-paciente-id="{{ $paciente->id }}"
        data-paciente-nombre="{{ $paciente->name }}"
        data-centro-actual="{{ $paciente->centroMedico->nombre ?? 'Sin centro' }}">
    🏥 Cambiar centro médico
</button>

{{-- Modal --}}
<div class="mcc-overlay" id="mcc-overlay">
    <div class="mcc-modal">
        <div class="mcc-header">
            <h3>Cambiar centro médico</h3>
            <button class="mcc-close" id="mcc-close-btn" type="button">✕</button>
        </div>

        <div class="mcc-body">
            {{-- Info paciente --}}
            <div class="mcc-patient-info">
                <div class="mcc-patient-avatar" id="mcc-avatar">{{ strtoupper(substr($paciente->name, 0, 2)) }}</div>
                <div>
                    <div class="mcc-patient-name">{{ $paciente->name }}</div>
                    <div class="mcc-patient-centro">
                        Centro actual: <strong>{{ $paciente->centroMedico->nombre ?? 'Sin asignar' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Selector de centro --}}
            <label class="mcc-label" for="mcc-centro-select">
                Nuevo centro médico <span style="color:#dc2626;">*</span>
            </label>
            <select class="mcc-select" id="mcc-centro-select">
                <option value="">— Selecciona el nuevo centro —</option>
                @foreach($centros as $c)
                    <option value="{{ $c->id }}"
                        {{ $paciente->centro_medico_id == $c->id ? 'disabled' : '' }}>
                        {{ $c->nombre }}
                        {{ $paciente->centro_medico_id == $c->id ? '(actual)' : '' }}
                    </option>
                @endforeach
            </select>
            <div class="mcc-error" id="mcc-error">Debes seleccionar un centro diferente al actual.</div>
        </div>

        <div class="mcc-footer">
            <button type="button" class="btn-mcc-cancel" id="mcc-cancel-btn">Cancelar</button>
            <button type="button" class="btn-mcc-confirm" id="mcc-confirm-btn">
                <span class="mcc-spinner" id="mcc-spinner"></span>
                <span id="mcc-confirm-text">Confirmar cambio</span>
            </button>
        </div>
    </div>
</div>

<div id="mcc-toast"></div>

<script>
(function () {
    const overlay    = document.getElementById('mcc-overlay');
    const openBtn    = document.getElementById('btn-open-mcc');
    const closeBtn   = document.getElementById('mcc-close-btn');
    const cancelBtn  = document.getElementById('mcc-cancel-btn');
    const confirmBtn = document.getElementById('mcc-confirm-btn');
    const select     = document.getElementById('mcc-centro-select');
    const errorEl    = document.getElementById('mcc-error');
    const spinner    = document.getElementById('mcc-spinner');
    const confirmTxt = document.getElementById('mcc-confirm-text');

    const pacienteId = openBtn.dataset.pacienteId;

    function openModal() {
        overlay.classList.add('active');
        select.value = '';
        errorEl.style.display = 'none';
    }

    function closeModal() {
        overlay.classList.remove('active');
    }

    function showToast(msg, type = 'success') {
        const t = document.getElementById('mcc-toast');
        t.textContent = msg;
        t.className = `show ${type}`;
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.className = ''; }, 4000);
    }

    function setLoading(loading) {
        confirmBtn.disabled = loading;
        spinner.style.display = loading ? 'inline-block' : 'none';
        confirmTxt.textContent = loading ? 'Guardando...' : 'Confirmar cambio';
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeModal();
    });

    confirmBtn.addEventListener('click', async () => {
        errorEl.style.display = 'none';

        if (!select.value) {
            errorEl.style.display = 'block';
            errorEl.textContent = 'Debes seleccionar un nuevo centro.';
            return;
        }

        setLoading(true);

        try {
            const resp = await fetch(`/superadmin/pacientes/${pacienteId}/cambiar-centro`, {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ centro_medico_id: select.value }),
            });

            const data = await resp.json();

            if (!resp.ok) {
                errorEl.style.display = 'block';
                errorEl.textContent = data.message || 'Error al cambiar el centro.';
                return;
            }

            closeModal();
            showToast('✅ Centro médico actualizado correctamente.', 'success');

            // Recargar la página para reflejar el cambio
            setTimeout(() => location.reload(), 1500);

        } catch {
            showToast('Error de conexión. Intenta de nuevo.', 'error');
        } finally {
            setLoading(false);
        }
    });
})();
</script>