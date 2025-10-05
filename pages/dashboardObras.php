<?php
session_start();

include "../conexion.php";
require_once "../DateClass.php";
require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$dateClass = new DateClass();

$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_empresa = $row['id_empresa'];
    $id_canal_entrada = $row['id_canal_entrada'];
    $id_usuario_asignado = $row['id_usuario_asignado'];
    $id_estado = $row['id_estado'];
    $titulo = $row['titulo'];
    $fecha_inicio = $row['fecha_inicio'];
    $fecha_fin = $row['fecha_fin'];
    $localizacion = $row['localizacion'];
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
        <a href="obras.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Obra Resultados</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboard.php">Resultados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboardGeneral.php">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="dashboardObras.php">Obras</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <!----------- FILTROS ----------->
    <div class="d-flex align-items-center">
        <div class="ms-auto">
            <a href="../backend/obras/obraResultadosCsv.php?id_obra=<?= $id_obra; ?>" target="_blank"
               class="btn btn-outline-secondary d-flex align-items-center" style="height: 38px"> <i
                        class="bi bi-cloud-arrow-down fs-6"></i></a>
        </div>
    </div>
    <!----------- END FILTROS ----------->


    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Seccion</th>
                <th scope="col">Partida</th>
                <th colspan="3" scope="col">Mano de Obra</th>
                <th colspan="3" scope="col">Material</th>
                <th colspan="3" scope="col">Otros proveedores</th>
                <th colspan="3" scope="col">Resultado</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $presupuestos = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
            while ($row = $presupuestos->fetch_assoc()) {
                $id_presupuesto = $row['id'];

                $presupuestos_partidas = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
                while ($row = $presupuestos_partidas->fetch_assoc()) {
                    $id_presupuestos_partidas = $row['id'];
                    $id_capitulo = $row['id_capitulo'];
                    $partida = $row['partida'];
                    $descripcion = $row['descripcion'];
                    $fecha_inicio = $row['fecha_inicio'];
                    $fecha_vencimiento = $row['fecha_vencimiento'];
                    $cantidad_partida = $row['cantidad'];


                    //  ----------- CALCULO COSTE MANO DE OBRA -------------- //
                    //  Extraemos el tiempo imputado a cada subpartida por los trajadores
                    $horas = 0;
                    $seconds = 0;
                    $totalSeconds = 0;
                    $totalHoras = 0;
                    $costeManoObra = 0;
                    $obras_registro_partes = $mysqli->query("SELECT * FROM obras_registro_partes where id_presupuesto=$id_presupuesto and id_presupuestos_partidas=$id_presupuestos_partidas");
                    while ($row = $obras_registro_partes->fetch_assoc()) {
                        $horas = $row['horas'];
                        $seconds = $dateClass->getSecondsFromFormatedHour("$horas");
                        $totalSeconds += $seconds;
                    }

                    $totalHoras = $totalSeconds / 3600;
                    $precioHoraTrabajador = 20; // SE PONE 20€/h A MANO ESTIMADO
                    $costeManoObra = round($totalHoras * $precioHoraTrabajador, 2);


                    //  ----------- CALCULO INGRESOS MANO DE OBRA -------------- //
                    $subtotal_subpartida_MObra = 0;
                    $subtotales_subpartida_MObra = 0;
                    $ingresosManoObra = 0;
                    $totalManoObra = 0;
                    $presupuestos_subpartidas = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=1");
                    while ($row = $presupuestos_subpartidas->fetch_assoc()) {
                        $subtotal_subpartida_MObra = $row['subtotal'];
                        $subtotales_subpartida_MObra += $subtotal_subpartida_MObra;
                    }

                    $ingresosManoObra = round($subtotales_subpartida_MObra * $cantidad_partida, 2);

                    $totalManoObra = $ingresosManoObra - $costeManoObra;


                    //  ----------- CALCULO COSTE MATERIAL -------------- //
                    $costeMaterial = 0;
                    $id_gasto = "";
                    $subtotal_Material = "";
                    $gastos = $mysqli->query("SELECT * FROM gastos where id_obra=$id_obra and id_categoria_gasto=2 and is_active=1");
                    while ($row = $gastos->fetch_assoc()) {
                        $id_gasto = $row['id'];
                        if ($id_gasto) {
                            $gastos_lineas = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto='$id_gasto' and id_presupuestos_partidas='$id_presupuestos_partidas'");
                            while ($row = $gastos_lineas->fetch_assoc()) {
                                $subtotal_Material = $row['subtotal'];
                                $costeMaterial += $subtotal_Material;
                            }
                        }
                    }


                    //  ----------- CALCULO INGRESOS MATERIAL -------------- //
                    $subtotal_subpartida_Material = "";
                    $subtotales_subpartida_Material = "";
                    $ingresosMaterial = 0;
                    $totalMaterial = 0;
                    $presupuestos_subpartidas2 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=2");
                    while ($row = $presupuestos_subpartidas2->fetch_assoc()) {
                        $subtotal_subpartida_Material = $row['subtotal'];
                        $subtotales_subpartida_Material += $subtotal_subpartida_Material;
                    }

                    $ingresosMaterial = round($subtotales_subpartida_Material * $cantidad_partida, 2);

                    $totalMaterial = $ingresosMaterial - $costeMaterial;


                    //  ----------- CALCULO COSTE OTROS -------------- //
                    $costeOtros = 0;
                    $id_gasto2 = "";
                    $subtotal_Otros = "";
                    $gastos2 = $mysqli->query("SELECT * FROM gastos where id_obra=$id_obra and id_categoria_gasto=3 and is_active=1");
                    while ($row = $gastos2->fetch_assoc()) {
                        $id_gasto2 = $row['id'];
                        if ($id_gasto) {
                            $gastos_lineas2 = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto='$id_gasto2' and id_presupuestos_partidas='$id_presupuestos_partidas'");
                            while ($row = $gastos_lineas2->fetch_assoc()) {
                                $subtotal_Otros = $row['subtotal'];
                                $costeOtros += $subtotal_Otros;
                            }
                        }
                    }

                    //  ----------- CALCULO INGRESOS OTROS -------------- //
                    $subtotal_subpartida_Otros = "";
                    $subtotales_subpartida_Otros = "";
                    $ingresosOtros = 0;
                    $totalOtros = 0;
                    $presupuestos_subpartidas3 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=3");
                    while ($row = $presupuestos_subpartidas3->fetch_assoc()) {
                        $subtotal_subpartida_Otros = $row['subtotal'];
                        $subtotales_subpartida_Otros += $subtotal_subpartida_Otros;
                    }

                    $ingresosOtros = round($subtotales_subpartida_Otros * $cantidad_partida, 2);

                    $totalOtros = $ingresosOtros - $costeOtros;

                    //  ----------- CALCULO RESULTADO TOTAL -------------- //
                    $totalCostes = 0;
                    $totalIngresos = 0;
                    $totalResultado = 0;
                    $totalCostes = $costeManoObra + $costeMaterial + $costeOtros;
                    $totalIngresos = $ingresosManoObra + $ingresosMaterial + $ingresosOtros;
                    $totalResultado = $totalIngresos - $totalCostes;


                    $c4 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                    while ($row = $c4->fetch_assoc()) {
                        $capitulo = $row['capitulo'];
                    }

                    include "../components/obras/dashboardLine.php";
                }
            }
            if ($presupuestos->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>


            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->
</div>


</body>
<?php include "../components/modals/obraNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
</html>