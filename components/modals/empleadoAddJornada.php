<!---------- MODAL ---------->
<div class="modal fade" id="empleadoAddJornada" tabindex="-1" aria-labelledby="empleadoAddJornada"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/empleados/empleadoAddJornada.php" method="POST" autocomplete="off">
                <p class="text-black fs-5 fw-bold">Añadir jornada</p>
                <div class="mt-2">
                    <div class="row mt-2 gx-2 gy-2">
                        <div class="col-12">
                            <div class="form-floating">
                                <input required name="fecha" type="date" class="form-control" placeholder="Fecha">
                                <label>Fecha</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input required name="hora_inicio" type="time" class="form-control" placeholder="Hora inicio">
                                <label>Hora inicio</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input required name="hora_fin" type="time" class="form-control" placeholder="Hora fin">
                                <label>Hora fin</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id_empleado" value="<?= $id_empleado; ?>">
                    <input type="submit" class="btn btn-primary w-100 mt-3" value="Añadir">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->