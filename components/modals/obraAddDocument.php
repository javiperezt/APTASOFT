<!---------- MODAL ---------->
<div class="modal fade" id="obraAddDocument" tabindex="-1" aria-labelledby="obraAddDocument"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content p-4">
            <button type="button" class="btn-close position-absolute" style="top: 10px;right: 10px"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="../backend/obras/obraUploadDocs.php" autocomplete="off" method="post" enctype="multipart/form-data">
                <p class="text-black fs-5 fw-bold">Subir documento</p>
                <div class="col-12 mt-3">
                    <div class="form-floating">
                        <select required class="form-select" name="id_directorio">
                            <option selected disabled hidden value="">Carpeta</option>
                            <?php
                            $obras_directorios = $mysqli->query("SELECT * FROM obras_directorios");
                            while ($row = $obras_directorios->fetch_assoc()) {
                                $id_directorio = $row['id'];
                                $directorio = $row['directorio'];
                                echo "<option value='$id_directorio'>$directorio</option>";
                            }
                            ?>
                        </select>
                        <label>Carpeta</label>
                    </div>
                </div>
                <div class="mt-2">
                    <input required class="form-control" type="file" id="uploadFile" name="upload[]" multiple>
                </div>
                <input type="hidden" value="<?= $ID_USER; ?>" name="uploaded_by">
                <input type="hidden" value="<?= $id_obra; ?>" name="id_obra">
                <input type="submit" class="btn btn-primary w-100 mt-3" value="Subir">
            </form>
        </div>
    </div>
</div>
<!---------- END MODAL ---------->