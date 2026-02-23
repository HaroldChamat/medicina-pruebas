@extends('layouts.app')

    @section('content')
        <div class="container">

            <h3 class="mb-4">
                Historial Médico de {{ $paciente->name }} {{ $paciente->Apellidos }}
            </h3>

            @if($historial->isEmpty())
                <div class="alert alert-warning">
                    Este paciente no tiene historial médico.
                </div>
            @else
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Cita</th>
                            <th>Fecha</th>
                            <th>Médico</th>
                            <th>Enfermedad</th>
                            <th>Tratamiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historial as $cita)
                            <tr>
                                <td>{{ $cita->id }}</td>
                                <td>{{ $cita->Fecha_y_hora }}</td>
                                <td>
                                    {{ $cita->medico->name }}
                                    {{ $cita->medico->Apellidos }}
                                </td>
                                <td>
                                    {{ $cita->enfermedad->descripcion ?? '—' }}
                                </td>
                                <td>
                                    {{ $cita->tratamiento->descripcion ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
                Volver
            </a>

        </div>
    @endsection
