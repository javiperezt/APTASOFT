<div class="card py-2 px-3">
    <div class="d-flex justify-content-start align-items-center">
        <i class="bi bi-file-earmark-text-fill text-primary"></i>
        <a class="letraPeq ms-3 text-black" href="../docs/obras/<?= $src; ?>" download><span
                class="text-uppercase"><?= $directorio; ?></span> - <?= $titulo; ?></a>
        <a onclick="obraDeleteDoc(<?= $id_archivo; ?>)" class="ms-auto d-flex align-items-center" href="#"
           style="color: #9a0202"><i
                class="bi bi-trash-fill fs-6 me-2"></i> Eliminar</a>
    </div>
</div>