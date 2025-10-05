<?php
include "../../conexion.php";

$id_gasto_linea = filter_input(INPUT_POST, 'id_gasto_linea', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM gastos_lineas where id=$id_gasto_linea");
while ($row = $c0->fetch_assoc()) {
    $id_presupuestos_partidas = $row['id_presupuestos_partidas'];
    $id_capitulo_presupuesto = $row['id_capitulo_presupuesto'];
    $id_iva = $row['id_iva'];
    $id_gasto = $row['id_gasto'];
    $concepto = $row['concepto'];
    $descripcion = $row['descripcion'];
    $cantidad = $row['cantidad'];
    $descuento = $row['descuento'];
    $precio = $row['precio'];
    $subtotal = $row['subtotal'];
    $total = $row['total'];
}

$c1 = $mysqli->query("SELECT * FROM iva where id=$id_iva");
while ($row = $c1->fetch_assoc()) {
    $iva = $row['iva'];
}

// Calculo subtotal y total de la linea de gasto teniendo en cuenta el descuento
$subtotalGasto = round($cantidad * $precio - ($cantidad * $precio * $descuento / 100), 2);
$totalGasto = round($subtotalGasto + ($subtotalGasto * ($iva / 100)), 2);


//actualizamos total y subtotal en la linea
$sql = "UPDATE gastos_lineas set subtotal=$subtotalGasto,total=$totalGasto where id=$id_gasto_linea";
mysqli_query($mysqli, $sql);

//Calculamos totales y subtotales de todas las lineas de gasto (no es necesario pero pa tenerlo)
$getSubtotalGastoGeneral = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalGastoGeneral FROM gastos_lineas where id_gasto='$id_gasto'");
$result = mysqli_fetch_assoc($getSubtotalGastoGeneral);
$subtotalGastoGeneral = $result['subtotalGastoGeneral'];

$getTotalGastoGeneral = mysqli_query($mysqli, "SELECT SUM(total) AS totalGastoGeneral FROM gastos_lineas where id_gasto='$id_gasto'");
$result = mysqli_fetch_assoc($getTotalGastoGeneral);
$totalGastoGeneral = $result['totalGastoGeneral'];


$arrayResult = ['subtotalGasto' => $subtotalGasto, 'totalGasto' => $totalGasto, 'subtotalGastoGeneral' => $subtotalGastoGeneral, 'totalGastoGeneral' => $totalGastoGeneral];
echo json_encode($arrayResult);