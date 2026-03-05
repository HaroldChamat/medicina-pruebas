@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-heart me-2"></i> Lista de Pacientes
            </h4>
            <p class="text-muted small mb-0">Gestión de pacientes registrados en el sistema.</p>
        </div>
        <a href="/login" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Buscador --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <input type="text" id="buscador" class="form-control"
                   placeholder="🔍 Buscar por nombre, apellido, RUT o email...">
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-uppercase small text-white"
                           style="background-color: #0d3b6e;">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>RUT</th>
                            <th>Teléfono</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        @foreach($pacientes as $paciente)
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>{{ $paciente->name }}</td>
                                <td>{{ $paciente->Apellidos }}</td>
                                <td>{{ $paciente->email }}</td>
                                <td><span class="badge bg-secondary">{{ $paciente->Rut }}</span></td>
                                <td>{{ $paciente->telefono }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm btnEditar"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }}"
                                        data-apellidos="{{ $paciente->Apellidos }}"
                                        data-email="{{ $paciente->email }}"
                                        data-telefono="{{ $paciente->telefono }}">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm btnEliminar"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }} {{ $paciente->Apellidos }}">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil me-2"></i> Editar Paciente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Apellidos</label>
                        <input type="text" id="edit_apellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" id="edit_telefono" class="form-control">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminar --}}
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de eliminar a <strong id="nombreEliminar"></strong>?
                Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger rounded-pill" id="btnConfirmarEliminar">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    let userIdEliminar = null;

    // Buscador
    $('#buscador').on('keyup', function () {
        const texto = $(this).val().toLowerCase();
        $('#tablaBody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(texto));
        });
    });

    // Abrir editar
    $(document).on('click', '.btnEditar', function () {
        const btn = $(this);
        $('#edit_id').val(btn.data('id'));
        $('#edit_name').val(btn.data('name'));
        $('#edit_apellidos').val(btn.data('apellidos'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_telefono').val(btn.data('telefono'));
        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });

    // Guardar edición
    $('#formEditar').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/usuario/' + $('#edit_id').val(),
            method: 'POST',
            data: {
                _token:    $('meta[name="csrf-token"]').attr('content'),
                _method:   'PUT',
                name:      $('#edit_name').val(),
                Apellidos: $('#edit_apellidos').val(),
                email:     $('#edit_email').val(),
                telefono:  $('#edit_telefono').val(),
            },
            success: function () {
                mostrarToast('Paciente actualizado correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            }
        });
    });

    // Abrir eliminar
    $(document).on('click', '.btnEliminar', function () {
        userIdEliminar = $(this).data('id');
        $('#nombreEliminar').text($(this).data('name'));
        new bootstrap.Modal(document.getElementById('modalEliminar')).show();
    });

    // Confirmar eliminar
    $('#btnConfirmarEliminar').on('click', function () {
        $.ajax({
            url: '/usuario/' + userIdEliminar,
            method: 'POST',
            data: {
                _token:  $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE',
            },
            success: function () {
                mostrarToast('Paciente eliminado correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al eliminar', 'danger');
            }
        });
    });

});
</script>
@endsection