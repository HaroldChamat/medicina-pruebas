@extends('layouts.app')

    @section('content')
        <div class="container">

            <h3>Informe Médico</h3>

            <div class="card mb-3">
                <div class="card-body">
                    <p>
                        <strong>Médico:</strong>
                        {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}
                    </p>

                    <p>
                        <strong>Paciente:</strong>
                        {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                    </p>

                    <p>
                        <strong>Fecha:</strong>
                        {{ $cita->Fecha_y_hora }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('informe.store', $cita->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Enfermedad diagnosticada</label>
                    <textarea name="enfermedad"
                            class="form-control"
                            rows="3"
                            required>{{ old('enfermedad', optional($cita->enfermedad)->descripcion) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tratamiento indicado</label>
                    <textarea name="tratamiento"
                            class="form-control"
                            rows="3"
                            required>{{ old('tratamiento', optional($cita->tratamiento)->descripcion) }}</textarea>
                </div>

                <button class="btn btn-success">Guardar Informe</button>
                <a href="/citas" class="btn btn-secondary">Volver</a>
            </form>

        </div>
    @endsection
