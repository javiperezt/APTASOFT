<tr>
    <td>
        <input <?= $is_verified ? "checked" : "" ?>
            onchange="updateGenerico('obras_registro_partes','is_verified',<?= $id_obras_registro_partes; ?>, this.checked ? 1 : 0)"
            type="checkbox" class="btn-check"
            id="btn-check-outlined<?= $id_obras_registro_partes; ?>" autocomplete="off">
        <label style="padding: 3px !important;" class="btn btn-outline-primary btn-sm"
               for="btn-check-outlined<?= $id_obras_registro_partes; ?>"><i
                class="bi bi-check-lg"></i></label>
    </td>
    <td><?= $fecha; ?></td>
    <td scope="col"><?= $titulo_obra; ?></td>
    <td scope="col"><?= $capitulo; ?></td>
    <td scope="col"><?= $partida; ?></td>
    <td scope="col"><?= $concepto_subpartida; ?></td>
    <td>
        <input onchange="updateGenerico('obras_registro_partes','horas',<?= $id_obras_registro_partes; ?>, this.value)"
               type="time" class="form-control form-control-sm" value="<?= $horas; ?>"></td>
    <td><span class="badge <?= $class; ?>"><?= $text; ?></span></td>
    <td><a class="pointer" onclick="obrasDeleteParte(<?= $id_obras_registro_partes; ?>)"><i
                class="bi bi-x fs-5"></i></a></td>
</tr>