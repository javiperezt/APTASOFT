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
    $id_cuenta = $row['id_cuenta'];
    $id_obra = $row['id_obra'];
    $id_estado = $row['id_estado'];
    $id_empresa = $row['id_empresa'];
    $pref_ref = $row['pref_ref'];
    $pref_ref_year = $row['pref_ref_year'];
    $ref2 = $row['ref'];
    $ref = $pref_ref . $pref_ref_year . $ref2;
    $asunto = $row['asunto'];
    $fecha_inicio = $row['fecha_inicio'];
    $nota = $row['nota'];
    $fecha_vencimiento = $row['fecha_vencimiento'];
}

$c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
while ($row = $c1->fetch_assoc()) {
    $nombre_contacto = $row['nombre'];
}

$c2 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c2->fetch_assoc()) {
    $titulo_obra = $row['titulo'];
}

if ($id_estado) {
    $c3 = $mysqli->query("SELECT * FROM facturas_estados where id=$id_estado");
    while ($row = $c3->fetch_assoc()) {
        $estado = $row['estado'];
    }
}

$getSubtotalFactura = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalFactura FROM facturas_partidas where id_factura='$id_factura'");
$result = mysqli_fetch_assoc($getSubtotalFactura);
$subtotalFactura = round($result['subtotalFactura'], 2);

$getTotalFactura = mysqli_query($mysqli, "SELECT SUM(total) AS totalFactura FROM facturas_partidas where id_factura='$id_factura'");
$result = mysqli_fetch_assoc($getTotalFactura);
$totalFactura = round($result['totalFactura'], 2);

//para saber cantidad descontada
$getImporteSinDescuento = mysqli_query($mysqli, "SELECT SUM(cantidad*precio) AS importeSinDescuento FROM facturas_partidas where id_factura='$id_factura'");
$result = mysqli_fetch_assoc($getImporteSinDescuento);
$importeSinDescuento = $result['importeSinDescuento'];

$dto = $importeSinDescuento - $subtotalFactura;


$getTotalPagado = mysqli_query($mysqli, "SELECT SUM(importe) AS x FROM facturas_pagos where estado='pagado' and id_factura=$id_factura");
$result = mysqli_fetch_assoc($getTotalPagado);
$totalPagado = $result['x'];
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
        <a href="<?= $_SERVER['HTTP_REFERER']; ?>"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Factura <?= $ref; ?></p>
    </div>
    <div>
        <a href="facturaPdf.php?id_factura=<?= $id_factura; ?>" target="_blank"
           class="btn btn-outline-secondary btn-sm me-4">Descargar PDF<i
                    class="bi bi-file-earmark-text-fill ms-2"></i></a>
        <button class="btn btn-secondary btn-sm me-4" data-bs-toggle="modal" data-bs-target="#facturaAddPago">+
            Registrar
            cobro
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#facturaAddCapitulo">Añadir
            Capítulo
        </button>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#facturaAddPartida">Añadir Partida
            Predefinida
        </button>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#facturaAddPartidaBlanco">
            Añadir Partida en Blanco
        </button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->

