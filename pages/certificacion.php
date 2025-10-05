<?php
session_start();
include "../conexion.php";
require_once "../authCookieSessionValidate.php";

if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_certificacion = filter_input(INPUT_GET, 'id_certificacion', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM certificaciones where id=$id_certificacion");
while ($row = $c0->fetch_assoc()) {
    $id_obra = $row['id_obra'];
    $concepto = $row['concepto'];
    $codigo = $row['codigo'];
    $id_estado = $row['id_estado'];
    $fecha = $row['fecha'];
    $creation_date = $row['creation_date'];
    $is_active = $row['is_active'];
}

$c023 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c023->fetch_assoc()) {
    $id_empresa = $row['id_empresa'];
    $id_contacto = $row['id_contacto'];
}

?>
<html lang="es">
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>
<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <div class="d-flex gap-2 align-items-center">
        <a class="pointer" href="<?=$_SERVER['HTTP_REFERER'];?>"><i
                    class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Certificación</p>
    </div>
    <div>
        <a href="certificacionPdf.php?id_certificacion=<?= $id_certificacion; ?>" target="_blank"
           class="btn btn-outline-secondary btn-sm">Descargar PDF<i
                    class="bi bi-file-earmark-text-fill ms-2"></i></a>
        <button data-bs-toggle="modal" data-bs-target="#certificacionFacturar" class="btn btn-outline-primary btn-sm">
            Facturar<i
                    class="bi bi-file-earmark-text-fill ms-2"></i></button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-6">
            <div class="form-floating">
                <input onfocusout="updateGenerico('certificaciones','concepto',<?= $id_certificacion; ?>,this.value)"
                       type="text" class="form-control" placeholder="Concepto" value="<?= $concepto; ?>">
                <label>Concepto</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onfocusout="updateGenerico('certificaciones','codigo',<?= $id_certificacion; ?>,this.value)"
                       type="text" class="form-control" placeholder="Código" value="<?= $codigo; ?>">
                <label>Código</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('certificaciones','id_estado',<?= $id_certificacion; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>-</option>
                    <?php
                    $getcertificaciones_estados = $mysqli->query("SELECT * FROM certificaciones_estados");
                    while ($row = $getcertificaciones_estados->fetch_assoc()) {
                        $s_id_certificaciones_estados = $row['id'];
                        $s_estado = $row['estado'];

                        $selected = "";
                        if ($s_id_certificaciones_estados == $id_estado) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$s_id_certificaciones_estados'>$s_estado</option>";
                    }
                    ?>
                </select>
                <label>Estado</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('certificaciones','fecha',<?= $id_certificacion; ?>,this.value)"
                       type="date" class="form-control" placeholder="Fecha" value="<?= $fecha; ?>">
                <label>Fecha</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- MAIN ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Partidas</p>
    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Capítulo</th>
            <th scope="col">Partida</th>
            <th colspan="2" scope="col">Presupuesto</th>
            <th scope="col">Cert. Anteriores</th>
            <th scope="col">Cant. Restante</th>
            <th colspan="3" scope="col">Certificación Actual</th>
            <th scope="col">% Certificado</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $c1 = $mysqli->query("SELECT * FROM certificaciones_partidas where id_certificacion=$id_certificacion");
        while ($row = $c1->fetch_assoc()) {
            $id_certificaciones_partidas = $row['id'];
            $id_certificacion = $row['id_certificacion'];
            $id_presupuesto = $row['id_presupuesto'];
            $id_capitulo = $row['id_capitulo'];
            $id_partida_presupuesto = $row['id_partida_presupuesto'];
            $partida = $row['partida'];
            $descripcion = $row['descripcion'];
            $id_unidad = $row['id_unidad'];
            $cantidad = $row['cantidad'];
            $precio = $row['precio'];
            $total = $row['total'];
            $cantidad_certificada = $row['cantidad_certificada'];
            $precio_certificado = $row['precio_certificado'];
            $total_cert = $cantidad_certificada * $precio_certificado;

            $c2 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
            while ($row = $c2->fetch_assoc()) {
                $capitulo = $row['capitulo'];
            }

            $q = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada) AS x FROM certificaciones_partidas where id_presupuesto='$id_presupuesto' and id_partida_presupuesto='$id_partida_presupuesto' and id_certificacion<$id_certificacion");
            $result = mysqli_fetch_assoc($q);
            $cantidad_cert_anterior = round($result['x'], 2);

            $q2 = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada) AS x2 FROM certificaciones_partidas where id_presupuesto='$id_presupuesto' and id_partida_presupuesto='$id_partida_presupuesto'");
            $result2 = mysqli_fetch_assoc($q2);
            $cantidad_cert_anterior2 = round($result2['x2'], 2);

            $procentajeCert = round(($cantidad_cert_anterior2 / $cantidad * 100), 2);

            $restante = $cantidad - $cantidad_cert_anterior2;

            include "../components/obras/certificacionLineDetail.php";
        }
        if ($c1->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END MAIN ----------->


<div class="container text-end">
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#certificacionDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>

</body>
<?php include "../components/modals/certificacionDelete.php"; ?>
<?php include "../components/modals/certificacionFacturar.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>

<script>
    const updateGenerico = (tabla, columna, fila, valor) => {
        $.ajax({
            method: "POST",
            url: "../backend/updateGenerico.php",
            data: {
                tabla: tabla,
                columna: columna,
                fila: fila,
                valor: valor
            }
        }).done(function () {
            if (columna == "cantidad_certificada" || columna == "precio_certificado") {
                $(".tableComon").load(location.href + " .tableComon");
                showMessage();
            } else {
                showMessage();
            }
        });
    }

    const certificacionDeleteLine = (id_certificaciones_partidas) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/certificacionDeleteLine.php",
            data: {
                id_certificaciones_partidas: id_certificaciones_partidas
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
        });
    }

    const certificacionDelete = (id_certificacion) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/certificacionDelete.php",
            data: {
                id_certificacion: id_certificacion
            }
        }).done(function () {
            location.href = 'obraDetail.php?id_obra=<?=$id_obra;?>';
        });
    }

    const certificacionFacturar = (id_certificacion) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/certificacionFacturar.php",
            data: {
                id_certificacion: id_certificacion,
                id_obra: <?=$id_obra;?>,
                id_empresa: <?=$id_empresa;?>,
                id_contacto: <?=$id_contacto;?>
            }
        }).done(function (data) {
            location.href = 'facturaDetail.php?id_factura=' + data;
        });
    }
</script>
</html>