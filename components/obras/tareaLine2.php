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
    <td><?= $concepto_subpartida; ?></td>
    <td><?= $descripcion_subpartida; ?></td>
    <td>
        <select onchange="updateGenerico('presupuestos_subpartidas','id_contacto',<?= $id_presupuestos_subpartidas; ?>, this.value)"
                class="selectizeSearch" name="" id="">
            <option value=""></option>
            <?php
            $c555 = $mysqli->query("SELECT * FROM contactos WHERE is_active=1");
            while ($row = $c555->fetch_assoc()) {
                $id_contactos = $row['id'];
                $nombre_contacto = $row['nombre'];
                $selected = "";
                if ($id_contacto == $id_contactos) {
                    $selected = "selected";
                }
                echo " <option $selected value='$id_contactos'>$nombre_contacto</option>";
            }
            ?>
        </select>
    </td>
    <td>
        <input onchange="updateGenerico('presupuestos_subpartidas','fecha_vencimiento',<?= $id_presupuestos_subpartidas; ?>, this.value)"
               type="date" class="form-control-sm form-control"
               value="<?= $fecha_vencimiento_subpartida; ?>"></td>
    <!--<td>Ppt</td>-->
</tr>