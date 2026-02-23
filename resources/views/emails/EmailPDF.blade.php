<h3>Informe Médico</h3>

<p><strong>Cita:</strong> #{{ $cita->id }}</p>
<p><strong>Fecha:</strong> {{ $cita->Fecha_y_hora }}</p>
<p><strong>Médico:</strong> {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</p>
<p><strong>Paciente:</strong> {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</p>

<p>Se adjunta el informe médico en formato PDF.</p>

<hr>
<p style="font-size: 12px; color: gray;">
    Este correo fue generado automáticamente.
</p>
