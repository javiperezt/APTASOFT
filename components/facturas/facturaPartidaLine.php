<tr>
    <td>
        <textarea onfocusout="updateGenerico('facturas_partidas','partida',<?= $id_facturas_partidas; ?>,this.value)"
                  class="form-control form-control-sm" placeholder="Partida"
        ><?= $partida; ?></textarea>
    </td>
    <td>
        <textarea
                onfocusout="updateGenerico('facturas_partidas','descripcion',<?= $id_facturas_partidas; ?>,this.value)"
                class="form-control form-control-sm" placeholder="Descripcion"><?= $descripcion; ?></textarea>
    </td>
    <td><select onchange="updateGenerico('facturas_partidas','id_unidad',<?= $id_facturas_partidas; ?>,this.value)"
                class="form-select form-select-sm">
            <option selected disabled hidden>Ud</option>
            <?php
            $cUd = $mysqli->query("SELECT * FROM unidades");
            while ($row = $cUd->fetch_assoc()) {
                $s_id_unidad = $row['id'];
                $s_unidad = $row['unidad'];
                $s_simbolo = $row['simbolo'];

                $selected = "";
                if ($s_id_unidad == $id_unidad) {
                    $selected = "selected";
                }
                echo "<option $selected value='$s_id_unidad'>$s_simbolo</option>";
            }
            ?>
        </select></td>
    <td>
        <input onfocusout="updateGenerico('facturas_partidas','cantidad',<?= $id_facturas_partidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $cantidad; ?>"></td>
    <td>
        <input onfocusout="updateGenerico('facturas_partidas','precio',<?= $id_facturas_partidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $precio; ?>"></td>
    <td>
        <input onfocusout="updateGenerico('facturas_partidas','descuento',<?= $id_facturas_partidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $descuento; ?>"></td>
    <td>
        <select onchange="updateGenerico('facturas_partidas','id_iva',<?= $id_facturas_partidas; ?>,this.value)"
                class="form-select form-select-sm">
            <option selected disabled hidden>IVA</option>
            <?php
            $cIva = $mysqli->query("SELECT * FROM iva order by iva desc");
            while ($row = $cIva->fetch_assoc()) {
                $s_id_iva = $row['id'];
                $s_iva = $row['iva'];

                $selected = "";
                if ($s_id_iva == $id_iva) {
                    $selected = "selected";
                }
                echo "<option $selected value='$s_id_iva'>$s_iva%</option>";
            }
            ?>
        </select>
    </td>
    <td id="subtotal<?= $id_facturas_partidas; ?>"><?= $subtotal; ?>€</td>
    <td id="total<?= $id_facturas_partidas; ?>"><?= $total; ?>€</td>
    <td><a onclick="facturaDeletePartida(<?= $id_facturas_partidas; ?>)" class="pointer"><i
                    class="bi bi-x fs-5 "></i></a></td>
</tr>