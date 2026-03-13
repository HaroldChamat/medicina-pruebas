<div class="modal fade" id="modalHorario">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formHorario">
            @csrf
            <input type="hidden" name="medico_id" id="medico_id">

            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #1a7a4a;">
                    <h5 class="modal-title fw-bold" id="tituloModal">
                        <i class="bi bi-plus-circle me-2"></i> Definir Horario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-hourglass-split me-1"></i> Hora inicio
                            </label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-hourglass-bottom me-1"></i> Hora fin
                            </label>
                            <input type="time" name="hora_fin" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-cup-hot me-1"></i> Almuerzo inicio
                            </label>
                            <input type="time" name="almuerzo_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-cup-hot me-1"></i> Almuerzo fin
                            </label>
                            <input type="time" name="almuerzo_fin" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">
                                <i class="bi bi-stopwatch me-1"></i> Duración por cita
                            </label>
                            <select name="hora_atencion" class="form-select" required>
                                <option value="" disabled selected>Seleccione duración</option>
                                <option value="20">20 minutos</option>
                                <option value="30">30 minutos</option>
                                <option value="40">40 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60">60 minutos</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-calendar-week me-1"></i> Días de atención
                    </label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach(['lunes','martes','miercoles','jueves','viernes'] as $dia)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    name="dias_semana[]"
                                    value="{{ $dia }}"
                                    id="dia_crear_{{ $dia }}"
                                    checked>
                                <label class="form-check-label text-capitalize"
                                    for="dia_crear_{{ $dia }}">
                                    {{ ucfirst($dia) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill">
                        <i class="bi bi-save me-1"></i> Guardar horario
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>