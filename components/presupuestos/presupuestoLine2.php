<tr>
    <td><?= $fecha_inicio; ?></td>
    <td><?= $fecha_vencimiento ? "$fecha_vencimiento" : "-"; ?></td>
    <td class="text-uppercase"><?= $ref; ?></td>
    <td class="text-uppercase"><?= $nombre_contacto; ?></td>
    <td class="text-uppercase"><a class="link-primary" href="obraDetail.php?id_obra=<?=$id_obra;?>"><?= $titulo_obra; ?></a></td>
    <td><?= $subtotal; ?>€</td>
    <td><?= $total; ?>€</td>
    <td><span class="badge <?= $classEtiq; ?>"><?= $estado; ?></span></td>
    <td><a href="presupuestoDetail2.php?id_presupuesto=<?= $id_presupuesto; ?>&id_obra=<?=$id_obra;?>"><i
                class="bi bi-arrow-right fs-5"
                style="color: #D2D5DA"></i></a>
    </td>
</tr>