<!---------- MODAL ---------->
<div class="modal fade" id="partidaNew" tabindex="-1" aria-labelledby="partidaNew"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/gestorPartidas/partidaCreate.php" method="POST" autocomplete="off">
                <p class="text-black fs-5 fw-bold">Nueva partida</p>
                <div class="mt-2">
                    <div class="form-floating">
                        <input required type="text" class="form-control" placeholder="Partida" name="partida">
                        <label>Partida</label>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Añadir">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->