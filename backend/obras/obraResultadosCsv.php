<?php
include "../../conexion.php";
include "../../authCookieSessionValidate.php";
include "../../DateClass.php";

$dateClass = new DateClass();
if (!$isLoggedIn) {
    header("Location: ../index.php");
}


$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

// Fetch records from database
$query = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");

if ($query->num_rows > 0) {
    $delimiter = ",";
    $filename = "resumenObra" . date('Y-m-d') . ".xls";

    // Create a file pointer
    $f = fopen('php://memory', 'w');

    // Set column headers
    $fields = array('Seccion', 'Partida', 'ManoObra_Gasto', 'ManoObra_Ingreso', 'ManoObra_Resultado', 'Material_Gasto', 'Material_Ingreso', 'Material_Resultado', 'OtrosProveedores_Gasto', 'OtrosProveedores_Ingreso', 'OtrosProveedores_Resultado', 'Resultado_Gastos', 'Resultado_Ingresos', 'ResultadoTotal');
    fputcsv($f, $fields, $delimiter);

    // Output each row of the data, format line as csv and write to file pointer
    while ($row = $query->fetch_assoc()) {
        $id_presupuesto = $row['id'];

        $presupuestos_partidas = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto order by id_capitulo");
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

            $lineData = array("$capitulo", "$partida", "$costeManoObra", "$ingresosManoObra", "$totalManoObra", "$costeMaterial", "$ingresosMaterial", "$totalMaterial", "$costeOtros", "$ingresosOtros", "$totalOtros", "$totalCostes", "$totalIngresos", "$totalResultado");
            fputcsv($f, $lineData, $delimiter);
        }
    }


// Move back to beginning of file
    fseek($f, 0);

// Set headers to download file rather than displayed
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer
    fpassthru($f);
}
exit;
?>