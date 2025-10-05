<div class="d-flex flex-column gap-2 ">
    <div class="card w-100 position-relative">
        <i onclick="obraDeleteNota(<?= $id_nota; ?>)" class="bi bi-x-lg position-absolute pointer"
           style="top: 10px;right: 10px"></i>
        <div class="card-body mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <p class="card-title fw-bold"><?= $titulo; ?></p>
                <p class="card-subtitle text-muted"><b><?= $nombre_empleado; ?></b> - <?= $creation_date; ?>
                </p>
            </div>
            <p class="card-text"><?= $comentario; ?></p>
        </div>
    </div>
</div>