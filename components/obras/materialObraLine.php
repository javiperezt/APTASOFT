<tr class="<?php if (!$is_checked) {
    if ($today > $fecha_vencimiento) {
        echo "table-danger";
    }
} ?>">
    <td>
        <input onchange="updateGenerico('presupuestos_subpartidas','is_checked',<?= $id_presupuestos_subpartidas; ?>, this.checked ? 1 : 0)" <?= $is_checked ? "checked" : "" ?>
               type="checkbox" class="btn-check"
               id="btn-check-outlined<?= $id_presupuestos_subpartidas; ?>" autocomplete="off">
        <label style="padding: 3px !important;" class="btn btn-outline-primary btn-sm"
               for="btn-check-outlined<?= $id_presupuestos_subpartidas; ?>"><i
                    class="bi bi-check-lg"></i></label>
    </td>
    <td><?= $capitulo; ?></td>
    <td><?= $partida; ?></td>
    <td><?= $concepto_material; ?></td>
    <td><?= round($cantidad_material*$cantidad_partida,2); ?></td>
    <td><?= $simbolo_ud_material; ?></td>
    <td><?= round($importe,2); ?></td>
    <td><?= round($cantidadTotal,2); ?>€</td>
    <td>
        <input onchange="updateGenerico('presupuestos_subpartidas','fecha_vencimiento',<?= $id_presupuestos_subpartidas; ?>,this.value)"
               type="date" class="form-control form-control-sm"
               value="<?= $fecha_vencimiento; ?>">
    </td>
</tr>