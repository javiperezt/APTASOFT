<tr>
    <td>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-x-lg"
               style="color: #D2D5DA; cursor: pointer; font-size: 0.85rem;"
               title="Eliminar partida"
               onclick="deletePartida(<?= $id_presupuestos_partidas; ?>)"></i>
            <span><?= $partida; ?></span>
        </div>
    </td>
    <td>
        <?php if (!empty($categorias_badges)): ?>
            <div class="d-flex gap-1 flex-wrap">
                <?php
                // Esquema de colores por categoría
                $color_map = [
                    'MO' => '#6366f1',    // Índigo para Mano de Obra
                    'MAT' => '#f59e0b',   // Ámbar para Material
                    'EXT' => '#10b981'    // Esmeralda para Externos
                ];

                foreach ($categorias_badges as $codigo):
                    $bg_color = $color_map[$codigo] ?? '#6b7280';
                ?>
                    <span class="badge"
                          style="background-color: <?= $bg_color; ?>;
                                 font-size: 0.65rem;
                                 padding: 2px 6px;
                                 font-weight: 500;
                                 letter-spacing: 0.5px;"><?= $codigo; ?></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <span style="color: #D2D5DA; font-size: 0.75rem;">-</span>
        <?php endif; ?>
    </td>
    <td><?= $descripcion ? "$descripcion" : "-"; ?></td>
    <td><?= $unidad ? "$unidad" : "-"; ?></td>
    <td>
        <input onfocusout="updateGenerico('presupuestos_partidas','total_x_cantidad',<?= $id_presupuestos_partidas; ?>,this.value)"
               type="number" min="0" class="form-control form-control-sm" value="<?= $cantidad; ?>"></td>
    <td><?= formatCurrency($subtotal); ?></td>
    <td id="subtotal_x_cantidad<?= $id_presupuestos_partidas; ?>"><?= formatCurrency($subtotal_x_cantidad); ?></td>
   <!-- <td id="total_x_cantidad<?= $id_presupuestos_partidas; ?>"><?= formatCurrency($total_x_cantidad); ?></td>-->
    <td>
        <a href="presupuestoDetailPartida.php?id_presupuestos_partidas=<?= $id_presupuestos_partidas; ?>"><i
                    class="bi bi-arrow-right fs-5"
                    style="color: #D2D5DA"></i></a>
    </td>
</tr>