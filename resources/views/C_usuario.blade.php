@extends('layouts.app')

    @section('content')

        <header class="mb-4">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-4">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="ms-auto">
                    <a class="nav-link fw-semibold" href="/">Ir a inicio</a>
                </div>
            </nav>
        </header>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">

                            <h4 class="text-center fw-bold mb-4">
                                Registro de Usuario
                            </h4>

                            <form action="{{ route('User.store') }}" method="POST" id="formUser">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nombre</label>
                                    <input type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        placeholder="Nombre">
                                </div>

                                <div class="mb-3">
                                    <label for="Apellidos" class="form-label fw-semibold">Apellidos</label>
                                    <input type="text"
                                        class="form-control"
                                        id="Apellidos"
                                        name="Apellidos"
                                        placeholder="Apellidos">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="example@gmail.com">
                                </div>

                                <div class="mb-3">
                                    <label for="Rut" class="form-label fw-semibold">Rut</label>
                                    <input type="text"
                                        class="form-control"
                                        id="Rut"
                                        name="Rut"
                                        placeholder="12345678-9">
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                    <input type="number"
                                        class="form-control"
                                        id="telefono"
                                        name="telefono"
                                        placeholder="Teléfono">
                                </div>

                                <div class="mb-4">
                                    <label for="id_cargo" class="form-label fw-semibold">Cargo</label>
                                    <select name="id_cargo"
                                            id="id_cargo"
                                            class="form-select"
                                            required>
                                        <option value="" disabled selected>
                                            Seleccione un cargo
                                        </option>

                                        @foreach($cargos as $cargo)
                                            @if(
                                                session('admin') === 1 ||
                                                !in_array($cargo->Nombre_cargo, ['Otro', 'Medico'])
                                            )
                                                <option value="{{ $cargo->id }}">
                                                    {{ $cargo->Nombre_cargo }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit"
                                        class="btn btn-primary w-100 fw-semibold"
                                        id="Ingresar_U">
                                    Ingresar Usuario
                                </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('javascript')
        <script>
            $(document).ready(function() {

                console.log('Documento listo');

                $("#Ingresar_U").on('click', function(event){
                    event.preventDefault();

                    var name = $("#name").val();
                    var Apellidos = $("#Apellidos").val();
                    var email = $("#email").val();
                    var Rut = $("#Rut").val();
                    var telefono = $("#telefono").val();
                    var id_cargo = $("#id_cargo").val();
                    var admin = $("#admin").val();

                    var argumentos = {
                        name: name,
                        Apellidos: Apellidos,
                        email: email,
                        Rut: Rut,
                        telefono: telefono,
                        id_cargo: id_cargo,
                        admin: admin
                    };

                    console.log('valores capturados:', argumentos);

                    $.ajax({
                        url: "/usuario/store",
                        type: "POST",
                        dataType: "json",
                        data: argumentos,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response){
                            console.log('Respuesta del servidor:', response);
                            alert('Usuario ingresado correctamente');
                            window.location.href = "/";
                        },
                        error: function(xhr, status, error){
                            console.error('Error en la solicitud AJAX:', error);
                            console.log(xhr.responseJSON);
                            alert('Error al ingresar el usuario');
                        }
                    });
                });
            });
        </script>
    @endsection