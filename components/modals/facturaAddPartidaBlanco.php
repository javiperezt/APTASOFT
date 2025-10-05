<!---------- MODAL ---------->
<div class="modal fade" id="facturaAddPartidaBlanco" tabindex="-1" aria-labelledby="facturaAddPartidaBlanco"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/facturas/facturaAddPartidaBlanco.php" method="POST" autocomplete="off">
                <p class="text-black fs-5 fw-bold">Añadir partida</p>
                <div class="mt-3">
                    <div class="form-floating">
                        <select required class="form-select" name="id_capitulo">
                            <option selected disabled hidden value="">Capítulo</option>
                            <?php
                            $getCapitulosFacturas2 = $mysqli->query("SELECT * FROM facturas_capitulos where id_factura=$id_factura");
                            while ($row = $getCapitulosFacturas2->fetch_assoc()) {
                                $id_capitulo = $row['id_capitulo'];

                                if ($id_capitulo) {
                                    $getCapitulo2 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                                    while ($row = $getCapitulo2->fetch_assoc()) {
                                        $capitulo = $row['capitulo'];
                                    }
                                }

                                echo "<option value='$id_capitulo'>$capitulo</option>";
                            }
                            ?>
                        </select>
                        <label>Capítulo</label>
                    </div>
                </div>
                <input type="hidden" name="id_factura" value="<?= $id_factura; ?>">
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Añadir">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->