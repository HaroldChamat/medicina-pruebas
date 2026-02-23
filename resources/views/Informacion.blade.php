@extends('layouts.app')

    @section('content')

        <header class="mb-4">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-4">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="ms-auto d-flex gap-3">
                    <a class="nav-link fw-semibold" href="/citas">Citas</a>
                    <a class="nav-link fw-semibold" href="/">Ir a inicio</a>
                </div>
            </nav>
        </header>

        <div class="container">

            <h3 class="fw-bold mb-4">Información de Informes Médicos</h3>

            <!-- Filtros -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Médico</label>
                            <select id="filtroMedico" class="form-select">
                                <option value="">Todos</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico->id }}">
                                        {{ $medico->name }} {{ $medico->Apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ID Cita</label>
                            <input type="number"
                                id="filtroCita"
                                class="form-control"
                                placeholder="Ej: 12">
                        </div>

                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Informe</th>
                                    <th>ID Cita</th>
                                    <th>Médico</th>
                                    <th>Paciente</th>
                                    <th>Fecha</th>
                                    <th>Observaciones</th>
                                    <th>Tratamiento</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($citas as $cita)
                                    <tr data-medico="{{ $cita->medico->id }}"
                                        data-cita="{{ $cita->id }}">

                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $cita->id }}</td>

                                        <td>
                                            {{ $cita->medico->name }}
                                            {{ $cita->medico->Apellidos }}
                                        </td>

                                        <td>
                                            {{ $cita->paciente->name }}
                                            {{ $cita->paciente->Apellidos }}
                                        </td>

                                        <td>{{ $cita->Fecha_y_hora }}</td>

                                        <td>
                                            {{ $cita->enfermedad->descripcion }}
                                        </td>

                                        <td>
                                            {{ $cita->tratamiento->descripcion }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    @endsection

    @section('javascript')
        <script>
            $(document).ready(function(){

                function filtrar() {
                    let medico = $('#filtroMedico').val();
                    let cita = $('#filtroCita').val();

                    $('tbody tr').each(function () {
                        let medicoFila = $(this).data('medico').toString();
                        let citaFila = $(this).data('cita').toString();

                        let mostrar =
                            (!medico || medicoFila === medico) &&
                            (!cita || citaFila.includes(cita));

                        $(this).toggle(mostrar);
                    });
                }

                $('#filtroMedico').on('change', filtrar);
                $('#filtroCita').on('keyup', filtrar);

            });
        </script>
    @endsection