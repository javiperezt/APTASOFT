<!---------- MODAL ---------->
<div class="modal fade" id="obraNew" tabindex="-1" aria-labelledby="obraNew" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obras/obraCreate.php" method="POST" autocomplete="off">
                <p class="text-black fs-4 fw-bold mb-3">Nueva obra</p>
                <div class="row" style="row-gap: 10px">
                    <div class="col-12">
                        <div class="form-floating">
                            <input required type="text" name="titulo" class="form-control" placeholder="Título">
                            <label>Título</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <select name="id_contacto" required class="selectizeSearch selectizeSearchBig">
                            <option selected disabled hidden>Cliente</option>
                            <?php
                            $getContactos = $mysqli->query("SELECT * FROM contactos where is_active=1");
                            while ($row = $getContactos->fetch_assoc()) {
                                $id_contacto = $row['id'];
                                $nombre = $row['nombre'];
                                echo "<option value='$id_contacto'>$nombre</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <select name="id_empresa" required class="form-select">
                                <option selected disabled hidden>Empresa</option>
                                <?php
                                $getEmpresas = $mysqli->query("SELECT * FROM empresas");
                                while ($row = $getEmpresas->fetch_assoc()) {
                                    $id_empresa = $row['id'];
                                    $empresa = $row['empresa'];
                                    echo "<option value='$id_empresa'>$empresa</option>";
                                }
                                ?>
                            </select>
                            <label>Empresa</label>
                        </div>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Crear">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->