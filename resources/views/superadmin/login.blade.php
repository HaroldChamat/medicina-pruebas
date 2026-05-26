<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SuperAdmin — Acceso Restringido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #0a1628;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background grid */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(212,160,23,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,160,23,.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Glowing orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .25;
            pointer-events: none;
        }
        .orb-1 { width: 400px; height: 400px; background: #d4a017; top: -100px; right: -100px; }
        .orb-2 { width: 300px; height: 300px; background: #0d3b6e; bottom: -80px; left: -80px; }

        .login-card {
            position: relative;
            z-index: 10;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(212,160,23,.2);
            border-radius: 20px;
            padding: 44px 40px;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(12px);
        }

        .crown-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #d4a017, #f0c040);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            color: #000;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(212,160,23,.4);
        }

        h4 { color: #fff; font-weight: 800; text-align: center; margin-bottom: 4px; }
        .subtitle { color: rgba(255,255,255,.45); font-size: 0.82rem; text-align: center; margin-bottom: 28px; letter-spacing: .5px; }

        .restricted-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(220,53,69,.15);
            border: 1px solid rgba(220,53,69,.3);
            color: #ff6b7a;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            margin: 0 auto 24px;
            display: block;
            width: fit-content;
        }

        .form-label { color: rgba(255,255,255,.6); font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; }

        .form-control {
            background: rgba(255,255,255,.06) !important;
            border: 1px solid rgba(255,255,255,.12) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 11px 14px !important;
            font-size: 0.875rem;
            transition: all .2s;
        }
        .form-control:focus {
            border-color: rgba(212,160,23,.5) !important;
            box-shadow: 0 0 0 3px rgba(212,160,23,.12) !important;
            background: rgba(255,255,255,.09) !important;
        }
        .form-control::placeholder { color: rgba(255,255,255,.25) !important; }
        .form-control.is-invalid { border-color: rgba(220,53,69,.5) !important; }
        .invalid-feedback { color: #ff6b7a !important; font-size: 0.75rem; }

        .btn-login {
            background: linear-gradient(135deg, #d4a017, #f0c040);
            color: #000;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.9rem;
            letter-spacing: .3px;
            transition: all .2s;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(212,160,23,.4); color: #000; }

        .back-link { color: rgba(255,255,255,.3); font-size: 0.75rem; text-align: center; margin-top: 20px; }
        .back-link a { color: rgba(212,160,23,.6); text-decoration: none; }
        .back-link a:hover { color: #d4a017; }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-card">
        <div class="crown-icon"><i class="bi bi-gem"></i></div>
        <h4>SuperAdmin</h4>
        <div class="subtitle">Panel de administración global</div>
        <div class="restricted-badge">
            <i class="bi bi-shield-lock-fill"></i> Acceso restringido
        </div>

        <form method="POST" action="{{ route('superadmin.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="superadmin@clinica.cl"
                    value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="saPass"
                        class="form-control"
                        placeholder="••••••••" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleSaPass"
                        style="border-color:rgba(255,255,255,.12); color:rgba(255,255,255,.4); background:rgba(255,255,255,.04);">
                        <i class="bi bi-eye-fill" id="saEye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Acceder al panel
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('welcome') }}">← Volver al sistema principal</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#toggleSaPass').on('click', function () {
            const t = $('#saPass').attr('type') === 'password' ? 'text' : 'password';
            $('#saPass').attr('type', t);
            $('#saEye').toggleClass('bi-eye-fill bi-eye-slash-fill');
        });
    </script>
</body>
</html>