<h2>Recordatorio de cita médica</h2>

<p>Hola <strong>{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</strong>,</p>

<p>Te recordamos que tienes una cita médica programada:</p>

<ul>
    <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</li>
    <li><strong>Médico:</strong> {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</li>
</ul>

<p>Adjuntamos la información disponible de tu cita.</p>

<p>Saludos,<br>
<strong>Clínica</strong></p>
