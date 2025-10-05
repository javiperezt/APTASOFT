<?php
include "../../conexion.php";


$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos_partidas where id=$id_presupuestos_partidas");
while ($row = $c0->fetch_assoc()) {
    $subtotal = $row['subtotal'];
    $id_presupuesto = $row['id_presupuesto'];
    $total = $row['total'];
}

$subtotal_x_cantidad = $subtotal * $cantidad;
$total_x_cantidad = $total * $cantidad;

$sql0 = "UPDATE presupuestos_partidas set cantidad='$cantidad' where id=$id_presupuestos_partidas";
mysqli_query($mysqli, $sql0);


/*Recalculamos totales y subtotales del presupuesto
$getSubtotalPresupuesto = mysqli_query($mysqli, "SELECT SUM(cantidad*subtotal) AS subtotalPresupuesto FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
$result = mysqli_fetch_assoc($getSubtotalPresupuesto);
$subtotalPresupuesto = $result['subtotalPresupuesto'];

$getTotalPresupuesto = mysqli_query($mysqli, "SELECT SUM(cantidad*total) AS totalPresupuesto FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
$result = mysqli_fetch_assoc($getTotalPresupuesto);
$totalPresupuesto = $result['totalPresupuesto'];

$sql1 = "UPDATE presupuestos set subtotal=$subtotalPresupuesto,total=$totalPresupuesto where id=$id_presupuesto";
mysqli_query($mysqli, $sql1);*/

$arrayResult = ['subtotal_x_cantidad' => $subtotal_x_cantidad, 'total_x_cantidad' => $total_x_cantidad];
echo json_encode($arrayResult);