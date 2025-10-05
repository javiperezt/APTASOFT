<!---------- MODAL ---------->
<div class="modal fade" id="obraAddNota" tabindex="-1" aria-labelledby="obraAddNota"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obras/obraAddNota.php" method="POST" autocomplete="off">
                <p class="text-black fs-5 fw-bold">Añadir nota</p>
                <div class="mt-2">
                    <div class="form-floating">
                        <input required type="text" class="form-control" placeholder="Título" name="titulo">
                        <label>Título</label>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="form-floating">
                        <textarea required class="form-control" placeholder="Descripción" name="comentario"
                                  style="height: 100px"></textarea>
                        <label>Descripción</label>
                    </div>
                </div>
                <input type="hidden" name="id_empleado" value="<?= $ID_USER; ?>">
                <input type="hidden" name="id_obra" value="<?= $id_obra; ?>">
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Añadir">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->