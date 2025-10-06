<?php
include "../../conexion.php";

$id_gasto_linea = filter_input(INPUT_POST, 'id_gasto_linea', FILTER_SANITIZE_SPECIAL_CHARS);

// Optimizado: Una sola query con JOIN para obtener todos los datos necesarios
$c0 = $mysqli->query("
    SELECT gl.*, i.iva
    FROM gastos_lineas gl
    LEFT JOIN iva i ON gl.id_iva = i.id
    WHERE gl.id = $id_gasto_linea
");

$row = $c0->fetch_assoc();
$id_gasto = $row['id_gasto'];
$cantidad = $row['cantidad'];
$descuento = $row['descuento'];
$precio = $row['precio'];
$iva = $row['iva'] ?? 0;

// Calculo subtotal y total de la linea de gasto teniendo en cuenta el descuento
$subtotalGasto = round($cantidad * $precio - ($cantidad * $precio * $descuento / 100), 2);
$totalGasto = round($subtotalGasto + ($subtotalGasto * ($iva / 100)), 2);

// Optimizado: Una sola query para actualizar y calcular totales
$sql = "UPDATE gastos_lineas SET subtotal=$subtotalGasto, total=$totalGasto WHERE id=$id_gasto_linea";
mysqli_query($mysqli, $sql);

// Optimizado: Una sola query para obtener todos los totales generales
$result = $mysqli->query("
    SELECT
        SUM(subtotal) AS subtotalGastoGeneral,
        SUM(total) AS totalGastoGeneral,
        SUM(cantidad * precio) AS importeSinDescuento
    FROM gastos_lineas
    WHERE id_gasto='$id_gasto'
")->fetch_assoc();

$subtotalGastoGeneral = $result['subtotalGastoGeneral'] ?? 0;
$totalGastoGeneral = $result['totalGastoGeneral'] ?? 0;
$importeSinDescuento = $result['importeSinDescuento'] ?? 0;
$dto = $importeSinDescuento - $subtotalGastoGeneral;

$arrayResult = [
    'subtotalGasto' => (float)$subtotalGasto,
    'totalGasto' => (float)$totalGasto,
    'subtotalGastoGeneral' => (float)$subtotalGastoGeneral,
    'totalGastoGeneral' => (float)$totalGastoGeneral,
    'dto' => (float)$dto,
    'importeSinDescuento' => (float)$importeSinDescuento
];
echo json_encode($arrayResult, JSON_NUMERIC_CHECK);