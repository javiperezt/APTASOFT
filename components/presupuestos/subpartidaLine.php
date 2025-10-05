<tr>
    <td>
        <select onchange="updateGenerico('presupuestos_subpartidas','id_categoria',<?= $id_presupuestos_subpartidas; ?>,this.value)"
                class="form-select form-select-sm">
            <option selected disabled hidden>Categoría</option>
            <?php
            $c4 = $mysqli->query("SELECT * FROM categorias_subpartidas");
            while ($row = $c4->fetch_assoc()) {
                $id_categorias_subpartidas = $row['id'];
                $codigo = $row['codigo'];
                $categoria = $row['categoria'];

                $selected = "";
                if ($id_categoria == $id_categorias_subpartidas) {
                    $selected = "selected";
                }
                echo "<option $selected value='$id_categorias_subpartidas'>$codigo</option>";
            }
            ?>
        </select>
    </td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_subpartidas','concepto',<?= $id_presupuestos_subpartidas; ?>,this.value)"
               type="text" class="form-control form-control-sm" placeholder="Concepto"
               value="<?= $concepto; ?>"></td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_subpartidas','descripcion',<?= $id_presupuestos_subpartidas; ?>,this.value)"
               type="text" class="form-control form-control-sm" placeholder="Descripcion"
               value="<?= $descripcion_subpartida; ?>"></td>
    <td>
        <select onchange="updateGenerico('presupuestos_subpartidas','id_unidad',<?= $id_presupuestos_subpartidas; ?>,this.value)"
                class="form-select form-select-sm">
            <option selected disabled hidden>Ud</option>
            <?php
            $c5 = $mysqli->query("SELECT * FROM unidades");
            while ($row = $c5->fetch_assoc()) {
                $id_unidades_subpartida = $row['id'];
                $unidad = $row['unidad'];
                $simbolo = $row['simbolo'];

                $selected = "";
                if ($id_unidades_subpartida == $id_unidad_subpartida) {
                    $selected = "selected";
                }
                echo "<option $selected value='$id_unidades_subpartida'>$simbolo</option>";
            }
            ?>
        </select>
    </td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_subpartidas','cantidad',<?= $id_presupuestos_subpartidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $cantidad; ?>"></td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_subpartidas','precio',<?= $id_presupuestos_subpartidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" placeholder="€"
               value="<?= $precio; ?>"></td>
    <td>
        <select onchange="updateGenerico('presupuestos_subpartidas','id_iva',<?= $id_presupuestos_subpartidas; ?>,this.value)"
                class="form-select form-select-sm">
            <option selected disabled hidden>IVA</option>
            <?php
            $c6 = $mysqli->query("SELECT * FROM iva order by iva desc");
            while ($row = $c6->fetch_assoc()) {
                $id_iva = $row['id'];
                $iva = $row['iva'];

                $selected = "";
                if ($id_iva_subpartida == $id_iva) {
                    $selected = "selected";
                }
                echo "<option $selected value='$id_iva'>$iva%</option>";
            }
            ?>
        </select>
    </td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_subpartidas','descuento',<?= $id_presupuestos_subpartidas; ?>,this.value)"
               type="number" min="0" max="100" class="form-control-sm form-control" placeholder="%"
               value="<?= $descuento; ?>"></td>
    <td id="subtotal<?= $id_presupuestos_subpartidas; ?>"><?= $subtotal; ?>€</td>
    <td id="total<?= $id_presupuestos_subpartidas; ?>"><?= $total; ?>€</td>
    <td><a onclick="presupuestoDeleteSubpartida(<?= $id_presupuestos_subpartidas; ?>)" class="pointer"><i
                    class="bi bi-x fs-5 "></i></a></td>
</tr>