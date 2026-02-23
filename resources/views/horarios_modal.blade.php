<div class="modal fade" id="modalHorario">
    <div class="modal-dialog">
        <form id="formHorario">
            @csrf
            <input type="hidden" name="medico_id" id="medico_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Hora inicio</label>
                    <input type="time" name="hora_inicio" class="form-control mb-2" required>

                    <label>Hora fin</label>
                    <input type="time" name="hora_fin" class="form-control mb-2" required>

                    <label>Almuerzo inicio</label>
                    <input type="time" name="almuerzo_inicio" class="form-control mb-2">

                    <label>Almuerzo fin</label>
                    <input type="time" name="almuerzo_fin" class="form-control mb-2">

                    <div class="mb-3">
                        <label class="form-label">Hora de atención (minutos)</label>
                        <select name="hora_atencion" class="form-select" required>
                            <option value="20">20 minutos</option>
                            <option value="30">30 minutos</option>
                            <option value="40">40 minutos</option>
                            <option value="60">60 minutos</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
