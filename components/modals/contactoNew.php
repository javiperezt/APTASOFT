<!---------- MODAL ---------->
<div class="modal fade" id="contactoNew" tabindex="-1" aria-labelledby="contactoNew" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/contactos/contactoCreate.php" method="POST" autocomplete="off">
                <p class="text-black fs-4 fw-bold mb-3">Nuevo contacto</p>
                <div class="row" style="row-gap: 10px">
                    <div class="col-12">
                        <div class="form-floating">
                            <input required type="text" class="form-control" name="nombre" placeholder="Nombre">
                            <label>Nombre</label>
                        </div>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Crear">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->