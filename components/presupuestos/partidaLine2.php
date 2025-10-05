<tr>
    <td><?= $partida; ?></td>
    <td><?= $descripcion ? "$descripcion" : "-"; ?></td>
    <td><?= $unidad ? "$unidad" : "-"; ?></td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_partidas','total_x_cantidad',<?= $id_presupuestos_partidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $cantidad; ?>"></td>
    <td><?= $subtotal; ?>€</td>
    <td id="subtotal_x_cantidad<?= $id_presupuestos_partidas; ?>"><?= $subtotal_x_cantidad; ?>€</td>
    <!-- <td id="total_x_cantidad<?= $id_presupuestos_partidas; ?>"><?= $total_x_cantidad; ?>€</td>-->
    <td>
        <a href="presupuestoDetailPartida.php?id_presupuestos_partidas=<?= $id_presupuestos_partidas; ?>&id_obra=<?= $id_obra; ?>"><i
                    class="bi bi-arrow-right fs-5"
                    style="color: #D2D5DA"></i></a>
    </td>
</tr>