<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
$year = date("Y");
$month = date("m");
$day = date("d");


$f_desde = $_GET['desde'];
$f_hasta = $_GET['hasta'];

$f_id_empresa = $_GET['id_empresa'];
if (!$f_id_empresa) {
    $f_id_empresa = 1;
}
$filter = "AND id_empresa=$f_id_empresa";
$filter2 = "";

if ($f_desde) {
    $filter = "AND fecha_inicio>='$f_desde' AND id_empresa=$f_id_empresa";
    $filter2 = "AND fecha>='$f_desde'";
}
if ($f_hasta) {
    $filter = "AND fecha_inicio<='$f_hasta' AND id_empresa=$f_id_empresa";
    $filter2 = "AND fecha<='$f_hasta'";

}
if ($f_desde && $f_hasta) {
    $filter = "AND id_empresa=$f_id_empresa AND fecha_inicio BETWEEN '$f_desde' AND '$f_hasta' ";
    $filter2 = "AND fecha BETWEEN '$f_desde' AND '$f_hasta'";
}
if (!$f_desde) {
    $f_desde = "$year-$month-$day";
    $filter = "AND fecha_inicio>='$f_desde' AND id_empresa=$f_id_empresa";
    $filter2 = "AND fecha>='$f_desde'";
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
    <p class="text-black fw-bold fs-3">Dashboard</p>

    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboard.php">Resultados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="dashboardGeneral.php">General</a>
            </li>
            <!--<li class="nav-item">
                <a class="nav-link text-black" href="dashboardObras.php">Obras</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboardCaja.php">Caja</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->

<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <?php include "../components/empresaFilter.php"; ?>
        <div class="d-flex gap-2">
            <div>
                <p>Desde:</p>
                <input onchange="filterHandler()" id="desde" type="date" class="form-control" value="<?= $f_desde; ?>">
            </div>
            <div>
                <p>Hasta:</p>
                <input onchange="filterHandler()" id="hasta" type="date" class="form-control" value="<?= $f_hasta; ?>">
            </div>
        </div>
    </div>
</div>


<!----------------- CONT 1 ----------------->
<div class="container">
    <div class="row">
        <!----------------- GASTOS ----------------->
        <div class="col-6 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <?php
                $id_gasto = "";
                $subtotal = 0;
                $TOTALGASTOS = 0;
                $cXX = $mysqli->query("SELECT * FROM gastos where is_active=1 $filter");
                while ($row = $cXX->fetch_assoc()) {
                    $id_gasto = $row['id'];

                    if ($id_gasto) {
                        $c1X = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto=$id_gasto");
                        while ($row = $c1X->fetch_assoc()) {
                            $subtotal = $row['subtotal'];
                            $TOTALGASTOS += $subtotal;
                        }
                    }
                }
                ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-bold fs-4">Gastos</p>
                    <p class="btn btn-danger"><?= $TOTALGASTOS; ?>€</p>
                </div>
                <div style="max-height: 500px;overflow: auto;">
                    <table class="table tableComon">
                        <thead>
                        <tr>
                            <th scope="col">Cuentas</th>
                            <th scope="col">Importe</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $id_cuenta = "";
                        $c0 = $mysqli->query("SELECT * FROM cuentas_gasto");
                        $totalesPorCuenta = array();
                        while ($row = $c0->fetch_assoc()) {
                            $id_cuenta = $row['id'];
                            $nombre = $row['nombre'];
                            $referencia = $row['referencia'];

                            if (array_key_exists($id_cuenta, $totalesPorCuenta)) {
                                // La cuenta ya ha sido procesada, saltar a la siguiente iteración
                                continue;
                            }

                            $id_gasto = "";
                            $totalGastos = 0;
                            $c2 = $mysqli->query("SELECT * FROM gastos where id_cuenta=$id_cuenta and is_active=1 $filter");
                            while ($row = $c2->fetch_assoc()) {
                                $id_gasto = $row['id'];

                                if ($id_gasto) {
                                    $c1 = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto=$id_gasto");
                                    while ($row = $c1->fetch_assoc()) {
                                        $subtotal = $row['subtotal'];
                                        $totalGastos += $subtotal;
                                    }
                                }
                            }

                            if ($totalGastos > 0) {
                                $totalesPorCuenta[$id_cuenta] = $totalGastos;
                                include "../components/dashboard/gastoLine.php";
                            }
                        }
                        if ($c0->num_rows == 0) {
                            include "../components/noDataLine.php";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!----------------- END GASTOS ----------------->


        <!----------------- INGRESOS ----------------->
        <div class="col-6 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <?php
                $id_factura = "";
                $subtotal = 0;
                $TOTALINGRESOS = 0;
                $cXXX = $mysqli->query("SELECT * FROM facturas where is_active=1 $filter");
                while ($row = $cXXX->fetch_assoc()) {
                    $id_factura = $row['id'];

                    if ($id_factura) {
                        $c2X = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura");
                        while ($row = $c2X->fetch_assoc()) {
                            $subtotal = $row['subtotal'];
                            $TOTALINGRESOS += $subtotal;
                        }
                    }
                }
                ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-bold fs-4">Ingresos</p>
                    <p class="btn btn-success"><?= $TOTALINGRESOS; ?>€</p>
                </div>
                <div style="max-height: 500px;overflow: auto;">
                    <table class="table tableComon">
                        <thead>
                        <tr>
                            <th scope="col">Cuentas</th>
                            <th scope="col">Importe</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $id_cuenta = "";
                        $c0 = $mysqli->query("SELECT * FROM cuentas_ingreso");
                        $totalesPorCuenta = array();
                        while ($row = $c0->fetch_assoc()) {
                            $id_cuenta = $row['id'];
                            $nombre = $row['nombre'];
                            $referencia = $row['referencia'];

                            if (array_key_exists($id_cuenta, $totalesPorCuenta)) {
                                // La cuenta ya ha sido procesada, saltar a la siguiente iteración
                                continue;
                            }

                            $id_factura = "";
                            $totalIngresos = 0;
                            $c2 = $mysqli->query("SELECT * FROM facturas where id_cuenta=$id_cuenta and is_active=1 $filter");
                            while ($row = $c2->fetch_assoc()) {
                                $id_factura = $row['id'];

                                if ($id_factura) {
                                    $c1 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura");
                                    while ($row = $c1->fetch_assoc()) {
                                        $subtotal = $row['subtotal'];
                                        $totalIngresos += $subtotal;
                                    }
                                }
                            }

                            if ($totalIngresos > 0) {
                                $totalesPorCuenta[$id_cuenta] = $totalIngresos;
                                include "../components/dashboard/ingresoLine.php";
                            }
                        }
                        if ($c0->num_rows == 0) {
                            include "../components/noDataLine.php";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!----------------- END INGRESOS ----------------->
    </div>
    <!----------------- END CONT 1 ----------------->


    <!----------------- CONT 2 ----------------->
    <div class="row">

        <!----------------- PAGOS ----------------->
        <div class="col-6 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-bold fs-4">Pagos</p>
                    <!--<p class="btn btn-danger">10,000€</p>-->
                </div>
                <div style="max-height: 500px;overflow: auto;">
                    <table class="table tableComon">
                        <thead>
                        <tr>
                            <th scope="col">Estado</th>
                            <th width="100px" scope="col">Importe</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $importe = 0;
                        $totalPagos = 0;
                        $id_gasto = "";
                        $c0 = $mysqli->query("SELECT * FROM gastos_pagos where estado='pagado' $filter2");
                        while ($row = $c0->fetch_assoc()) {
                            $importe = $row['importe'];
                            $id_gasto = $row['id_gasto'];

                            if ($id_gasto) {
                                $cX = $mysqli->query("SELECT * FROM gastos where id=$id_gasto and is_active=1");
                                if ($cX->num_rows > 0) {
                                    $totalPagos += $importe;
                                }
                            }
                        }

                        $importePendiente = 0;
                        $totalPagosPendientes = 0;
                        $id_gasto = "";
                        $c00 = $mysqli->query("SELECT * FROM gastos_pagos where estado='pendiente' $filter2");
                        while ($row = $c00->fetch_assoc()) {
                            $importePendiente = $row['importe'];
                            $id_gasto = $row['id_gasto'];

                            if ($id_gasto) {
                                $cX1 = $mysqli->query("SELECT * FROM gastos where id=$id_gasto and is_active=1");
                                if ($cX1->num_rows > 0) {
                                    $totalPagosPendientes += $importePendiente;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td scope="col">Recibido</td>
                            <td scope="col"><?= $totalPagos; ?>€</td>
                        </tr>
                        <tr>
                            <td scope="col">Pendiente</td>
                            <td scope="col"><?= $totalPagosPendientes; ?>€</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!----------------- END PAGOS ----------------->


        <!----------------- COBROS ----------------->
        <div class="col-6 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-bold fs-4">Cobros</p>
                    <!--<p class="btn btn-success">10,000€</p>-->
                </div>
                <div style="max-height: 500px;overflow: auto;">
                    <table class="table tableComon">
                        <thead>
                        <tr>
                            <th scope="col">Estado</th>
                            <th width="100px" scope="col">Importe</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $importe = 0;
                        $totalPagos = 0;
                        $id_factura = "";
                        $c0 = $mysqli->query("SELECT * FROM facturas_pagos where estado='pagado' $filter2");
                        while ($row = $c0->fetch_assoc()) {
                            $importe = $row['importe'];
                            $id_factura = $row['id_factura'];

                            if ($id_factura) {
                                $cX2 = $mysqli->query("SELECT * FROM facturas where id=$id_factura  and is_active=1");
                                if ($cX2->num_rows > 0) {
                                    $totalPagos += $importe;
                                }
                            }
                        }

                        $importePendiente = 0;
                        $totalPendiente = 0;
                        $id_factura = "";
                        $c0 = $mysqli->query("SELECT * FROM facturas_pagos where estado='pendiente' $filter2");
                        while ($row = $c0->fetch_assoc()) {
                            $importePendiente = $row['importe'];
                            $id_factura = $row['id_factura'];

                            if ($id_factura) {
                                $cX3 = $mysqli->query("SELECT * FROM facturas where id=$id_factura and is_active=1");
                                if ($cX3->num_rows > 0) {
                                    $totalPendiente += $importePendiente;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td scope="col">Pagado</td>
                            <td scope="col"><?= $totalPagos; ?>€</td>
                        </tr>
                        <tr>
                            <td scope="col">Pendiente</td>
                            <td scope="col"><?= $totalPendiente; ?>€</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!----------------- END COBROS ----------------->
    </div>
    <!----------------- END CONT 2 ----------------->


    <!----------------- RESULTADOS ----------------->
    <!--<div class="row">
        <div class="col-6 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="fw-bold fs-5">Resultado</p>
                    <p class="btn btn-primary">10,000€</p>
                </div>
            </div>
        </div>

        <div class="col-6 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="fw-bold fs-5">Variación de caja</p>
                    <p class="btn btn-primary">10,000€</p>
                </div>
            </div>
        </div>

    </div>-->
    <!----------------- END RESULTADOS ----------------->
</div>
</body>

<script>
    const filterHandler = () => {
        var desde = $("#desde").val();
        var id_empresa = $("input[name='btnradio']:checked").val();
        var hasta = $("#hasta").val();

        location.href = "dashboardGeneral.php?desde=" + desde + "&hasta=" + hasta + "&id_empresa=" + id_empresa;
    }
</script>
</html>