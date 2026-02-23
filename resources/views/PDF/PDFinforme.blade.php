<!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Informe Médico</title>
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 12px;
                }
                h1 {
                    text-align: center;
                }
                .section {
                    margin-bottom: 15px;
                }
                .label {
                    font-weight: bold;
                }
                .box {
                    border: 1px solid #000;
                    padding: 8px;
                }
            </style>
        </head>
        <body>

        <h1>Informe Médico</h1>

        <div class="section">
            <span class="label">ID Cita:</span> {{ $cita->id }}<br>
            <span class="label">Fecha:</span> {{ $cita->Fecha_y_hora }}
        </div>

        <div class="section">
            <span class="label">Médico:</span>
            {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}<br>

            <span class="label">Paciente:</span>
            {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
        </div>

        <div class="section">
            <div class="label">Enfermedad</div>
            <div class="box">
                {{ $cita->enfermedad->descripcion ?? 'No registrada' }}
            </div>
        </div>

        <div class="section">
            <div class="label">Tratamiento</div>
            <div class="box">
                {{ $cita->tratamiento->descripcion ?? 'No registrado' }}
            </div>
        </div>

        <p style="margin-top:40px; font-size:10px;">
            Documento generado automáticamente – uso informativo.
        </p>

        </body>
    </html>
