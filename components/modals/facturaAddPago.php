<!---------- MODAL ---------->
<div class="modal fade" id="facturaAddPago" tabindex="-1" aria-labelledby="facturaAddPago" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/facturas/facturaAddPago.php" method="POST" autocomplete="off">
                <p class="text-black fs-4 fw-bold mb-3">Registrar Cobro</p>
                <div class="row" style="row-gap: 10px">
                    <div class="col-6">
                        <div class="form-floating">
                            <input required type="number" step="any" min="0" class="form-control" placeholder="Importe"
                                   name="importe">
                            <label>Importe</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input required type="date" class="form-control" name="fecha">
                            <label>Fecha</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" placeholder="Comentario" name="comentario">
                            <label>Comentario</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <select required class="form-select" name="forma_pago">
                                <option selected disabled hidden value="">Forma de pago</option>
                                <option value="giro">Giro</option>
                                <option value="pagare">Pagare</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta de credito">Tarjeta de credito</option>
                                <option value="efectivo">Efectivo</option>
                            </select>
                            <label>Forma de pago</label>
                        </div>
                    </div>
                    <input type="hidden" name="id_factura" value="<?= $id_factura; ?>">
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Registrar">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->