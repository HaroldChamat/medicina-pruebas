<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Administrativo — Centro Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0a1628;
            display: flex;
            overflow: hidden;
        }

        /* ── Left panel (decorativo) ── */
        .left-panel {
            width: 55%;
            background: linear-gradient(135deg, #0d3b6e 0%, #1a6fa8 60%, #2196b0 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .left-panel .deco-icon {
            position: absolute;
            opacity: .06;
            font-size: 25rem;
            color: #fff;
            bottom: -60px;
            right: -60px;
            transform: scaleX(-1);
        }
        .left-panel .deco-icon2 {
            position: absolute;
            opacity: .04;
            font-size: 18rem;
            color: #fff;
            top: -40px;
            left: -40px;
        }

        .left-title { color: #fff; font-size: 2.2rem; font-weight: 800; line-height: 1.2; position: relative; z-index: 1; }
        .left-sub   { color: rgba(255,255,255,.7); font-size: .95rem; margin-top: 12px; max-width: 380px; position: relative; z-index: 1; }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px;
            padding: 14px 18px;
            margin-top: 16px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(6px);
            width: 100%;
            max-width: 420px;
        }
        .feature-item i { font-size: 1.3rem; color: #f0c040; flex-shrink: 0; }
        .feature-item div { color: rgba(255,255,255,.85); font-size: .88rem; }
        .feature-item strong { color: #fff; display: block; font-size: .9rem; }

        /* ── Right panel (formulario) ── */
        .right-panel {
            width: 45%;
            background: #0f1d2e;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
        }

        .tab-selector {
            display: flex;
            gap: 8px;
            background: rgba(255,255,255,.05);
            border-radius: 14px;
            padding: 6px;
            width: 100%;
            max-width: 360px;
            margin-bottom: 36px;
        }
        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            color: rgba(255,255,255,.45);
            background: transparent;
        }
        .tab-btn.active {
            background: #d4a017;
            color: #000;
            box-shadow: 0 4px 14px rgba(212,160,23,.35);
        }

        .form-box { width: 100%; max-width: 360px; }
        .form-box h4 { color: #fff; font-weight: 700; font-size: 1.3rem; margin-bottom: 6px; }
        .form-box .subtitle { color: rgba(255,255,255,.4); font-size: .82rem; margin-bottom: 28px; }

        .form-label { color: rgba(255,255,255,.6); font-size: .8rem; font-weight: 600; margin-bottom: 6px; }
        .form-control, .form-select {
            background: rgba(255,255,255,.06) !important;
            border: 1px solid rgba(255,255,255,.12) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 11px 14px !important;
            font-size: .875rem;
            transition: all .2s;
        }
        .form-control:focus {
            border-color: rgba(212,160,23,.5) !important;
            box-shadow: 0 0 0 3px rgba(212,160,23,.12) !important;
            background: rgba(255,255,255,.09) !important;
        }
        .form-control::placeholder { color: rgba(255,255,255,.25) !important; }
        .form-control.is-invalid { border-color: rgba(220,53,69,.5) !important; }
        .invalid-feedback { color: #ff6b7a !important; font-size: .75rem; }

        .btn-ingresar {
            background: linear-gradient(135deg, #d4a017, #f0c040);
            color: #000;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: .9rem;
            transition: all .2s;
            width: 100%;
        }
        .btn-ingresar:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(212,160,23,.4);
            color: #000;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.2);
            font-size: .75rem;
            margin: 20px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.1);
        }

        .btn-back {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            color: rgba(255,255,255,.6);
            border-radius: 10px;
            padding: 10px;
            font-size: .83rem;
            width: 100%;
            transition: all .2s;
        }
        .btn-back:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
        }

        .badge-restricted {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(220,53,69,.15);
            border: 1px solid rgba(220,53,69,.3);
            color: #ff6b7a;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; min-height: 240px; padding: 40px 30px; }
            .left-panel .deco-icon { display: none; }
            .right-panel { width: 100%; padding: 40px 24px; }
        }
    </style>
</head>
<body>

    {{-- ── Panel izquierdo ── --}}
    <div class="left-panel">
        <i class="bi bi-heart-pulse-fill deco-icon"></i>
        <i class="bi bi-hospital-fill deco-icon2"></i>

        <div class="text-center mb-5" style="position:relative;z-index:1;">
            <i class="bi bi-hospital-fill text-white" style="font-size:2.8rem;"></i>
            <h2 class="text-white fw-bold mt-2 mb-1">Centro Médico</h2>
            <p style="color:rgba(255,255,255,.55);font-size:.85rem;">Sistema de Gestión Médica</p>
        </div>

        <h2 class="left-title text-center">Portal Administrativo</h2>
        <p class="left-sub text-center">Acceso exclusivo para administradores y superadministradores del sistema.</p>

        <div class="feature-item mt-4">
            <i class="bi bi-shield-fill-check"></i>
            <div>
                <strong>Administrador de Centro</strong>
                Gestión de médicos, pacientes, citas e informes.
            </div>
        </div>
        <div class="feature-item">
            <i class="bi bi-gem"></i>
            <div>
                <strong>SuperAdministrador</strong>
                Control global de centros médicos y administradores.
            </div>
        </div>
    </div>

    {{-- ── Panel derecho ── --}}
    <div class="right-panel">

        {{-- Selector de tipo de acceso --}}
        <div class="tab-selector">
            <button class="tab-btn active" id="tabAdmin" onclick="switchTab('admin')">
                <i class="bi bi-shield-check me-1"></i> Administrador
            </button>
            <button class="tab-btn" id="tabSuperadmin" onclick="switchTab('superadmin')">
                <i class="bi bi-gem me-1"></i> SuperAdmin
            </button>
        </div>

        {{-- Formulario Admin (login por RUT) --}}
        <div class="form-box" id="formAdmin">
            <div class="badge-restricted">
                <i class="bi bi-lock-fill"></i> Acceso restringido
            </div>
            <h4>Acceso Administrador</h4>
            <p class="subtitle">Ingresa con tus credenciales de administrador</p>

            @if($errors->any() && old('_tipo') === 'admin')
                <div class="alert alert-danger rounded-3 small py-2 mb-3" style="background:rgba(220,53,69,.15);border:1px solid rgba(220,53,69,.3);color:#ff6b7a;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="_tipo" value="admin">

                <div class="mb-3">
                    <label class="form-label">RUT</label>
                    <input type="text" name="rut" id="rutAdmin"
                           class="form-control @error('rut') is-invalid @enderror"
                           placeholder="12345678-9"
                           value="{{ old('rut') }}"
                           maxlength="12" autocomplete="off" required>
                    @error('rut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passAdmin"
                               class="form-control"
                               placeholder="••••••••" required>
                        <button class="btn" type="button" id="togglePassAdmin"
                                style="border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.4);background:rgba(255,255,255,.04);">
                            <i class="bi bi-eye-fill" id="eyeAdmin"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-ingresar mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                </button>
            </form>

            <div class="divider">o</div>

            <a href="{{ route('welcome') }}" class="btn btn-back text-decoration-none text-center d-block">
                <i class="bi bi-arrow-left me-1"></i> Volver al inicio
            </a>
        </div>

        {{-- Formulario SuperAdmin (login por email) --}}
        <div class="form-box d-none" id="formSuperadmin">
            <div class="badge-restricted" style="background:rgba(212,160,23,.12);border-color:rgba(212,160,23,.3);color:#d4a017;">
                <i class="bi bi-gem"></i> SuperAdmin
            </div>
            <h4>Acceso SuperAdmin</h4>
            <p class="subtitle">Panel de control global del sistema</p>

            @if($errors->any() && old('_tipo') === 'superadmin')
                <div class="alert alert-danger rounded-3 small py-2 mb-3" style="background:rgba(220,53,69,.15);border:1px solid rgba(220,53,69,.3);color:#ff6b7a;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login.post') }}">
                @csrf
                <input type="hidden" name="_tipo" value="superadmin">

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email"
                           class="form-control"
                           placeholder="superadmin@clinica.cl"
                           value="{{ old('email') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passSA"
                               class="form-control"
                               placeholder="••••••••" required>
                        <button class="btn" type="button" id="togglePassSA"
                                style="border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.4);background:rgba(255,255,255,.04);">
                            <i class="bi bi-eye-fill" id="eyeSA"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-ingresar mb-3"
                        style="background:linear-gradient(135deg,#0d3b6e,#1a6fa8);color:#fff;">
                    <i class="bi bi-gem me-2"></i>Acceder al panel
                </button>
            </form>

            <div class="divider">o</div>

            <a href="{{ route('welcome') }}" class="btn btn-back text-decoration-none text-center d-block">
                <i class="bi bi-arrow-left me-1"></i> Volver al inicio
            </a>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // ── Tab switch ─────────────────────────────────────────────────────────
        function switchTab(tipo) {
            document.getElementById('tabAdmin').classList.toggle('active', tipo === 'admin');
            document.getElementById('tabSuperadmin').classList.toggle('active', tipo === 'superadmin');
            document.getElementById('formAdmin').classList.toggle('d-none', tipo !== 'admin');
            document.getElementById('formSuperadmin').classList.toggle('d-none', tipo !== 'superadmin');
        }

        // Auto-switch si hay error en superadmin form
        @if(old('_tipo') === 'superadmin')
            switchTab('superadmin');
        @endif

        // ── Toggle contraseñas ─────────────────────────────────────────────────
        document.getElementById('togglePassAdmin').addEventListener('click', function () {
            const p = document.getElementById('passAdmin');
            p.type = p.type === 'password' ? 'text' : 'password';
            document.getElementById('eyeAdmin').classList.toggle('bi-eye-fill');
            document.getElementById('eyeAdmin').classList.toggle('bi-eye-slash-fill');
        });

        document.getElementById('togglePassSA').addEventListener('click', function () {
            const p = document.getElementById('passSA');
            p.type = p.type === 'password' ? 'text' : 'password';
            document.getElementById('eyeSA').classList.toggle('bi-eye-fill');
            document.getElementById('eyeSA').classList.toggle('bi-eye-slash-fill');
        });

        // ── Formato RUT ────────────────────────────────────────────────────────
        document.getElementById('rutAdmin').addEventListener('input', function () {
            const limpio = this.value.replace(/[^0-9kK]/g, '');
            if (limpio.length < 2) { this.value = limpio; return; }
            this.value = limpio.slice(0, -1) + '-' + limpio.slice(-1).toUpperCase();
        });
    </script>

</body>
</html>