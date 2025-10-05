<tr>
    <td><input onchange="updateGenerico('gastos_pagos','fecha',<?= $id_pago; ?>,this.value)" class="form-control" type="date" value="<?= $fecha; ?>"></td>
    <td><?= $comentario; ?></td>
    <td class="text-capitalize"><?= $forma_pago ? "$forma_pago" : "-"; ?></td>
    <td id="estado<?= $id_pago; ?>">
        <select onchange="updateGenerico('gastos_pagos','estado',<?= $id_pago; ?>,this.value)"
                class="form-select <?= $estado == "pendiente" ? "text-bg-warning" : "text-bg-primary"; ?>"
                name="estado">
            <option selected disabled hidden value="">Estado</option>
            <option <?= $estado == "pagado" ? "selected" : ""; ?> value="pagado">Pagado</option>
            <option <?= $estado == "pendiente" ? "selected" : ""; ?> value="pendiente">Pendiente</option>
        </select>
    </td>
    <td><?= $importe; ?></td>
    <td><a onclick="gastoDeletePago(<?= $id_pago; ?>)" class="pointer"><i class="bi bi-x fs-5 "></i></a></td>
</tr>