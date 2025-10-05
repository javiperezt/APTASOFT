<tr>
    <td>
        <select onchange="updateGenerico('gestor_subpartidas','id_categoria',<?= $id_subpartida; ?>,this.value)"
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
        <input onfocusout="updateGenerico('gestor_subpartidas','concepto',<?= $id_subpartida; ?>,this.value)"
               type="text" class="form-control form-control-sm" placeholder="Concepto"
               value="<?= $concepto; ?>"></td>
    <td>
        <input onfocusout="updateGenerico('gestor_subpartidas','descripcion',<?= $id_subpartida; ?>,this.value)"
               type="text" class="form-control form-control-sm" placeholder="Descripcion"
               value="<?= $descripcion_subpartida; ?>"></td>
    <td>
        <select onchange="updateGenerico('gestor_subpartidas','id_unidad',<?= $id_subpartida; ?>,this.value)"
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
        <input oninput="updateGenerico('gestor_subpartidas','cantidad',<?= $id_subpartida; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $cantidad; ?>"></td>
    <td><input oninput="updateGenerico('gestor_subpartidas','precio',<?= $id_subpartida; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" placeholder="€"
               value="<?= $precio; ?>"></td>
    <td>
        <select onchange="updateGenerico('gestor_subpartidas','id_iva',<?= $id_subpartida; ?>,this.value)"
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
    <td id="subtotal<?= $id_subpartida; ?>"><?= $subtotal; ?>€</td>
    <td id="total<?= $id_subpartida; ?>"><?= $total; ?>€</td>
    <td><a onclick="subpartidaDeleteLine(<?= $id_subpartida; ?>)" class="pointer"><i
                class="bi bi-x fs-5 "></i></a></td>
</tr>