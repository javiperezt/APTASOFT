<?php
// Determinar el color del indicador de estado según el margen
$estado_color_badge = 'success';
$estado_icono_badge = 'bi-check-circle';
$estado_texto = 'OK';

if ($margen_partida < 0) {
    $estado_color_badge = 'danger';
    $estado_icono_badge = 'bi-x-circle';
    $estado_texto = 'Pérdida';
} elseif ($margen_partida < 10) {
    $estado_color_badge = 'warning';
    $estado_icono_badge = 'bi-exclamation-circle';
    $estado_texto = 'Alerta';
} elseif ($margen_partida < 20) {
    $estado_color_badge = 'info';
    $estado_icono_badge = 'bi-info-circle';
    $estado_texto = 'Bajo';
}

// Helpers inline para evitar redeclaración de funciones
$getColorDesviacion = function($desviacion) {
    if (abs($desviacion) <= 5) return 'success';
    if (abs($desviacion) <= 15) return 'warning';
    return 'danger';
};

$getProgressBar = function($gasto, $previsto) {
    if ($previsto == 0) return '';
    $porcentaje = min(($gasto / $previsto) * 100, 100);
    $color = $porcentaje > 100 ? 'danger' : ($porcentaje > 80 ? 'warning' : 'success');
    return '<div class="progress" style="height: 4px; width: 60px;">
        <div class="progress-bar bg-'.$color.'" role="progressbar" style="width: '.$porcentaje.'%"></div>
    </div>';
};
?>
<tr data-estado="<?= $estado_texto == 'OK' ? 'ok' : ($estado_texto == 'Pérdida' ? 'danger' : 'warning') ?>">
    <!-- INDICADOR DE ESTADO -->
    <td class="text-center">
        <span class="badge bg-<?= $estado_color_badge ?>" title="<?= $estado_texto ?>">
            <i class="bi <?= $estado_icono_badge ?>"></i>
        </span>
    </td>

    <td class="fw-semibold"><?= $capitulo; ?></td>
    <td><?= $partida; ?></td>

    <!-- MANO DE OBRA -->
    <td class="text-danger fw-light">
        <?= number_format($costeManoObra, 2, ',', '.'); ?>€
        <?= $getProgressBar($costeManoObra, $ingresosManoObra); ?>
    </td>
    <td class="text-success fw-light"><?= number_format($ingresosManoObra, 2, ',', '.'); ?>€</td>
    <td class="text-center">
        <span class="badge bg-<?= $getColorDesviacion($desviacion_MO) ?> bg-opacity-75">
            <?= $desviacion_MO > 0 ? '+' : '' ?><?= number_format($desviacion_MO, 1, ',', '.'); ?>%
        </span>
    </td>

    <!-- MATERIAL -->
    <td class="text-danger fw-light">
        <?= number_format($costeMaterial, 2, ',', '.'); ?>€
        <?= $getProgressBar($costeMaterial, $ingresosMaterial); ?>
    </td>
    <td class="text-success fw-light"><?= number_format($ingresosMaterial, 2, ',', '.'); ?>€</td>
    <td class="text-center">
        <span class="badge bg-<?= $getColorDesviacion($desviacion_MAT) ?> bg-opacity-75">
            <?= $desviacion_MAT > 0 ? '+' : '' ?><?= number_format($desviacion_MAT, 1, ',', '.'); ?>%
        </span>
    </td>

    <!-- OTROS PROVEEDORES -->
    <td class="text-danger fw-light">
        <?= number_format($costeOtros, 2, ',', '.'); ?>€
        <?= $getProgressBar($costeOtros, $ingresosOtros); ?>
    </td>
    <td class="text-success fw-light"><?= number_format($ingresosOtros, 2, ',', '.'); ?>€</td>
    <td class="text-center">
        <span class="badge bg-<?= $getColorDesviacion($desviacion_EXT) ?> bg-opacity-75">
            <?= $desviacion_EXT > 0 ? '+' : '' ?><?= number_format($desviacion_EXT, 1, ',', '.'); ?>%
        </span>
    </td>

    <!-- RESULTADO TOTAL -->
    <td class="text-danger fw-bold"><?= number_format($totalCostes, 2, ',', '.'); ?>€</td>
    <td class="text-success fw-bold"><?= number_format($totalIngresos, 2, ',', '.'); ?>€</td>
    <td class="fw-bold <?= $totalResultado >= 0 ? 'text-success' : 'text-danger' ?>">
        <?= number_format($totalResultado, 2, ',', '.'); ?>€
    </td>
    <td class="text-center">
        <span class="badge bg-<?= $estado_color_badge ?> fs-6 fw-bold">
            <?= number_format($margen_partida, 1, ',', '.'); ?>%
        </span>
    </td>
</tr>