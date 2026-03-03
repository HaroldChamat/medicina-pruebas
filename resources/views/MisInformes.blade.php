@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h4 class="fw-bold mb-4">📄 Mis Informes Médicos</h4>

    @if($Citas->isEmpty())
        <div class="alert alert-info">
            Aún no tienes informes médicos registrados.
        </div>
    @else
        <div class="row g-4">
            @foreach($Citas as $cita)
                @if($cita->enfermedad && $cita->tratamiento)
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Cita #{{ $cita->id }}</span>
                                <small>{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="card-body">

                                <p class="mb-2">
                                    <span class="text-muted small">👨‍⚕️ Médico</span><br>
                                    <strong>{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</strong>
                                </p>

                                <hr>

                                <p class="mb-2">
                                    <span class="text-muted small">🦠 Diagnóstico</span><br>
                                    {{ $cita->enfermedad->descripcion }}
                                </p>

                                <p class="mb-0">
                                    <span class="text-muted small">💊 Tratamiento</span><br>
                                    {{ $cita->tratamiento->descripcion }}
                                </p>

                            </div>
                            <div class="card-footer bg-white border-0 text-end">
                                <a href="{{ route('informe.pdf', $cita->id) }}"
                                   class="btn btn-outline-danger btn-sm"
                                   target="_blank">
                                    📄 Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</div>
@endsection