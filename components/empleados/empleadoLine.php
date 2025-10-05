<tr>
    <td class="text-uppercase"><?= $nombre ? "$nombre" : "-"; ?></td>
    <td class="text-uppercase"><?= $nif ? "$nif" : "-"; ?></td>
    <td class="text-uppercase"><?= $correo; ?></td>
    <td class="text-uppercase"><?= $tel ? "$tel" : "-"; ?></td>
    <td><span class="badge <?= $classEtiq; ?>"><?= $is_active ? "Activo" : "Inactivo"; ?></span></td>
    <td><a href="empleadoDetail.php?id_empleado=<?= $id_empleado; ?>"><i class="bi bi-arrow-right fs-5"
                                                                         style="color: #D2D5DA"></i></a>
    </td>
</tr>