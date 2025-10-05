<tr>
    <td>
        <input <?= $is_checked ? "checked" : "" ?>
                onchange="updateGenerico('presupuestos_subpartidas','is_checked',<?= $id_presupuestos_subpartidas; ?>, this.checked ? 1 : 0)"
                type="checkbox" class="btn-check"
                id="btn-check-outlined<?= $id_presupuestos_subpartidas; ?>" autocomplete="off">
        <label style="padding: 3px !important;" class="btn btn-outline-primary btn-sm"
               for="btn-check-outlined<?= $id_presupuestos_subpartidas; ?>"><i
                    class="bi bi-check-lg"></i></label>
    </td>
    <td><?= $concepto_subpartida ? "$concepto_subpartida" : "-"; ?></td>
    <td><?= $descripcion_subpartida ? "$descripcion_subpartida" : "-"; ?></td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div>
                <button data-bs-toggle="modal"
                        data-bs-target="#obraAssignEmpleadoToTask<?= $id_presupuestos_subpartidas; ?>"
                        class="btn btn-outline-secondary d-flex align-items-center btn-sm p-0 justify-content-center"
                        style="border-radius: 50%;height: 25px;width: 25px"><i
                            class="bi bi-person-fill-add fs-6"></i></button>
            </div>
            <div class="row row-cols-2 g-1">
                <?php
                $c9 = $mysqli->query("SELECT * FROM obras_trabajadores_subpartidas where id_presupuesto=$id_presupuesto and id_presupuestos_subpartidas='$id_presupuestos_subpartidas' and id_obra=$id_obra");
                while ($row = $c9->fetch_assoc()) {
                    $id_empleado_asignado = $row['id_empleado'];

                    if ($id_empleado_asignado) {
                        $c10 = $mysqli->query("SELECT * FROM empleados where id=$id_empleado_asignado");
                        while ($row = $c10->fetch_assoc()) {
                            $nombre_empleado_asignado = $row['nombre'];
                        }
                    }
                    ?>
                    <div class="col">
                                <span class="badge rounded-pill text-bg-light w-100"
                                      style="font-size: 12px "><?= $nombre_empleado_asignado; ?></span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </td>
    <td>
        <input onchange="updateGenerico('presupuestos_subpartidas','fecha_vencimiento',<?= $id_presupuestos_subpartidas; ?>, this.value)"
               type="date" class="form-control form-control-sm" value="<?= $fecha_vencimiento_subpartida; ?>"></td>
    <td>
        <input onchange="updateGenerico('presupuestos_subpartidas','fecha_prox_intervencion',<?= $id_presupuestos_subpartidas; ?>, this.value)"
               type="date" class="form-control form-control-sm" value="<?= $fecha_prox_intervencion; ?>"></td>
    <td><?= $horas; ?>:<?= $minutos; ?><?= $simbolo_ud_material; ?></td>
    <td><?= $total_horasRegistradas; ?>h</td>
    <td>
        <a href="obraPlanningDetailPartes.php?id_presupuestos_subpartidas=<?= $id_presupuestos_subpartidas; ?>&id_obra=<?= $id_obra; ?>&id_presupuesto=<?= $id_presupuesto; ?>&id_presupuestos_partidas=<?= $id_presupuestos_partidas; ?>"><i
                    class="bi bi-arrow-right fs-5"
                    stylex="color: #D2D5DA"></i></a>
    </td>
</tr>