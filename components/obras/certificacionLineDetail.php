<tr >
    <td><?= $capitulo; ?></td>
    <td><?= $partida; ?></td>
    <td><?= $cantidad; ?></td>
    <td><?= $precio; ?>€</td>
    <td ><?= $cantidad_cert_anterior; ?></td>
    <td class="text-primary fw-bold"><?= $restante; ?></td>
    <td width="100px"><input
            onchange="updateGenerico('certificaciones_partidas','cantidad_certificada',<?= $id_certificaciones_partidas; ?>,this.value)"
            type="number" min="0" class="form-control form-control-sm" placeholder=""
            value="<?= $cantidad_certificada; ?>"></td>
    <td width="100px"><input
            onchange="updateGenerico('certificaciones_partidas','precio_certificado',<?= $id_certificaciones_partidas; ?>,this.value)"
            type="number" min="0" class="form-control form-control-sm" placeholder="€"
            value="<?= $precio_certificado; ?>"></td>
    <td  class="fw-bold"><?= round ($total_cert,2); ?>€</td>
    <td  width="150px">
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-label="Example with label"
                 style="width: <?= $procentajeCert; ?>%;"
                 aria-valuenow="<?= $procentajeCert; ?>" aria-valuemin="0"
                 aria-valuemax="100"><?= $procentajeCert; ?>%
            </div>
        </div>
    </td>
    <td><a class="pointer" onclick="certificacionDeleteLine(<?= $id_certificaciones_partidas; ?>)"><i
                class="bi bi-x fs-5 "></i></a></td>
</tr>