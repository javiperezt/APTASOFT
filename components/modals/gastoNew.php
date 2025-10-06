<!---------- MODAL ---------->
<div class="modal fade" id="gastoNew" tabindex="-1" aria-labelledby="gastoNew" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/gastos/gastoCreate.php" method="POST" autocomplete="off">
                <p class="text-black fs-4 fw-bold mb-3">Nuevo gasto</p>
                <div class="row" style="row-gap: 10px">
                    <div class="col-6">
                        <div class="form-floating">
                            <input required type="text" class="form-control" placeholder="Código" id="codigo"
                                   name="codigo">
                            <label>Código</label>
                        </div>
                        <div id="result"></div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input required type="date" class="form-control" name="fecha_inicio">
                            <label>Fecha</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <select required class="selectizeSearch selectizeSearchBig" name="id_obra">
                            <option selected disabled hidden>Obra</option>
                            <?php
                            $getObras_gastos = $mysqli->query("SELECT * FROM obras where is_active=1");
                            while ($row = $getObras_gastos->fetch_assoc()) {
                                $s_id_obra = $row['id'];
                                $titulo_obra = $row['titulo'];
                                $selected = "";
                                if (isset($id_obra) && $id_obra == $s_id_obra) {
                                    $selected = "selected";
                                }
                                echo "<option $selected value='$s_id_obra'>$titulo_obra</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select required class="selectizeSearch selectizeSearchBig" name="id_contacto">
                            <option selected disabled hidden>Proveedor</option>
                            <?php
                            $getContactos_gastos = $mysqli->query("SELECT * FROM contactos where is_active=1");
                            while ($row = $getContactos_gastos->fetch_assoc()) {
                                $s_id_contacto = $row['id'];
                                $nombre_contacto = $row['nombre'];
                                $selected = "";
                                if (isset($id_contacto_obra) && $id_contacto_obra == $s_id_contacto) {
                                    $selected = "selected";
                                }
                                echo "<option $selected value='$s_id_contacto'>$nombre_contacto</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <select required class="form-select" name="id_empresa">
                                <option selected disabled hidden value="">Empresa</option>
                                <?php
                                $getEmpresas_gastos = $mysqli->query("SELECT * FROM empresas");
                                while ($row = $getEmpresas_gastos->fetch_assoc()) {
                                    $s_id_empresa = $row['id'];
                                    $empresa = $row['empresa'];
                                    $selected = "";
                                    if (isset($id_empresa_obra) && $id_empresa_obra == $s_id_empresa) {
                                        $selected = "selected";
                                    }
                                    echo "<option $selected value='$s_id_empresa'>$empresa</option>";
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



<script>
    $(document).ready(function () {
        $('#codigo').on('input', function () {
            var codigo = $(this).val();
            $.ajax({
                url: '../backend/gastos/gastoCheckCode.php',
                method: 'POST',
                data: {codigo: codigo},
                success: function (response) {
                    $('#result').html(response);
                }
            });
        });
    });
</script>
