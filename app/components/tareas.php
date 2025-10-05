<div class="bg-white rounded-2 p-3">
    <p class="text-primary"><?= $fecha_vencimiento_subpartida; ?></p>
    <p class="mt-1 fw-bold fs-6"><?= $partida; ?></p>
    <p class="fs-6"><?= $descripcion_partida; ?></p>
    <hr>
    <div class="mt-2">
        <p class="letraPeq">Obra: <?= $titulo_obra; ?></p>
        <p class="letraPeq">
            Cantidad: <?= round($cantidad_partida * $cantidad_subpartida, 2); ?> <?= $simbolo; ?></p>
        <p class="letraPeq"><?= $descripcion_subpartida; ?></p>
    </div>
    <button data-bs-toggle="modal" data-bs-target="#imputarHoras<?=$id_obras_trabajadores_subpartidas;?>" class="btn btn-primary w-100 mt-3">
        Imputar horas
    </button>
</div>