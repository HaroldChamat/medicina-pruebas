@extends('layouts.app')
    @section('content')
        @if(session('cargo'))
            <div style="background:#f0f0f0; padding:10px; margin:10px;">
                <strong>DEBUG:</strong>
                cargo = {{ session('cargo') }} |
                admin = {{ session('admin') }} |
                user_id = {{ session('user_id') }}
            </div>
        @endif
        <header class="mb-4">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-4">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="ms-auto d-flex align-items-center gap-3">

                    @if(session('cargo'))

                        <a class="nav-link" href="/citas">Citas</a>

                        <button class="btn btn-outline-primary btn-sm" onclick="location.href='/C_usuario'">Crear Usuario</button>

                        <form action="/logout" method="GET">
                            <button class="btn btn-outline-danger btn-sm">
                                Cerrar sesión
                            </button>
                        </form>
                    @endif
                </div>
            </nav>
        </header>

        @if(!session()->has('cargo'))
            {{-- LOGIN POR RUT --}}
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title text-center mb-3 fw-semibold">Acceso al sistema</h5>

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <input type="text"
                                            name="rut"
                                            class="form-control"
                                            placeholder="Ingrese RUT"
                                            required>
                                    </div>

                                    @error('rut')
                                        <div class="text-danger small mb-2">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <p class="mb-1">¿Eres nuevo?</p>
                                    <a href="/C_usuario" class="btn btn-link p-0">Crear cuenta</a>

                                    <button type="submit" class="btn btn-primary w-100 mt-3">Ingresar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($nombreCargo)
            @if ($nombreCargo === 'admin')
                <div class="container mb-4">
                    <div class="alert alert-success d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <strong>Bienvenido Administrador</strong><br>
                            Tienes acceso completo al sistema.
                        </div>
                        <span class="badge bg-success">ADMIN</span>
                    </div>
                </div>
            @endif
            
            @if ($nombreCargo === 'admin' || $nombreCargo === 'Medico')
                <div class="container">
                    <div class="row g-4">
                        <div class="col-md-6 p-5">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold text-primary mb-2">Médicos</h5>

                                    <p class="card-text text-muted">Gestión y administración de médicos del sistema.</p>
                                    
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#exampleMedico">
                                        Ver médicos
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleMedico" tabindex="-1" aria-labelledby="exampleMedicoLabel">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleMedicoLabel">Medicos</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle table-borderless">
                                                        <thead class="table-light text-uppercase small">
                                                            <tr>
                                                                <th scope="col">ID</th>
                                                                <th scope="col">Nombre</th>
                                                                <th scope="col">Apellidos</th>
                                                                <th scope="col">Email</th>
                                                                <th scope="col">Rut_medico</th>
                                                                <th scope="col">Telefono</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @foreach($medicos as $medico)
                                                                <tr>
                                                                    <th class="table-primary">{{ $loop->iteration }}</th>
                                                                    <td class="table-primary">{{ $medico->name }}</td>
                                                                    <td class="table-primary">{{ $medico->Apellidos }}</td>
                                                                    <td class="table-primary">{{ $medico->email }}</td>
                                                                    <td class="table-primary"><span class="badge bg-secondary">{{ $medico->Rut }}</span></td>
                                                                    <td class="table-primary">{{ $medico->telefono }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    @if ($nombreCargo === 'Medico' || $nombreCargo === 'admin')
                        <div class="col-md-6 p-5">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold text-success mb-2">
                                        Pacientes
                                    </h5>

                                    <p class="card-text text-muted mb-4">
                                        Gestión y administración de pacientes del sistema.
                                    </p>

                                    <button
                                        type="button"
                                        class="btn btn-outline-success mt-auto w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#examplePaciente">
                                        Ver pacientes
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="examplePaciente" tabindex="-1" aria-labelledby="examplePacienteLabel">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="examplePacienteLabel">
                                                        Lista de pacientes
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-middle table-borderless">
                                                            <thead class="table-light text-uppercase small">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Nombre</th>
                                                                    <th>Apellidos</th>
                                                                    <th>Email</th>
                                                                    <th>RUT</th>
                                                                    <th>Teléfono</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($pacientes as $paciente)
                                                                    <tr>
                                                                        <td class="fw-semibold table-primary">{{ $loop->iteration }}</td>
                                                                        <td class="table-primary">{{ $paciente->name }}</td>
                                                                        <td class="table-primary">{{ $paciente->Apellidos }}</td>
                                                                        <td class="text-muted table-primary">{{ $paciente->email }}</td>
                                                                        <td class="table-primary">
                                                                            <span class="badge bg-secondary">
                                                                                {{ $paciente->Rut }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="table-primary">{{ $paciente->telefono }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Cerrar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($nombreCargo === 'Paciente')
                <div class="container mb-4">
                    <div class="alert alert-info text-center shadow-sm">
                        Bienvenido/a<br>
                        <strong>{{ $usuario->name }} {{ $usuario->Apellidos }}</strong>
                        <hr>
                        Puedes revisar tus citas, tu historial médico y solicitar nuevas atenciones.
                    </div>
                </div>
            @endif
        @endif
        
    @endsection