<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_presupuesto = filter_input(INPUT_GET, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);


$c0 = $mysqli->query("SELECT * FROM presupuestos where id=$id_presupuesto");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_cuenta = $row['id_cuenta'];
    $id_obra = $row['id_obra'];
    $id_estado = $row['id_estado'];
    $id_empresa = $row['id_empresa'];
    $pref_ref = $row['pref_ref'];
    $pref_ref_year = $row['pref_ref_year'];
    $ref = $row['ref'];
    $ref = $pref_ref . $pref_ref_year . $ref;
    $asunto = $row['asunto'];
    $fecha_inicio = $row['fecha_inicio'];
    $nota = $row['nota'];
    $fecha_vencimiento = $row['fecha_vencimiento'];
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

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>P_<?= $ref; ?></title>
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
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            line-height: 19px;
            color: #555;
        }

        td, th {
            font-size: 12px !important;
            padding: 1px 10px 5px 10px !important;
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
            <p class="fw-bold fs-6 mb-3 mt-2"> <?= $ref; ?></p>
            <p class="fw-bold text-uppercase"><?= $cliente_nombre_comercial; ?></p>
            <p class="text-uppercase"><?= $cliente_direccion; ?></p>
            <p class="text-uppercase"><?= $cliente_provincia; ?>,<?= $cliente_cp; ?></p>
            <p class="text-uppercase"><?= $cliente_tel; ?> / <?= $cliente_tel2; ?></p>
            <p class="text-uppercase"><?= $cliente_nif; ?></p>
        </div>
        <div>
            <p class="text-end"><?= $fecha_vencimiento; ?></p>
        </div>
    </div>

    <!----------- CONTAINER ----------->
    <div class="mt-3">
        <?php
        $n_capitulo = 0;
        $c5 = $mysqli->query("SELECT * FROM presupuestos_capitulos where id_presupuesto=$id_presupuesto");
        while ($row = $c5->fetch_assoc()) {
            $id_capitulo = $row['id_capitulo'];
            $n_capitulo++;

            $c6 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
            while ($row = $c6->fetch_assoc()) {
                $capitulo = $row['capitulo'];
            }
            ?>
            <div class="mt-4">
                <p class="fw-bold letraPeq text-uppercase py-2"
                   style="border-bottom:  1px solid currentcolor;">
                    CAPÍTULO <?= $n_capitulo; ?> - <?= $capitulo; ?></p>
            </div>

            <!----------- TABLE ----------->
            <div class="mt-2">
                <table class="table table-borderless ">
                    <thead>
                    <tr>
                        <th width="30px" scope="col"></th>
                        <th scope="col"></th>
                        <th width="50px" scope="col">UD</th>
                        <th width="50px" scope="col">CANT.</th>
                        <th width="50px" scope="col">PRECIO</th>
                        <th width="50px" scope="col">IMPORTE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $n_partida = 0;
                    $id_unidad = "";
                    $partida = "";
                    $descripcion = "";
                    $cantidad = 0;
                    $subtotal = 0;
                    $total = 0;
                    $subtotal_x_cantidad = 0;
                    $total_x_cantidad = 0;
                    $total_capitulo = 0;
                    $subtotal_capitulo = 0;
                    $c7 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto and id_capitulo=$id_capitulo");
                    while ($row = $c7->fetch_assoc()) {
                        $id_presupuestos_partidas = $row['id'];
                        $id_partida = $row['id_partida'];
                        $id_unidad = $row['id_unidad'];
                        $partida = $row['partida'];
                        $descripcion = $row['descripcion'];
                        $cantidad = $row['cantidad'];
                        $subtotal = $row['subtotal'];
                        $total = $row['total'];
                        $subtotal_x_cantidad = $cantidad * $subtotal;
                        $total_x_cantidad = $cantidad * $total;

                        $total_capitulo += $total_x_cantidad;
                        $subtotal_capitulo += $subtotal_x_cantidad;

                        $n_partida++;

                        if ($id_unidad) {
                            $unidades = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
                            while ($row = $unidades->fetch_assoc()) {
                                $unidad = $row['simbolo'];
                            }
                        }
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $n_capitulo; ?>.<?= $n_partida; ?></td>
                            <td class="fw-bold"><?= $partida; ?></td>
                            <td class="fw-bold"><?= $unidad; ?></td>
                            <td class="fw-bold"><?= $cantidad; ?></td>
                            <td class="fw-bold"><?= formatCurrency($subtotal); ?></td>
                            <td class="fw-bold"><?= formatCurrency($subtotal_x_cantidad); ?></td>
                        </tr>
                        <tr>
                            <td colspan="9"><?= $descripcion ? "$descripcion" : "-"; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <!----------- END TABLE ----------->

            <div class="d-flex align-items-center justify-content-between gap-4 text-nowrap mt-3">
                <p class="text-uppercase letraPeq">TOTAL CAPÍTULO <?= $n_capitulo; ?> <?= $capitulo; ?> </p>
                <hr class="m-0 mb-1 align-self-end" width="100%"/>
                <p class="letraPeq"><?= formatCurrency($subtotal_capitulo); ?></p>
            </div>

            <div class="d-flex justify-content-between align-items-center" style="margin-top: 1000px">
                <img src="../img/logos/<?= $src_logo; ?>" style="width: 100%; max-width: 140px"/>
                <div class="text-end">
                    <p class="fw-bold text-uppercase"><?= $nombre_comercial; ?></p>
                    <p class="text-uppercase"><?= $direccion; ?></p>
                    <p class="text-uppercase"><?= $provincia; ?>,<?= $cp; ?></p>
                    <p class="text-uppercase"><?= $tel; ?> / <?= $tel2; ?></p>
                    <p class="text-uppercase"><?= $nif; ?></p>
                </div>
            </div>
            <hr>
            <?php

        }
        ?>
    </div>
    <!----------- END CONTAINER ----------->


    <!----------- RESUMEN ----------->
    <div style="margin-top: 30px">
        <h5 class="m-0 fw-bold">RESUMEN DEL PRESUPUESTO</h5>
        <table class="table ">
            <thead>
            <tr>
                <th width="30px" scope="col">CAP</th>
                <th scope="col">RESUMEN</th>
                <th width="90px" scope="col">IMPORTE</th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
            <?php
            $n_capitulo = 0;
            $c8 = $mysqli->query("SELECT * FROM presupuestos_capitulos where id_presupuesto=$id_presupuesto");
            while ($row = $c8->fetch_assoc()) {
                $id_capitulo = $row['id_capitulo'];
                $n_capitulo++;

                $c9 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                while ($row = $c9->fetch_assoc()) {
                    $capitulo = $row['capitulo'];
                }

                $cantidad = 0;
                $subtotal = 0;
                $total = 0;
                $subtotal_x_cantidad = 0;
                $total_x_cantidad = 0;
                $total_capitulo = 0;
                $subtotal_capitulo = 0;
                $subtotalSubpartidas = 0;
                $totalSubpartidas = 0;
                $c10 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto and id_capitulo=$id_capitulo");
                while ($row = $c10->fetch_assoc()) {
                    $id_presupuestos_partidas = $row['id'];
                    $cantidad = $row['cantidad'];
                    $subtotal = $row['subtotal'];
                    $total = $row['total'];
                    $subtotal_x_cantidad = $cantidad * $subtotal;
                    $total_x_cantidad = $cantidad * $total;

                    $subtotal_capitulo += $subtotal_x_cantidad;
                    $total_capitulo += $total_x_cantidad;

                    // Calculo total del importe descontado del capitulo
                    $getSubtotalSubpartidas = mysqli_query($mysqli, "SELECT SUM(cantidad*precio) AS subtotalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas'");
                    $result = mysqli_fetch_assoc($getSubtotalSubpartidas);
                    $subtotalSubpartidas = $result['subtotalSubpartidas'];
                    $subtotalSubpartidas2 += $subtotalSubpartidas;

                    $getTotalSubpartidas = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas'");
                    $result = mysqli_fetch_assoc($getTotalSubpartidas);
                    $totalSubpartidas = $result['totalSubpartidas'];
                    $totalSubpartidas2 += $totalSubpartidas;

                }

                $getTotalPresupuesto = mysqli_query($mysqli, "SELECT SUM(subtotal*cantidad) AS totalPresupuesto FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
                $result = mysqli_fetch_assoc($getTotalPresupuesto);
                $totalPresupuesto = round($result['totalPresupuesto'] ?? 0, 2);

                ?>
                <tr>
                    <td><?= $n_capitulo; ?></td>
                    <td><?= $capitulo; ?></td>
                    <td><?= formatCurrency($subtotal_capitulo); ?></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td colspan="2" class="fw-bold">IMPORTE PRESUPUESTO</td>
                <td class="fw-bold"><?= formatCurrency($totalPresupuesto); ?></td>
            </tr>
            </tbody>
        </table>
        <div class="text-end">
            <p class="fw-bold">IVA NO INCLUIDO</p>
        </div>
    </div>

    <div class="mt-5">
        <p><b>NOTA:</b> <?=$nota;?></p>
    </div>

    <!----------- END RESUMEN ----------->
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
