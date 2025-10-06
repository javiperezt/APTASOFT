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

$c00 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c00->fetch_assoc()) {
    $id_empresa = $row['id_empresa'];
    $id_contacto = $row['id_contacto'];
}

$c1 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
while ($row = $c1->fetch_assoc()) {
    $empresa = $row['empresa'];
    $nombre_comercial = $row['nombre_comercial'];
    $direccion = $row['direccion'];
    $nif = $row['nif'];
    $poblacion = $row['poblacion'];
    $provincia = $row['provincia'];
    $cp = $row['cp'];
    $tel = $row['tel'];
    $tel2 = $row['tel2'];
    $src_logo = $row['src_logo'];
}

$c2 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
while ($row = $c2->fetch_assoc()) {
    $cliente_nombre_comercial = $row['nombre'];
    $cliente_direccion = $row['direccion'];
    $cliente_nif = $row['nif'];
    $cliente_poblacion = $row['poblacion'];
    $cliente_provincia = $row['provincia'];
    $cliente_cp = $row['cp'];
    $cliente_tel = $row['tel'];
    $cliente_tel2 = $row['movil'];
}

$q22 = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada*precio_certificado) AS x FROM certificaciones_partidas where id_certificacion='$id_certificacion'");
$result22 = mysqli_fetch_assoc($q22);
$totalCert = round($result22['x'], 2);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Certificacion</title>
    <?php include "../links_header.php"; ?>
    <style media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 30px;  /* this affects the margin in the printer settings */
        }

    </style>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            line-height: 19px;
            color: #555;
        }

        td, th {
            font-size: 12px !important;
        }


        @media print {
            .invoice-box {
                max-width: unset;
                box-shadow: none;
                border: 0;
            }
        }
    </style>
</head>

<body>

