<!---------- MODAL ---------->
<div class="modal fade" id="certificacionNew" tabindex="-1" aria-labelledby="certificacionNew" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obras/certificacionCreate.php" method="POST" autocomplete="off">
                <p class="text-black fs-4 fw-bold mb-3">Nueva certificación</p>
                <div class="row g-2">
                    <div class="col-12">
                        <div class="form-floating">
                            <input name="concepto" required type="text" class="form-control" placeholder="Concepto">
                            <label>Concepto</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input required name="fecha" type="date" class="form-control">
                            <label>Fecha</label>
                        </div>
                    </div>
                    <input type="hidden" value="<?= $id_obra; ?>" name="id_obra">
                    <div class="col-6">
                        <div class="form-floating">
                            <input required name="codigo" type="text" class="form-control" placeholder="Código">
                            <label>Código</label>
                        </div>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Crear">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->