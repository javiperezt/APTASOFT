<!---------- MODAL ---------->
<div class="modal fade" id="obraAddParteToPlanning" tabindex="-1" aria-labelledby="obraAddParteToPlanning"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obras/obrasAddParte.php" method="POST" autocomplete="off">
                <p class="text-black fs-5 fw-bold">Añadir parte</p>
                <div class="mt-2">
                    <div class="form-floating">
                        <select required class="form-select" name="id_empleado">
                            <option selected disabled hidden>Empleado</option>
                            <?php
                            $c4 = $mysqli->query("SELECT * FROM empleados WHERE is_active=1");
                            while ($row = $c4->fetch_assoc()) {
                                $nombre_empleado = $row['nombre'];
                                $id_empleado = $row['id'];

                                echo "<option value='$id_empleado'>$nombre_empleado</option>";
                            }

                            ?>
                        </select>
                        <label>Empleado</label>
                    </div>
                    <div class="row mt-2 gx-2">
                        <div class="col-6">
                            <div class="form-floating">
                                <input required name="horas" type="time" class="form-control" placeholder="Horas">
                                <label>Horas</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input required name="fecha" type="date" class="form-control" placeholder="Fecha">
                                <label>Fecha</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id_presupuesto" value="<?= $id_presupuesto; ?>">
                    <input type="hidden" name="id_obra" value="<?= $id_obra; ?>">
                    <input type="hidden" name="id_presupuestos_subpartidas"
                           value="<?= $id_presupuestos_subpartidas; ?>">
                    <input type="hidden" name="id_presupuestos_partidas" value="<?= $id_presupuestos_partidas; ?>">
                    <input type="submit" class="btn btn-primary w-100 mt-3" value="Añadir">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->