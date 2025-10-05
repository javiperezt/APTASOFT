<!---------- MODAL ---------->
<div class="modal fade" id="presupuestoNew" tabindex="-1" aria-labelledby="presupuestoNew" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/presupuestos/presupuestoCreate.php" method="POST" autocomplete="off">
                <p class="text-black fs-4 fw-bold mb-3">Nuevo presupuesto</p>
                <div class="row" style="row-gap: 10px">
                    <div class="col-6">
                        <div class="form-floating">
                            <input id="ref" required type="text" class="form-control" placeholder="Referencia"
                                   name="ref"
                                   value="<?= $nextRefBudget; ?>">
                            <label>Referencia</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input required type="date" class="form-control" name="fecha_inicio">
                            <label>Fecha</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <select required class="selectizeSearch selectizeSearchBig" name="id_contacto">
                            <option selected disabled hidden>Cliente</option>
                            <?php
                            $getContactos = $mysqli->query("SELECT * FROM contactos where is_active=1");
                            while ($row = $getContactos->fetch_assoc()) {
                                $s_id_contacto = $row['id'];
                                $nombre = $row['nombre'];
                                $selected="";
                                if($id_contacto_obra == $s_id_contacto){
                                    $selected="selected";
                                }
                                echo "<option $selected value='$s_id_contacto'>$nombre</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select required class="selectizeSearch selectizeSearchBig" name="id_obra">
                            <option selected disabled hidden>Obra</option>
                            <?php
                            $getObras = $mysqli->query("SELECT * FROM obras where is_active=1");
                            while ($row = $getObras->fetch_assoc()) {
                                $s_id_obra = $row['id'];
                                $titulo_obra = $row['titulo'];
                                $selected="";
                                if($id_obra == $s_id_obra){
                                    $selected="selected";
                                }
                                echo "<option $selected value='$s_id_obra'>$titulo_obra</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select required class="selectizeSearch selectizeSearchBig" name="id_empresa">
                            <option selected disabled hidden>Empresa</option>
                            <?php
                            $getEmpresas = $mysqli->query("SELECT * FROM empresas");
                            while ($row = $getEmpresas->fetch_assoc()) {
                                $s_id_empresa = $row['id'];
                                $empresa = $row['empresa'];
                                $selected="";
                                if($id_empresa_obra == $s_id_empresa){
                                    $selected="selected";
                                }
                                echo "<option $selected value='$s_id_empresa'>$empresa</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Crear">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->