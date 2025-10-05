<!---------- MODAL ---------->
<div class="modal fade" id="contactoDelete" tabindex="-1" aria-labelledby="contactoDelete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <p class="text-black fs-4 fw-bold mb-2">Eliminar contacto</p>
            <p class="text-black">¿Estás seguro de que quieres eliminar el contacto? Esta acción es irreversible</p>
            <button onclick="contactoDelete(<?= $id_contacto; ?>)" class="btn btn-danger w-100 mt-3">Eliminar</button>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->