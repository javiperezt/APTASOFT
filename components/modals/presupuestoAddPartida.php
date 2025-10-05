<!---------- MODAL ---------->
<div class="modal fade" id="presupuestoAddPartida" tabindex="-1" aria-labelledby="presupuestoAddPartida"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/presupuestos/presupuestoAddPartida.php" method="POST" autocomplete="off">
                <p class="text-black fs-5 fw-bold">Añadir partida</p>
                <div class="mt-2">
                    <div class="form-floating">
                        <select required class="form-select" name="id_capitulo">
                            <option selected disabled hidden value="">Capítulo</option>
                            <?php
                            $getCapitulosPresupuestos = $mysqli->query("SELECT * FROM presupuestos_capitulos where id_presupuesto=$id_presupuesto");
                            while ($row = $getCapitulosPresupuestos->fetch_assoc()) {
                                $id_capitulo = $row['id_capitulo'];

                                if ($id_capitulo) {
                                    $getCapitulo = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                                    while ($row = $getCapitulo->fetch_assoc()) {
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
                <div class="mt-2">
                    <div class="form-floating">
                        <select required class="selectizeSearch selectizeSearchBig" name="id_partida">
                            <option selected disabled hidden>Partida</option>
                            <?php
                            $getPartidas = $mysqli->query("SELECT * FROM gestor_partidas where is_active=1");
                            while ($row = $getPartidas->fetch_assoc()) {
                                $id_partida = $row['id'];
                                $partida = $row['partida'];

                                echo "<option value='$id_partida'>$partida</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id_presupuesto" value="<?= $id_presupuesto; ?>">
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Añadir">
                <div class="text-center mt-2">
                    <a target="_blank" href="partidas.php">Crear nueva</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->