<!---------- MODAL ---------->
<div class="modal fade" id="certificacionFacturar" tabindex="-1" aria-labelledby="certificacionFacturar"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <p class="text-black fs-4 fw-bold mb-2">Facturar certificación</p>
            <p class="text-black">¿Estás seguro de que quieres facturar la certificación?</p>
            <a onclick="certificacionFacturar(<?=$id_certificacion;?>)" class="btn btn-primary w-100 mt-3" >Aceptar</a>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->