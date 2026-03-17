@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-chat-dots-fill me-2"></i> Mensajes
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                Conversaciones activas con
                @if(session('cargo') === 'Paciente') el equipo administrativo
                @else los pacientes
                @endif
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    @if($citas->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-chat-slash fs-1 mb-3 d-block"></i>
                <p class="mb-0">No tienes conversaciones activas.</p>
                <small>El chat se habilita cuando una cita está <strong>Programada</strong>
                o dentro de los 2 días posteriores a ser <strong>Finalizada</strong>.</small>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($citas as $cita)
                @php
                    $otroUsuario = session('cargo') === 'Paciente' ? $cita->medico : $cita->paciente;
                    $noLeidos = \App\Models\Mensaje::where('cita_id', $cita->id)
                        ->where('receptor_id', session('user_id'))
                        ->where('leido', false)
                        ->count();
                    $ultimoMensaje = \App\Models\Mensaje::where('cita_id', $cita->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                @endphp
                <div class="col-12">
                    <a href="{{ route('chat.show', $cita->id) }}"
                       class="text-decoration-none">
                        <div class="card border-0 shadow-sm hover-shadow">
                            <div class="card-body d-flex align-items-center gap-3">

                                {{-- Avatar --}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:52px; height:52px; background: linear-gradient(135deg, #0d3b6e, #1a6fa8); font-size: 1.2rem;">
                                    {{ strtoupper(substr($otroUsuario->name, 0, 1)) }}
                                </div>

                                {{-- Info --}}
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="fw-semibold mb-0 text-dark">
                                                @if(session('cargo') === 'Paciente') Dr. @endif
                                                {{ $otroUsuario->name }} {{ $otroUsuario->Apellidos }}
                                            </p>
                                            <small class="text-muted">
                                                Cita: {{ $cita->codigo_cita ?? 'CIT-'.$cita->id }}
                                                · {{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}
                                                @if(session('admin') === 1)
                                                    · <i class="bi bi-person-badge me-1"></i>Dr. {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end flex-shrink-0 ms-2">
                                            @if($cita->estado === 'Programada')
                                                <span class="badge bg-primary mb-1">📅 Programada</span>
                                            @else
                                                @php
                                                    $diasRestantes = (int) \Carbon\Carbon::now()
                                                        ->diffInDays(\Carbon\Carbon::parse($cita->Fecha_y_hora)->addDays(2), false);
                                                @endphp
                                                <span class="badge bg-warning text-dark mb-1">
                                                    ⏳ Cierra en {{ $diasRestantes }} día(s)
                                                </span>
                                            @endif
                                            @if($noLeidos > 0)
                                                <br>
                                                <span class="badge bg-danger">{{ $noLeidos }} nuevo(s)</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($ultimoMensaje)
                                        <p class="text-muted small mb-0 mt-1 text-truncate">
                                            {{ $ultimoMensaje->contenido }}
                                        </p>
                                    @else
                                        <p class="text-muted small mb-0 mt-1 fst-italic">Sin mensajes aún</p>
                                    @endif
                                </div>

                                <i class="bi bi-chevron-right text-muted flex-shrink-0"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection