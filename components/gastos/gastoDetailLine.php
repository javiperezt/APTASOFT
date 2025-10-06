<tr>
    <td>
        <form class="m-0" action="" method="post">
            <select onchange="gastoAssignPartida(this.value,<?= $id_gasto_linea; ?>)" name="id_partida"
                    class="selectizeSearch">
                <option selected value="">Ninguna partida</option>
                <?php
                $getPartidas = $mysqli->query("SELECT * FROM presupuestos_partidas order by id_capitulo");
                while ($row = $getPartidas->fetch_assoc()) {
                    $s_id_presupuestos_partidas = $row['id'];
                    $s_id_presupuesto = $row['id_presupuesto'];
                    $s_partida = $row['partida'];
                    $s_id_capitulo = $row['id_capitulo'];

                    if ($s_id_capitulo) {
                        $getCapitulos = $mysqli->query("SELECT * FROM capitulos where id=$s_id_capitulo");
                        while ($row = $getCapitulos->fetch_assoc()) {
                            $s_capitulo = $row['capitulo'];
                        }
                    }

                    $getPresupuesto = $mysqli->query("SELECT * FROM presupuestos where id=$s_id_presupuesto");
                    while ($row = $getPresupuesto->fetch_assoc()) {
                        $s_id_obra = $row['id_obra'];
                    }

                    $selected = "";
                    if ($s_id_presupuestos_partidas == $id_presupuestos_partidas) {
                        $selected = "selected";
                    }

                    // solo mostramos las partidas que estan asignadas a la obra a la cual va asignada el gasto
                    if ($id_obra == $s_id_obra) {
                        echo "<option $selected value='$s_id_presupuestos_partidas'>$s_capitulo - $s_partida</option>";
                    }
                }
                ?>
            </select>
        </form>
    </td>
    <td><input onfocusout="updateGenerico('gastos_lineas','concepto',<?= $id_gasto_linea; ?>,this.value)"
               type="text" class="form-control form-control-sm" placeholder="Concepto"
               value="<?= $concepto; ?>"></td>
    <td><input onfocusout="updateGenerico('gastos_lineas','descripcion',<?= $id_gasto_linea; ?>,this.value)"
               type="text" class="form-control form-control-sm" placeholder="Descripcion"
               value="<?= $descripcion; ?>"></td>
    <td><input onfocusout="updateGenerico('gastos_lineas','cantidad',<?= $id_gasto_linea; ?>,this.value)"
               type="number" min="0" step="any" class="form-control form-control-sm"
               value="<?= $cantidad; ?>"></td>
    <td><input onfocusout="updateGenerico('gastos_lineas','precio',<?= $id_gasto_linea; ?>,this.value)"
               type="number" min="0" step="any" class="form-control form-control-sm" placeholder="€"
               value="<?= $precio; ?>"></td>
    <td><input onfocusout="updateGenerico('gastos_lineas','descuento',<?= $id_gasto_linea; ?>,this.value)"
               type="number" min="0" max="100" class="form-control form-control-sm" placeholder="%"
               value="<?= $descuento; ?>"></td>
    <td>
        <select onchange="updateGenerico('gastos_lineas','id_iva',<?= $id_gasto_linea; ?>,this.value)"
                class="form-select form-select-sm">
            <option selected disabled hidden>IVA</option>
            <?php
            $c6 = $mysqli->query("SELECT * FROM iva order by iva desc");
            while ($row = $c6->fetch_assoc()) {
                $s_id_iva = $row['id'];
                $s_iva = $row['iva'];

                $selected = "";
                if ($id_iva == $s_id_iva) {
                    $selected = "selected";
                }
                echo "<option $selected value='$s_id_iva'>$s_iva%</option>";
            }
            ?>
        </select>
    </td>
    <td id="subtotal<?= $id_gasto_linea; ?>"><?= formatCurrency($subtotal); ?></td>
    <td id="total<?= $id_gasto_linea; ?>"><?= formatCurrency($total); ?></td>
    <td><a class="pointer" onclick="gastoDeleteLine(<?= $id_gasto_linea; ?>)"><i
                class="bi bi-x fs-5"></i></a></td>
</tr>