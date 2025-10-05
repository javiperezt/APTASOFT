<!---------- MODAL ---------->
<div class="modal fade" id="obraDelete" tabindex="-1" aria-labelledby="obraDelete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <p class="text-black fs-4 fw-bold mb-2">Eliminar obra</p>
            <p class="text-black">¿Estás seguro de que quieres eliminar la obra?</p>
            <p class="text-black mt-2">También se eliminaran todos los presupuestos, facturas, certificaciones y gastos
                asociados a esta obra</p>
            <button onclick="obraDelete(<?= $id_obra; ?>)" class="btn btn-danger w-100 mt-3">Eliminar</button>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->