<tr>
    <td class="text-uppercase"><?= $empresa; ?></td>
    <td><?= $titulo; ?></td>
    <td><?= $nombre_contacto; ?></td>
    <td><?= $fecha_inicio ? "$fecha_inicio" : "-"; ?></td>
    <td><?= $fecha_fin ? "$fecha_fin" : "-"; ?></td>
    <td>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-label="Example with label"
                 style="width: <?= $porcentaje; ?>%;"
                 aria-valuenow="<?= $porcentaje; ?>" aria-valuemin="0"
                 aria-valuemax="100"><?= $porcentaje; ?>%
            </div>
        </div>
    </td>
    <td><span class="badge <?= $classEtiq; ?>"><?= $estado; ?></span></td>
    <td><a href="obraDetail.php?id_obra=<?= $id_obra; ?>"><i class="bi bi-arrow-right fs-5"
                                                             style="color: #D2D5DA"></i></a></td>
</tr>