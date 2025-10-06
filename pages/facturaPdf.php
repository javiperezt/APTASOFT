<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_factura = filter_input(INPUT_GET, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);


$c0 = $mysqli->query("SELECT * FROM facturas where id=$id_factura");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_obra = $row['id_obra'];
    $id_estado = $row['id_estado'];
    $id_empresa = $row['id_empresa'];
    $pref_ref = $row['pref_ref'];
    $nota = $row['nota'];
    $pref_ref_year = $row['pref_ref_year'];
    $ref = $row['ref'];
    $ref = $pref_ref . $pref_ref_year . $ref;
    $fecha_inicio = $row['fecha_inicio'];
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
    $iban = $row['iban'];
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
    <title>F_<?= $ref; ?></title>
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

        td, th, p {
            font-size: 12px !important;

        }

        * {
            color: #000000 !important;
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
            <p class="fw-bold fs-6 mb-3 mt-2">FACTURA <?= $ref; ?></p>
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
        <div>
            <table class="table ">
                <thead>
                <tr>
                    <th scope="col">Trabajo realizado</th>
                    <th width="50px" scope="col">Cantidad</th>
                    <th width="50px" scope="col">Precio</th>
                    <th width="50px" scope="col">IVA</th>
                    <th width="50px" scope="col">Total</th>
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
                $precio = "";
                $total = 0;
                $subtotal_x_cantidad = 0;
                $total_x_cantidad = 0;
                $total_capitulo = 0;
                $subtotal_capitulo = 0;
                $c7 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura");
                while ($row = $c7->fetch_assoc()) {
                    $id_facturas_partidas = $row['id'];
                    $id_partida = $row['id_partida'];
                    $id_capitulo = $row['id_capitulo'];
                    $id_unidad = $row['id_unidad'];
                    $partida = $row['partida'];
                    $descripcion = $row['descripcion'];
                    $cantidad = $row['cantidad'];
                    $precio = $row['precio'];
                    $descuento = $row['descuento'];
                    $id_iva = $row['id_iva'];
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

                    if ($id_iva) {
                        $unidades = $mysqli->query("SELECT * FROM iva where id=$id_iva");
                        while ($row = $unidades->fetch_assoc()) {
                            $iva = $row['iva'];
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $partida; ?></td>
                        <td><?= $cantidad; ?></td>
                        <td><?= round($precio, 2); ?>€</td>
                        <td><?= $iva; ?>%</td>
                        <td><?= round($total, 2); ?>€</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <!----------- END TABLE ----------->
    </div>
    <?php
    $getTotalFactura = mysqli_query($mysqli, "SELECT SUM(total) AS totalFactura FROM facturas_partidas where id_factura='$id_factura'");
    $result = mysqli_fetch_assoc($getTotalFactura);
    $totalFactura = round($result['totalFactura'] ?? 0, 2);

    $getSubtotalFactura = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalFactura FROM facturas_partidas where id_factura='$id_factura'");
    $result = mysqli_fetch_assoc($getSubtotalFactura);
    $subtotalFactura = round($result['subtotalFactura'] ?? 0, 2);
    ?>

</div>
<div class="invoice-box" style="margin-top: 20px">
    <div class="border border-secondary p-2 border-bottom-0 d-flex justify-content-between">
        <p class="text-black "><b>Base:</b> <?= $subtotalFactura; ?>€</p>
        <p class="text-black ">
            <?php
            $iva_totals = []; // Este arreglo guardará los totales por cada tipo de IVA
            $c72 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura");
            while ($row = $c72->fetch_assoc()) {
                $id_iva = $row['id_iva'];
                $subtotal = $row['subtotal'];
                $total = $row['total'];

                if ($id_iva) {
                    $resultIva = $mysqli->query("SELECT * FROM iva where id=$id_iva");
                    $rowIva = $resultIva->fetch_assoc();
                    $iva_rate = $rowIva['iva']; // Aquí obtenemos el valor real del IVA
                }

                // Calculamos el importe del IVA para el producto actual
                $iva_amount = $total - $subtotal;

                // Acumulamos el importe del IVA en el arreglo $iva_totals
                if (!isset($iva_totals[$iva_rate])) {
                    $iva_totals[$iva_rate] = 0;
                }
                $iva_totals[$iva_rate] += $iva_amount;
            }
            foreach ($iva_totals as $iva_rate => $amount) {
                ?>
                <tr>
                    <td><b>IVA (<?= $iva_rate; ?>%):</b></td>
                    <td><?= round($amount, 2); ?>€</td>
                </tr>
                <br>
                <?php
            }
            ?>
        </p>
        <p class="text-black "><b>Total:</b> <?= $totalFactura; ?>€</p>
    </div>
    <div class="border border-secondary p-2">
        <p class="text-black "><b>Método de pago:</b> <?= $iban; ?></p>
    </div>
    <div class="border border-secondary p-2 border-top-0">
        <p class="text-black "><b>NOTAS:</b></p>
        <p class="mt-1"><?= $nota; ?></p>
    </div>
    <div class="d-flex">
        <div class="border border-secondary p-2 w-50 border-top-0">
            <p class="text-black "><b>Fecha:</b> <?= $fecha_inicio; ?></p>
            <p class="text-black mt-2"><b>Firma Cliente:</b></p>
            <div style="height: 20px"></div>
        </div>
        <div class="border border-secondary p-2 w-50 border-top-0 border-start-0">
            <p class="text-black "><b>Empresa reparadora:</b></p>
            <div style="height: 20px"></div>
        </div>
    </div>
    <p class="mt-3" style="font-size: 9px !important;line-height: 1">Todos los datos facilitados por usted a este
        formulario serán tratados con estricta confidencialidad. En virtud de la L.O. 15/1999, de 13 de diciembre, de
        Protección de Datos de Carácter Personal, le informamos que todos los datos que usted nos facilita serán
        incluidos en un fichero de datos personales de APTA LA PLANA, S.L. para su tratamiento con la finalidad de
        realizar las gestiones oportunas referentes a facturación y para mantenerle informado de las novedades
        relacionadas con la compañía . Usted podrá ejercitar sus derechos de acceso, rectificación, cancelación, y
        oposición dirigiendose por escrito a APTA LA PLANA, S.L. a la dirección: P. I. Fadrell.Camino Fadrell, Nave 21
        12005 Castellón de la Plana
        <br><br>APTA LA PLANA, S.L. REGISTRO MERCANTIL DE CASTELLÓN, TOMO 1791, FOLIO 19, HOJA CS-42237, INSCR.1</p>
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