<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; color: #333; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0d3b6e 0%, #1a6fa8 60%, #2196b0 100%); padding: 30px; text-align: center; }
        .header h1 { color: #fff; font-size: 1.4rem; margin-bottom: 4px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 0.85rem; }
        .header .icon { font-size: 2.5rem; margin-bottom: 10px; }
        .body { padding: 30px; }
        .body h2 { font-size: 1.1rem; color: #0d3b6e; margin-bottom: 16px; border-left: 4px solid #0d3b6e; padding-left: 10px; }
        .info-box { background: #f8f9fa; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6c757d; font-weight: 600; }
        .info-value { color: #333; text-align: right; }
        .message-box { background: #fff; border: 1px solid #e9ecef; border-radius: 8px; padding: 16px; margin: 16px 0; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; margin: 6px; }
        .btn-primary { background: #0d3b6e; color: #fff; }
        .btn-success { background: #1a7a4a; color: #fff; }
        .btn-danger  { background: #dc3545; color: #fff; }
        .btn-warning { background: #e6a817; color: #fff; }
        .btn-center { text-align: center; margin: 24px 0; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .badge-alta   { background: #fde8e8; color: #dc3545; }
        .badge-media  { background: #fff8e1; color: #e6a817; }
        .badge-baja   { background: #e8f5e9; color: #1a7a4a; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 0.78rem; color: #adb5bd; border-top: 1px solid #e9ecef; }
        .divider { border: none; border-top: 1px solid #e9ecef; margin: 20px 0; }
        .alert { padding: 12px 16px; border-radius: 8px; margin: 16px 0; font-size: 0.88rem; }
        .alert-info    { background: #e8f4fd; color: #0d3b6e; border-left: 4px solid #0d3b6e; }
        .alert-success { background: #e8f5e9; color: #1a7a4a; border-left: 4px solid #1a7a4a; }
        .alert-warning { background: #fff8e1; color: #856404; border-left: 4px solid #e6a817; }
        .alert-danger  { background: #fde8e8; color: #dc3545; border-left: 4px solid #dc3545; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="icon">🏥</div>
            <h1>Centro Médico</h1>
            <p>Sistema de Gestión Médica</p>
        </div>
        <div class="body">
            @yield('contenido')
        </div>
        <div class="footer">
            <p>Este correo fue generado automáticamente, por favor no respondas a este mensaje.</p>
            <p style="margin-top:6px;">© {{ date('Y') }} Centro Médico · Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>