<div class="invoice-box">
    <div class="d-flex justify-content-between align-items-center">
        <img src="../img/logos/<?= $src_logo; ?>?rnd=<?= time(); ?>" style="width: 100%; max-width: 140px"/>
        <div class="text-end">
            <p class="fw-bold text-uppercase"><?= $nombre_comercial; ?></p>
            <p class="text-uppercase"><?= $direccion; ?></p>
            <p class="text-uppercase"><?= $provincia; ?>,<?= $cp; ?></p>
            <p class="text-uppercase"><?= $tel; ?> / <?= $tel2; ?></p>
            <p class="text-uppercase"><?= $nif; ?></p>
        </div>
    </div>
    <hr>
    <div class="d-flex align-items-center justify-content-between mt-3">
        <div>
            <p class="fw-bold fs-6 mb-3 mt-2">CERTIFICACION <?= $codigo; ?></p>
            <p class="fw-bold text-uppercase"><?= $cliente_nombre_comercial; ?></p>
            <p class="text-uppercase"><?= $cliente_direccion; ?></p>
            <p class="text-uppercase"><?= $cliente_provincia; ?>,<?= $cliente_cp; ?></p>
            <p class="text-uppercase"><?= $cliente_tel; ?> / <?= $cliente_tel2; ?></p>
            <p class="text-uppercase"><?= $cliente_nif; ?></p>
        </div>
        <div>
            <p class="text-end"><?= $fecha; ?></p>
        </div>
    </div>

    <!----------- CONTAINER ----------->
    <div class="mt-3">
        <?php
        $c1 = $mysqli->query("SELECT MIN(id), id_capitulo FROM certificaciones_partidas where id_certificacion='$id_certificacion' GROUP BY id_capitulo");
        while ($row = $c1->fetch_assoc()) {
            $id_capitulo = $row['id_capitulo'];
            $n_capitulo++;

            $c9 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
            while ($row = $c9->fetch_assoc()) {
                $capitulo = $row['capitulo'];
            }

            $q = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada) AS x FROM certificaciones_partidas where  id_capitulo=$id_capitulo  and id_certificacion=$id_certificacion");
            $result = mysqli_fetch_assoc($q);
            $cantidadCertificadaCapitulo = round($result['x'] ?? 0, 2);

            if ($cantidadCertificadaCapitulo != 0) {
                ?>
                <div class="mt-5">
                    <p class="fw-bold letraPeq text-uppercase py-2"
                       style="border-top: 1px solid currentcolor;border-bottom:  1px solid currentcolor;">
                        CAPÍTULO <?= $n_capitulo; ?> - <?= $capitulo; ?></p>
                </div>

                <!----------- TABLE ----------->
                <div class="mt-3">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="30px" scope="col">REF</th>
                            <th scope="col">RESUMEN</th>
                            <th scope="col" colspan="2">PRESUPUESTO</th>
                            <th scope="col" colspan="2">ANTERIORES</th>
                            <th scope="col" colspan="2">ACTUAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $n_partida = 0;
                        $id_presupuesto = "";
                        $id_partida_presupuesto = "";
                        $partida = "";
                        $descripcion = "";
                        $id_unidad = "";
                        $cantidad = 0;
                        $precio = 0;
                        $total = 0;
                        $cantidad_certificada = 0;
                        $precio_certificado = 0;
                        $total_cert = 0;
                        $cantidad_cert_anterior = 0;
                        $precio_cert_anterior = 0;
                        $total_capitulo = 0;
                        $c10 = $mysqli->query("SELECT * FROM certificaciones_partidas where id_certificacion=$id_certificacion and id_capitulo=$id_capitulo");
                        while ($row = $c10->fetch_assoc()) {
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

                            $n_partida++;

                            $q = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada) AS x FROM certificaciones_partidas where id_presupuesto='$id_presupuesto' and id_partida_presupuesto='$id_partida_presupuesto' and id_certificacion<$id_certificacion");
                            $result = mysqli_fetch_assoc($q);
                            $cantidad_cert_anterior = round($result['x'] ?? 0, 2);

                            $q3 = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada*precio_certificado) AS x FROM certificaciones_partidas where id_presupuesto='$id_presupuesto' and id_capitulo='$id_capitulo' and id_certificacion='$id_certificacion'");
                            $result3 = mysqli_fetch_assoc($q3);
                            $total_capitulo = round($result3['x'] ?? 0, 2);

                            if ($cantidad_certificada != 0) {
                                ?>
                                <tr>
                                    <td class="fw-bold"><?= $n_capitulo; ?>.<?= $n_partida; ?></td>
                                    <td><?= $partida; ?></td>
                                    <td><?= $cantidad; ?></td>
                                    <td class="text-center"><?= $precio; ?>€</td>
                                    <td><?= $cantidad_cert_anterior; ?></td>
                                    <td class="text-center"><?= $precio_cert_anterior; ?>€</td>
                                    <td><?= $cantidad_certificada; ?></td>
                                    <td class="text-center"><?= $precio_certificado; ?>€</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-4 text-nowrap mt-4">
                    <p class="text-uppercase letraPeq">TOTAL CAPÍTULO <?= $n_capitulo; ?> <?= $capitulo; ?> </p>
                    <hr class="m-0 mb-1 align-self-end" width="100%"/>
                    <p class="letraPeq"><?= $total_capitulo; ?>€</p>
                </div>

                <?php
            }
        }
        ?>
        <!----------- END TABLE ----------->
    </div>
    <!----------- END CONTAINER ----------->


    <div class="d-flex align-items-center justify-content-between gap-4 text-nowrap mt-5">
        <h2 class="text-uppercase letraPeq fw-bold">TOTAL CERTIFICACION</h2>
        <hr class="m-0 mb-1 align-self-end" width="100%"/>
        <p class="letraPeq"><?= $totalCert; ?>€</p>
    </div>
</div>

<script>
    window.onload = function () {
        window.print();
        window.onafterprint = function () {
            window.close();
        }
    }
</script>
</body>
</html>
