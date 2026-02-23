@extends('layouts.app')
    @section('content')
            <header class="mb-4 shadow-sm">
                <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-4">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>

                    @if(session('admin') === 1 || session('cargo') === 'Medico')

                        <div class="navbar-nav ms-auto">
                            <a class="nav-link" href="/Informacion">Informes Medicos</a>
                        </div>

                        <div class="navbar-nav ms-auto">
                            <a class="nav-link" href="/Especialidad">Especialidades</a>
                        </div>

                        <div class="navbar-nav ms-auto">
                            <a class="nav-link" href="/Horario">Horarios</a>
                        </div>
                    @endif

                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="/">Ir a inicio</a>
                    </div>

                    @if(session('admin') === 1 || session('cargo') === 'Medico' || session('cargo') === 'Paciente')
                        <button type="button" class="btn btn-outline-danger">
                            <a href="/logout" class="nav-link">Cerrar Sesión</a>
                        </button>
                    @endif

                </nav>
            </header>


        @if(session('cargo') === 'admin')

            <div class="container mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <div class="d-flex align-items-center mb-3">
                            <span class="fs-5 me-2">🔍</span>
                            <h5 class="mb-0">Filtros de búsqueda</h5>
                        </div>

                        <div class="row g-3 align-items-end">

                            <!-- Médico -->
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    👨‍⚕️ Médico
                                </label>
                                <select id="filtroMedico" class="form-select">
                                    <option value="">Todos los médicos</option>
                                    @foreach($medicos as $medico)
                                        <option value="{{ $medico->id }}">
                                            {{ $medico->name }} {{ $medico->Apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Paciente -->
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    🧑 Paciente
                                </label>
                                <select id="filtroPaciente" class="form-select" disabled>
                                    <option value="">Todos los pacientes</option>
                                </select>
                            </div>

                            <!-- Limpiar -->
                            <div class="col-md-2 d-grid">
                                <button class="btn btn-outline-secondary" id="btnLimpiarFiltros">
                                    ❌ Limpiar
                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        @endif

        <div class="container">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- AQUÍ VA TODO -->
                        <table class="table table-hover align-middle table-borderless">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Medico</th>
                                    <th scope="col">Paciente</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Estado</th>
                                    @if(session('admin') === 1 || session('cargo') === 'Medico')
                                        <th scope="col">Acciones</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($Citas as $cita)
                                    <tr data-medico="{{ $cita->medico->id }}" data-paciente="{{ $cita->paciente->id }}" data-paciente-texto="{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}">
                                    <td>{{ $cita->id }}</td>
                                    <td>{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</td>
                                    <td><a href="{{ route('historial.index', $cita->paciente->id) }}"> {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }} </a></td>
                                    <td>{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $cita->estado }}</td>

                                    @if(session('admin') === 1 || session('cargo') === 'Medico')
                                        <td>
                                            <a href="#" data-id="{{ $cita->id }}" class="btn btn-primary editar">Editar</a>

                                            @if(session('admin') === 1)
                                                <a href="#" data-id="{{ $cita->id }}" class="btn btn-danger eliminar">Eliminar</a>
                                            @endif
                                            
                                            @if(!$cita->enfermedad || !$cita->tratamiento)
                                                <a href="{{ route('informe.create', $cita->id) }}" class="btn btn-info">
                                                    Informe
                                                </a>
                                            @else
                                                <button class="btn btn-secondary" disabled>
                                                    Informe enviado
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                            <button class="btn btn-outline-primary" id="btnEmailGlobal">
                                📧 Enviar correo
                            </button>

                            @if(session('admin') === 1)
                                <button class="btn btn-outline-success" id="btnWhatsappGlobal">
                                    💬 WhatsApp
                                </button>
                            @endif

                            <button class="btn btn-outline-danger" id="btnPdfGlobal">
                                📄 Descargar PDF
                            </button>
                        </div>

                        @if(session('admin') === 1 || session('cargo') === 'Paciente')
                            <div class="text-end mt-4">
                                <button class="btn btn-success btn-lg" id="btnAgregarCita">
                                    ➕ Nueva cita
                                </button>
                            </div>
                        @endif

                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalInstrucciones">
                            📘 Instrucciones
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal editar -->
        <div class="modal fade" id="exampledit" tabindex="-1" aria-labelledby="exampleditLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleditLabel">Editar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="formEditarCita">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="cita_id" name="id">

                            <input id="medico" class="form-control mb-2" disabled>
                            <input id="paciente" class="form-control mb-2" disabled>

                            <input id="fecha" name="Fecha_y_hora" type="datetime-local" class="form-control mb-2">

                            <select id="estado" name="estado" class="form-select">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Finalizada">Finalizada</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>

                            <div class="modal-footer px-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Modal crear -->
        <div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCrearLabel">Nueva cita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="formCrearCita">
                        @csrf

                        <div class="modal-body">

                            <!-- Médico -->
                            <label>Médico</label>
                            <select name="medico_id" id="medico_id" class="form-select mb-2" required>
                                <option value="" selected disabled>Seleccione un médico</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico->id }}">
                                        {{ $medico->name }} {{ $medico->Apellidos }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Paciente -->
                            <label>Paciente</label>
                            <select name="paciente_id" class="form-select mb-2" required>
                                <option value="" selected disabled>Seleccione un paciente</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">
                                        {{ $paciente->name }} {{ $paciente->Apellidos }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Fecha -->
                            <label>Fecha</label>
                            <input type="date" id="fecha_cita" name="Fecha_y_hora" class="form-control mb-2" required>
                            <!-- input oculto que se enviará -->
                            <input type="hidden" name="Fecha_y_hora" id="Fecha_y_hora">

                            <div class="mb-3">
                                <label class="form-label">Hora de atención</label>
                                <select id="hora_atencion" class="form-select" disabled>
                                    <option value="">Seleccione una hora</option>
                                </select>
                            </div>

                            <!-- Estado -->
                            <label>Estado</label>
                            <select name="estado" class="form-select">
                                <option value="Pendiente">Pendiente</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <!-- Modal confirmar cancelación -->
        <div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmar cancelación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        ¿Estás seguro de que deseas <strong>cancelar y eliminar</strong> esta cita?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            No
                        </button>
                        <button class="btn btn-danger" id="btnConfirmarCancelacion">
                            Sí, cancelar cita
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="modalWhatsapp" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Enviar informe por WhatsApp</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="mb-1">Cita</label>
                        <select id="selectCitaWhatsapp" class="form-select mb-3">
                            <option value="" selected disabled>Seleccione una cita</option>
                            @foreach($Citas as $cita)
                                <option value="{{ $cita->id }}"
                                    data-fecha="{{ $cita->Fecha_y_hora }}"
                                    data-medico="{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}"
                                    data-paciente="{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}"
                                    data-enfermedad="{{ $cita->enfermedad->descripcion ?? '' }}"
                                    data-tratamiento="{{ $cita->tratamiento->descripcion ?? '' }}">
                                    #{{ $cita->id }} - {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                </option>
                            @endforeach
                        </select>

                        <label class="mb-1">Número de teléfono</label>
                        <input type="text"
                            id="telefonoWhatsapp"
                            class="form-control"
                            placeholder="Ej: 56912345678">

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="btn btn-success" id="btnEnviarWhatsapp">
                            Enviar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPdf" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Descargar informe PDF</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="mb-1">Cita</label>
                        <select id="selectCitaPdf" class="form-select">
                            <option value="" selected disabled>Seleccione una cita</option>
                            @foreach($Citas as $cita)
                                <option value="{{ $cita->id }}">
                                    #{{ $cita->id }} - {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="btn btn-danger" id="btnDescargarPdf">
                            Descargar PDF
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEmail" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Enviar informe por correo</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="mb-1">Cita</label>
                        <select id="selectCitaEmail" class="form-select mb-3">
                            <option value="" selected disabled>Seleccione una cita</option>
                            @foreach($Citas as $cita)
                                <option value="{{ $cita->id }}">
                                    #{{ $cita->id }} - {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                                </option>
                            @endforeach
                        </select>

                        <label class="mb-1">Correo electrónico</label>
                        <input type="email"
                            id="correoEmail"
                            class="form-control"
                            placeholder="ejemplo@correo.com">

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="btn btn-primary" id="btnEnviarEmail">
                            Enviar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalInstrucciones" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">📘 Instrucciones del sistema</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        @if(session('admin') === 1)
                            <h6 class="fw-bold text-primary">👑 Administrador</h6>
                            <ul>
                                <li>Gestionar médicos y pacientes</li>
                                <li>Crear, editar y eliminar citas</li>
                                <li>Asignar especialidades a médicos</li>
                                <li>Enviar informes por correo, WhatsApp o PDF</li>
                                <li>Ver y filtrar todas las citas del sistema</li>
                            </ul>

                        @elseif(session('cargo') === 'Medico')
                            <h6 class="fw-bold text-success">👨‍⚕️ Médico</h6>
                            <ul>
                                <li>Ver sus citas asignadas</li>
                                <li>Editar el estado de una cita</li>
                                <li>Crear informes médicos</li>
                                <li>Consultar historial de pacientes</li>
                                <li>Enviar informes por correo o WhatsApp</li>
                            </ul>

                        @elseif(session('cargo') === 'Paciente')
                            <h6 class="fw-bold text-warning">🧑 Paciente</h6>
                            <ul>
                                <li>Ver sus citas médicas</li>
                                <li>Solicitar nuevas citas</li>
                                <li>Consultar su historial médico</li>
                                <li>Recibir informes médicos</li>
                            </ul>

                        @else
                            <p class="text-muted">
                                No hay una sesión activa. Inicie sesión para acceder al sistema.
                            </p>
                        @endif

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>
        
    @endsection

    @section('javascript')
    @parent

    
        <script>
            $(document).ready(function(){

                let citaCancelarId = null;
                let modalConfirmar;


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                console.log('Citas cargadas');

                console.log(
                $('meta[name="csrf-token"]').length
                );
                
                $('.eliminar').on('click', function(){
                    var citaId = $(this).data('id');
                    console.log('ID de la cita a eliminar:', citaId);

                    $.ajax({
                        url: '/citas/' + citaId,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'DELETE'
                        },
                        success: function () {
                            console.log('Cita eliminada con éxito');
                            location.reload();
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                });

                let modalEditar;

                $('.editar').on('click', function(){
                    let citaId = $(this).data('id');

                    $.ajax({
                        url: '/citas/' + citaId + '/edit',
                        type: 'GET',
                        success: function(cita) {

                            $('#cita_id').val(cita.id);
                            $('#medico').val(cita.medico.Nombre);
                            $('#paciente').val(cita.paciente.Nombre);
                            $('#fecha').val(cita.Fecha_y_hora.replace(' ', 'T'));
                            $('#estado').val(cita.estado);

                            modalEditar = new bootstrap.Modal(
                                document.getElementById('exampledit')
                            );
                            modalEditar.show();
                        }
                    });
                });

                let modalCrear;

                //--- abrir modal ---
                $('#btnAgregarCita').on('click', function () {
                    modalCrear = new bootstrap.Modal(
                        document.getElementById('modalCrear')
                    );
                    modalCrear.show();
                });

                //+++ guardar cita +++
                $('#formCrearCita').on('submit', function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: '/citas',
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function () {
                            modalCrear.hide();
                            location.reload();
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            alert('Error al crear la cita');
                        }
                    });
                });

                //+++ Editar cita +++

                $('#formEditarCita').on('submit', function(e){
                    e.preventDefault();

                    let citaId = $('#cita_id').val();

                    $.ajax({
                        url: '/citas/' + citaId,
                        method: 'POST',   // ⬅️ SIEMPRE POST
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'PUT',
                            Fecha_y_hora: $('#fecha').val(),
                            estado: $('#estado').val()
                        },
                        success: function () {
                            modalEditar.hide();
                            location.reload();
                        },
                        error: function (xhr) {
                            console.error(xhr.status);
                            console.error(xhr.responseText);
                            alert('Error al actualizar');
                        }
                    });
                });

                // === FILTROS ===

                // cambiar médico
                $('#filtroMedico').on('change', function () {

                    let medicoSeleccionado = $(this).val(); // string
                    let pacientes = new Map();

                    $('tbody tr').each(function () {
                        let medicoFila = $(this).data('medico').toString();
                        let pacienteFila = $(this).data('paciente').toString();
                        let textoPaciente = $(this).data('paciente-texto');

                        if (!medicoSeleccionado || medicoFila === medicoSeleccionado) {
                            $(this).show();
                            pacientes.set(pacienteFila, textoPaciente);
                        } else {
                            $(this).hide();
                        }
                    });

                    // llenar select de pacientes
                    let selectPaciente = $('#filtroPaciente');
                    selectPaciente.empty()
                        .append('<option value="">Todos los pacientes</option>');

                    pacientes.forEach((nombre, id) => {
                        selectPaciente.append(`<option value="${id}">${nombre}</option>`);
                    });

                    selectPaciente.prop('disabled', pacientes.size === 0);
                    selectPaciente.val(''); // reset paciente
                });


                // cambiar paciente
                $('#filtroPaciente').on('change', function () {

                    let pacienteSeleccionado = $(this).val();
                    let medicoSeleccionado = $('#filtroMedico').val();

                    $('tbody tr').each(function () {
                        let medicoFila = $(this).data('medico').toString();
                        let pacienteFila = $(this).data('paciente').toString();

                        let mostrar =
                            (!medicoSeleccionado || medicoFila === medicoSeleccionado) &&
                            (!pacienteSeleccionado || pacienteFila === pacienteSeleccionado);

                        $(this).toggle(mostrar);
                    });
                });

                $('#estado').on('change', function () {
                    if ($(this).val() === 'Cancelada') {
                        citaCancelarId = $('#cita_id').val();

                        modalConfirmar = new bootstrap.Modal(
                            document.getElementById('modalConfirmarCancelacion')
                        );
                        modalConfirmar.show();
                    }
                });

                $('#btnConfirmarCancelacion').on('click', function () {

                    $.ajax({
                        url: '/citas/' + citaCancelarId,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'DELETE'
                        },
                        success: function () {
                            modalConfirmar.hide();
                            modalEditar.hide();
                            location.reload();
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            alert('Error al cancelar la cita');
                        }
                    });
                });

                let modalWhatsapp;
                let dataWhatsapp = {};

                $('#btnWhatsappGlobal').on('click', function () {
                    modalWhatsapp = new bootstrap.Modal(
                        document.getElementById('modalWhatsapp')
                    );
                    modalWhatsapp.show();
                });

                $('#selectCitaWhatsapp').on('change', function () {
                    let option = $(this).find(':selected');

                    dataWhatsapp = {
                        id: option.val(),
                        fecha: option.data('fecha'),
                        medico: option.data('medico'),
                        paciente: option.data('paciente'),
                        enfermedad: option.data('enfermedad'),
                        tratamiento: option.data('tratamiento')
                    };
                });

                $('#btnEnviarWhatsapp').on('click', function () {

                    if (!dataWhatsapp.id) {
                        alert('Seleccione una cita');
                        return;
                    }

                    let telefono = $('#telefonoWhatsapp').val().replace(/\D/g, '');

                    if (!telefono) {
                        alert('Ingrese un número válido');
                        return;
                    }

                    let mensaje =
                `📋 *Informe Médico*
                ------------------------
                🆔 Cita: ${dataWhatsapp.id}
                📅 Fecha: ${dataWhatsapp.fecha}
                👨‍⚕️ Médico: ${dataWhatsapp.medico}
                🧑 Paciente: ${dataWhatsapp.paciente}

                🦠 Enfermedad:
                ${dataWhatsapp.enfermedad || 'No registrada'}

                💊 Tratamiento:
                ${dataWhatsapp.tratamiento || 'No registrado'}

                ⚠️ Documento informativo`;

                    let url = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;

                    window.open(url, '_blank');

                    modalWhatsapp.hide();
                    $('#telefonoWhatsapp').val('');
                    $('#selectCitaWhatsapp').val('');
                    dataWhatsapp = {};
                });

                let modalPdf;

                $('#btnPdfGlobal').on('click', function () {
                    modalPdf = new bootstrap.Modal(
                        document.getElementById('modalPdf')
                    );
                    modalPdf.show();
                });

                $('#btnDescargarPdf').on('click', function () {

                    let citaId = $('#selectCitaPdf').val();

                    if (!citaId) {
                        alert('Seleccione una cita');
                        return;
                    }

                    // abre descarga directa
                    window.open('/informe/pdf/' + citaId, '_blank');

                    modalPdf.hide();
                    $('#selectCitaPdf').val('');
                });

                let modalEmail;

                $('#btnEmailGlobal').on('click', function () {
                    modalEmail = new bootstrap.Modal(
                        document.getElementById('modalEmail')
                    );
                    modalEmail.show();
                });

                $('#btnEnviarEmail').on('click', function () {

                    let citaId = $('#selectCitaEmail').val();
                    let correo = $('#correoEmail').val();

                    if (!citaId || !correo) {
                        alert('Seleccione una cita y escriba un correo');
                        return;
                    }

                    $.ajax({
                        url: '/informe/email',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            cita_id: citaId,
                            correo: correo
                        },
                        success: function () {
                            alert('Correo enviado correctamente');
                            modalEmail.hide();
                            $('#selectCitaEmail').val('');
                            $('#correoEmail').val('');
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            alert('Error al enviar el correo');
                        }
                    });
                });
            });


        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const medico  = document.getElementById('medico_id');
                const fecha   = document.getElementById('fecha_cita');
                const horaSel = document.getElementById('hora_atencion');
                const fechaHoraInput = document.getElementById('Fecha_y_hora');

                function cargarHoras() {

                    // limpiar select
                    horaSel.innerHTML = '<option value="">Cargando...</option>';
                    horaSel.disabled = true;

                    // si no hay medico o fecha, no hacer nada
                    if (!medico.value || !fecha.value) {
                        horaSel.innerHTML = '<option value="">Seleccione médico y fecha</option>';
                        return;
                    }

                    fetch(`/citas/horas-disponibles?medico_id=${medico.value}&fecha=${fecha.value}`)
                        .then(response => response.json())
                        .then(horas => {

                            horaSel.innerHTML = '<option value="">Seleccione una hora</option>';

                            // si no hay horas disponibles
                            if (horas.length === 0) {
                                horaSel.innerHTML = '<option value="">No hay horas disponibles</option>';
                                return;
                            }

                            // llenar el select
                            horas.forEach(hora => {
                                const option = document.createElement('option');
                                option.value = hora;
                                option.textContent = hora;
                                horaSel.appendChild(option);
                            });

                            horaSel.disabled = false;
                        })
                        .catch(() => {
                            horaSel.innerHTML = '<option value="">Error al cargar horas</option>';
                        });
                }

                // cuando cambie medico o fecha
                medico.addEventListener('change', cargarHoras);
                fecha.addEventListener('change', cargarHoras);

                // cuando se elija una hora, armar Fecha_y_hora
                horaSel.addEventListener('change', () => {

                    if (!horaSel.value) {
                        fechaHoraInput.value = '';
                        return;
                    }

                    // unir fecha + hora
                    fechaHoraInput.value = `${fecha.value} ${horaSel.value}`;
                });

            });
        </script>

    @endsection