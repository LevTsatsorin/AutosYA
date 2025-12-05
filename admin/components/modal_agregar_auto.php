<!-- Modal Agregar Auto -->
<div class="modal fade" id="modalAgregarAuto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/AutosYA/admin/abm_autos/alta_auto.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Agregar Nuevo Auto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="mb-3">
                        <label for="anio" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio" name="anio"
                            min="1900" max="2026" required>
                    </div>
                    <div class="mb-3">
                        <label for="patente" class="form-label">Patente</label>
                        <input type="text" class="form-control" id="patente" name="patente" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_por_dia" class="form-label">Precio por Día ($)</label>
                        <input type="number" class="form-control" id="precio_por_dia" name="precio_por_dia"
                            step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Auto</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Auto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
