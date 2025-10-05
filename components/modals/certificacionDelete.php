<!---------- MODAL ---------->
<div class="modal fade" id="certificacionDelete" tabindex="-1" aria-labelledby="certificacionDelete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <p class="text-black fs-4 fw-bold mb-2">Eliminar certificación</p>
            <p class="text-black">¿Estás seguro de que quieres eliminar la certificación? Esta acción es
                irreversible</p>
            <a onclick="certificacionDelete(<?= $id_certificacion; ?>)" class="btn btn-danger w-100 mt-3">Eliminar</a>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->