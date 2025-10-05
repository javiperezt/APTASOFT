<tr>
    <td><a class="pointer text-decoration-underline text-primary" target="_blank" href="presupuestoDetail.php?id_presupuesto=<?=$id_presupuesto;?>"><?= $ref; ?></a></td>
    <td><?= $capitulo; ?></td>
    <td><?= $partida; ?></td>
    <td><?= $fecha_inicio ? "$fecha_inicio" : "-"; ?></td>
    <td><?= $fecha_vencimiento ? "$fecha_vencimiento" : "-"; ?></td>
    <td>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-label="Example with label"
                 style="width: <?= $porcentaje; ?>%;"
                 aria-valuenow="<?= $porcentaje; ?>" aria-valuemin="0"
                 aria-valuemax="100"><?= $porcentaje; ?>%
            </div>
        </div>
    </td>
    <td>
        <a href="obraPlanningDetail.php?id_presupuestos_partidas=<?= $id_presupuestos_partidas; ?>&id_obra=<?= $id_obra; ?>"><i
                    class="bi bi-arrow-right fs-5"
                    style="color: #D2D5DA"></i></a></td>
</tr>