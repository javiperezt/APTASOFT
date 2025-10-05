<div class="col">
    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
        <?php
        $s_EMPRESAS = $mysqli->query("SELECT * FROM empresas");
        while ($row = $s_EMPRESAS->fetch_assoc()) {
            $s_ID_EMPRESA = $row['id'];
            $s_EMPRESA = $row['empresa'];
            $checked = "";
            if ($f_id_empresa == $s_ID_EMPRESA) {
                $checked = "checked";
            }

            ?>
            <input onclick="filterHandler()" type="radio" class="btn-check" name="btnradio"
                   id="btnradio<?= $s_ID_EMPRESA; ?>" autocomplete="off" <?= $checked; ?> value="<?= $s_ID_EMPRESA; ?>">
            <label class="btn btn-outline-primary letraPeq"
                   for="btnradio<?= $s_ID_EMPRESA; ?>"><?= $s_EMPRESA; ?></label>
            <?php
        }
        ?>
    </div>
</div>