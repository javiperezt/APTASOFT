<tr style="cursor: pointer;" onclick="location.href='gastoDetail.php?id_gasto=<?= $id_gasto; ?>'">
    <td class="text-uppercase"><?= $empresa; ?></td>
    <td><?= $fecha_inicio; ?></td>
    <td><?= $fecha_vencimiento; ?></td>
    <td><?= $codigo; ?></td>
    <td><?= $nombre_contacto; ?></td>
    <td><a class="link-primary" href="obraDetail.php?id_obra=<?= $id_obra; ?>"><?= $titulo_obra; ?></a></td>
    <td><?= $categoria_gasto; ?></td>
    <td><?= $subtotal; ?>€</td>
    <td><?= $total; ?>€</td>
    <td><span class="badge <?= $classEtiq; ?>"><?= $estado; ?></span></td>
    <td><a href="gastoDetail.php?id_gasto=<?= $id_gasto; ?>"><i class="bi bi-arrow-right fs-5"
                                                                style="color: #D2D5DA"></i></a></td>
</tr>