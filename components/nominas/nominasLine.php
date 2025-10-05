<tr>
    <td><?= $fecha ? "$fecha" : "-"; ?></td>
    <td><?= $comentario ? "$comentario" : "-"; ?></td>
    <td><?= $nombre_empleado ? "$nombre_empleado" : "-"; ?></td>
    <td><?= $salario ? "$salario" : "-"; ?></td>
    <td><?= $total_ss ? "$total_ss" : "-"; ?></td>
    <td><?= $gastos_ss_empresa ? "$gastos_ss_empresa" : "-"; ?></td>
    <td><?= $irpf ? "$irpf" : "-"; ?></td>
    <td><span class="badge text-capitalize <?= $classEtiq; ?>"><?= $estado ?></span></td>
    <td><a href="nominaDetail.php?id_nomina=<?= $id_nomina; ?>"><i class="bi bi-arrow-right fs-5"
                                                                   style="color: #D2D5DA"></i></a>
    </td>
</tr>

