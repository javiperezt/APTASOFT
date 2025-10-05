<!---------- MODAL ---------->
<div class="modal fade" id="imputarHoras<?= $id_obras_trabajadores_subpartidas; ?>" tabindex="-1"
     aria-labelledby="imputarHoras<?= $id_obras_trabajadores_subpartidas; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obraAddParte.php" method="post">
                <p class="text-black fs-4 fw-bold">Registrar horas</p>
                <p class="mt-2">Registra la cantidad de horas que has trabajado en esta tarea. </p>
                <p class="mt-2"><code>Ejemplo: 2h 30min = 02:30</code></p>
                <div class="row mt-3" style="row-gap: 10px">
                    <div class="col-12">
                        <div class="form-floating">
                            <input required name="horas" type="time" class="form-control" placeholder="Horas">
                            <label>Horas</label>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id_empleado" value="<?= $ID_USER; ?>">
                <input type="hidden" name="id_presupuesto" value="<?= $id_presupuesto; ?>">
                <input type="hidden" name="id_obra" value="<?= $id_obra; ?>">
                <input type="hidden" name="id_presupuestos_subpartidas" value="<?= $id_presupuestos_subpartidas; ?>">
                <input type="hidden" name="id_presupuestos_partidas" value="<?= $id_presupuesto_partidas; ?>">
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Registrar horas">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->