<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-4">
            <select class="selectizeSearch selectizeSearchBig" id="id_contacto"
                    onchange="updateGenerico('facturas','id_contacto',<?= $id_factura; ?>,this.value)">
                <option selected value="">Todos los clientes</option>
                <?php
                $s_contactos = $mysqli->query("SELECT * FROM contactos");
                while ($row = $s_contactos->fetch_assoc()) {
                    $s_id_contacto = $row['id'];
                    $s_nombre_contacto = $row['nombre'];

                    $selected = "";
                    if ($id_contacto == $s_id_contacto) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_contacto'>$s_nombre_contacto</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-4">
            <select onchange="updateGenerico('facturas','id_obra',<?= $id_factura; ?>,this.value)"
                    class="selectizeSearch selectizeSearchBig">
                <?php
                $getObras = $mysqli->query("SELECT * FROM obras");
                while ($row = $getObras->fetch_assoc()) {
                    $s_id_obra = $row['id'];
                    $s_titulo = $row['titulo'];

                    $selected = "";
                    if ($id_obra == $s_id_obra) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_obra'>$s_titulo</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('facturas','fecha_inicio',<?= $id_factura; ?>,this.value)"
                       required type="date" class="form-control" value="<?= $fecha_inicio; ?>">
                <label>Fecha</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('facturas','fecha_vencimiento',<?= $id_factura; ?>,this.value)"
                       required type="date" class="form-control" value="<?= $fecha_vencimiento; ?>">
                <label>Vencimiento</label>
            </div>
        </div>
        <div class="col-4">
            <select onchange="updateGenerico('facturas','id_cuenta',<?= $id_factura; ?>,this.value)"
                    class="selectizeSearch selectizeSearchBig">
                <option selected disabled hidden>Asignar cuenta</option>
                <?php
                $getExpensesTypes = $mysqli->query("SELECT * FROM cuentas_ingreso where is_active=1");
                while ($row = $getExpensesTypes->fetch_assoc()) {
                    $id_ingresos_cuentas = $row['id'];
                    $referencia_ingresos = $row['referencia'];
                    $tipo_ingresos = $row['nombre'];

                    $selected = "";
                    if ($id_cuenta == $id_ingresos_cuentas) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$id_ingresos_cuentas'>$referencia_ingresos. $tipo_ingresos</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-4">
            <div class="d-flex align-items-center gap-1">
                <div class="form-floating ">
                    <input oninput="updateGenerico('facturas','pref_ref',<?= $id_factura; ?>,this.value)"
                           placeholder="Prefijo"
                           required type="text" class="form-control border-primary" value="<?= $pref_ref; ?>">
                    <label>Prefijo</label>
                </div>
                <div class="form-floating">
                    <input oninput="updateGenerico('facturas','pref_ref_year',<?= $id_factura; ?>,this.value)" placeholder="Año"
                           required type="text" class="form-control border-primary" value="<?= $pref_ref_year; ?>">
                    <label>Año</label>
                </div>
                <div class="form-floating">
                    <input oninput="updateGenerico('facturas','ref',<?= $id_factura; ?>,this.value)"
                           placeholder="Terminación"
                           required type="text" class="form-control border-primary" value="<?= $ref2; ?>">
                    <label>Terminación</label>
                </div>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('facturas','id_estado',<?= $id_factura; ?>,this.value)"
                        class="form-select" id="floatingSelect">
                    <option selected disabled hidden>Estado</option>
                    <?php
                    $c4 = $mysqli->query("SELECT * FROM facturas_estados");
                    while ($row = $c4->fetch_assoc()) {
                        $id_facturas_estados = $row['id'];
                        $estado = $row['estado'];

                        $selected = "";
                        if ($id_facturas_estados == $id_estado) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_facturas_estados'>$estado</option>";
                    }
                    ?>
                </select>
                <label>Estado</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('facturas','id_empresa',<?= $id_factura; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Empresa</option>
                    <?php
                    $getEmpresas = $mysqli->query("SELECT * FROM empresas");
                    while ($row = $getEmpresas->fetch_assoc()) {
                        $id_empresa2 = $row['id'];
                        $empresa = $row['empresa'];

                        $selected = "";
                        if ($id_empresa == $id_empresa2) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_empresa2'>$empresa</option>";
                    }
                    ?>
                </select>
                <label>Empresa</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <input onfocusout="updateGenerico('facturas','asunto',<?= $id_factura; ?>,this.value)" required
                       type="text" class="form-control" placeholder="Asunto" value="<?= $asunto; ?>">
                <label>Asunto</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <input onfocusout="updateGenerico('facturas','nota',<?= $id_factura; ?>,this.value)"
                       type="text" class="form-control" placeholder="Nota" value="<?= $nota; ?>">
                <label>Nota</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- CAPÍTULOS ----------->
<?php
$c5 = $mysqli->query("SELECT * FROM facturas_capitulos where id_factura=$id_factura");
while ($row = $c5->fetch_assoc()) {
    $id_capitulo = $row['id_capitulo'];

    $c6 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
    while ($row = $c6->fetch_assoc()) {
        $capitulo = $row['capitulo'];
    }

    include "../components/modals/facturaDeleteCapitulo.php";
    ?>
    <div class="bg-white container-md my-3 rounded-3 p-4">
        <div class="d-flex align-items-center justify-content-between">
            <p class="text-black fw-bold fs-5 "><?= $capitulo; ?></p>
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#facturaDeleteCapitulo<?= $id_capitulo; ?>"><i
                        class="bi bi-trash-fill"></i></button>
        </div>

        <!----------- TABLE ----------->
        <table class="table tableComon mt-3">
            <thead>
            <tr>
                <th scope="col">Partida</th>
                <th scope="col">Descripción</th>
                <th width="80px" scope="col">Ud</th>
                <th width="110px" scope="col">Cantidad</th>
                <th width="100px" scope="col">Precio</th>
                <th width="80px" scope="col">Dto</th>
                <th width="90px" scope="col">IVA</th>
                <th width="55px" scope="col">Subt</th>
                <th width="55px" scope="col">Total</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c7 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura and id_capitulo=$id_capitulo");
            while ($row = $c7->fetch_assoc()) {
                $id_facturas_partidas = $row['id'];
                $id_partida = $row['id_partida'];
                $id_factura = $row['id_factura'];
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

                if ($id_unidad) {
                    $unidades = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
                    while ($row = $unidades->fetch_assoc()) {
                        $unidad = $row['simbolo'];
                    }
                }

                include "../components/facturas/facturaPartidaLine.php";
            }
            if ($c7->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
        <!----------- END TABLE ----------->
    </div>
    <?php
}
?>
<!----------- END CAPÍTULOS ----------->


<!----------- PAGOS ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-black fw-bold fs-5 ">Ingresos</p>
        <div id="pagos2" class="d-flex align-items-center gap-4">
            <p class="text-success"><span class="text-black">Ingresado:</span> <?= round($totalPagado, 2); ?>€</p>
            <p class="text-danger"><span
                        class="text-black">Pendiente:</span> <?= round($totalFactura - $totalPagado, 2); ?>€</p>
        </div>
    </div>

    <!----------- TABLE ----------->
    <table id="pagos" class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Comentario</th>
            <th scope="col">Forma pago</th>
            <th width="150px" scope="col">Estado</th>
            <th scope="col">Importe</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cPagos = $mysqli->query("SELECT * FROM facturas_pagos where id_factura=$id_factura order by fecha");
        while ($row = $cPagos->fetch_assoc()) {
            $id_pago = $row['id'];
            $importe = $row['importe'];
            $fecha = $row['fecha'];
            $estado = $row['estado'];
            $forma_pago = $row['forma_pago'];
            $comentario = $row['comentario'];

            include "../components/facturas/facturaPagoLine.php";
        }
        if ($cPagos->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END PAGOS ----------->


<!----------- RESUMEN ----------->
<div id="result" class="container-md">
    <div class="d-flex gap-4 bg-white p-4 rounded-3 ms-auto" style="width: fit-content">
        <div>
            <p class="fs-3 text-black fw-bold"><?= $subtotalFactura; ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Subtotal</p>
        </div>
        <div>
            <p class="fs-3 text-black fw-bold"><?= $totalFactura - $subtotalFactura; ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">IVA</p>
        </div>
        <div>
            <p class="fs-3 text-black fw-bold"><?= $dto; ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Dto</p>
        </div>
        <div>
            <p class="fs-3 text-black fw-bold"><?= $totalFactura; ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Total</p>
        </div>
    </div>
</div>
<!----------- END RESUMEN ----------->


<div class="container text-end mt-3">
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#facturaDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>

</body>
<?php include "../components/modals/facturaAddCapitulo.php"; ?>
<?php include "../components/modals/facturaAddPartida.php"; ?>
<?php include "../components/modals/facturaAddPartidaBlanco.php"; ?>
<?php include "../components/modals/facturaAddPago.php"; ?>
<?php include "../components/modals/facturaDelete.php"; ?>
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
            if (columna == "cantidad" || columna == "precio" || columna == "descuento" || columna == "id_iva") {
                calculateTotal(fila);
            } else {
                showMessage();
            }
            if (columna == "estado") {
                $("#estado" + fila).load(location.href + " #estado" + fila);
                $("#pagos2").load(location.href + " #pagos2");
            }
        });
    }

    // Calcular subtotal y total
    const calculateTotal = (id_facturas_partidas) => {
        $.ajax({
            method: "POST",
            url: "../backend/facturas/calculateTotal.php",
            dataType: 'json',
            data: {
                id_facturas_partidas: id_facturas_partidas
            }
        }).done(function (data) {
            totalPartida = data['totalPartida'];
            subtotalPartida = data['subtotalPartida'];

            $("#subtotal" + id_facturas_partidas).text(subtotalPartida + "€");
            $("#total" + id_facturas_partidas).text(totalPartida + "€");
            $("#result").load(location.href + " #result");
        });
    }


    const facturaDeleteCapitulo = (id_capitulo, id_factura) => {
        $.ajax({
            method: "POST",
            url: "../backend/facturas/facturaDeleteCapitulo.php",
            data: {
                id_capitulo: id_capitulo,
                id_factura: id_factura
            }
        }).done(function () {
            location.reload();
        });
    }

    // Eliminar un pago registrado
    const facturaDeletePago = (id_pago) => {
        $.ajax({
            method: "POST",
            url: "../backend/facturas/facturaDeletePago.php",
            data: {
                id_pago: id_pago
            }
        }).done(function () {
            $("#pagos").load(location.href + " #pagos");
            $("#pagos2").load(location.href + " #pagos2");
        });
    }


    const facturaDeletePartida = (id_facturas_partidas) => {
        $.ajax({
            method: "POST",
            url: "../backend/facturas/facturaDeletePartida.php",
            data: {
                id_facturas_partidas: id_facturas_partidas
            }
        }).done(function () {
            location.reload();
        });
    }

    // Eliminar factura completa
    const facturaDelete = (id_factura) => {
        $.ajax({
            method: "POST",
            url: "../backend/facturas/facturaDelete.php",
            data: {
                id_factura: id_factura
            }
        }).done(function () {
            location.href = "facturas.php";
        });
    }
</script>
</html>