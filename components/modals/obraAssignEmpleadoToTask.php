<!---------- MODAL ---------->
<div class="modal fade" id="obraAssignEmpleadoToTask<?= $id_presupuestos_subpartidas; ?>" tabindex="-1"
     aria-labelledby="obraAssignEmpleadoToTask<?= $id_presupuestos_subpartidas; ?>"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obras/obraAssignEmpleadoToTask.php" autocomplete="off" method="post">
                <p class="text-black fs-5 fw-bold">Seleccionar empleados</p>
                <!--<div class="mt-3">
                    <input type="text" class="form-control" placeholder="Buscar empleado">
                </div>-->
                <input type="hidden" name="id_obra" value="<?= $id_obra; ?>">
                <input type="hidden" name="id_presupuesto" value="<?= $id_presupuesto; ?>">
                <input type="hidden" name="id_presupuestos_partidas" value="<?= $id_presupuestos_partidas; ?>">
                <div class="mt-3">
                    <ul class="list-group">
                        <?php
                        $c7 = $mysqli->query("SELECT * FROM empleados where is_active=1");
                        while ($row = $c7->fetch_assoc()) {
                            $id_empleado = $row['id'];
                            $nombre_empleado = $row['nombre'];

                            $c22 = $mysqli->query("SELECT * FROM obras_trabajadores_subpartidas where id_presupuesto=$id_presupuesto and id_presupuestos_subpartidas=$id_presupuestos_subpartidas and id_obra=$id_obra and id_empleado='$id_empleado'");
                            $result = $c22->num_rows;
                            ?>
                            <li class="list-group-item">
                                <input type="hidden" value="<?= $id_presupuestos_subpartidas; ?>" name="id_presupuestos_subpartidas_empty">
                                <input type="hidden" value="<?= $id_empleado; ?>" name="id_empleado_empty">
                                <input <?= $result > 0 ? "checked" : ""; ?> class="form-check-input me-1"
                                                                            type="checkbox"
                                                                            name="name[<?= $id_empleado . "-" . $id_presupuestos_subpartidas; ?>]"
                                                                            id="thirdCheckboxStretched<?= $id_empleado; ?><?= $id_presupuestos_subpartidas; ?>">
                                <label class="form-check-label stretched-link letraPeq"
                                       for="thirdCheckboxStretched<?= $id_empleado; ?><?= $id_presupuestos_subpartidas; ?>"><?= $nombre_empleado; ?></label>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Guardar">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->