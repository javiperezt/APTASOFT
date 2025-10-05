<?php
include "../../conexion.php";


$id_presupuestos_subpartidas = filter_input(INPUT_POST, 'id_presupuestos_subpartidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id=$id_presupuestos_subpartidas");
while ($row = $c0->fetch_assoc()) {
    $id_presupuesto_partidas = $row['id_presupuesto_partidas'];
}

$sql = "DELETE FROM presupuestos_subpartidas where id=$id_presupuestos_subpartidas";
mysqli_query($mysqli, $sql);

//Recalculamos totales y subtotales de la partida
$getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuesto_partidas'");
$result = mysqli_fetch_assoc($getSubtotalPartida);
$subtotalPartida = $result['subtotalSubpartidas'];

$getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuesto_partidas'");
$result = mysqli_fetch_assoc($getTotalPartida);
$totalPartida = $result['totalSubpartidas'];


// Si ya no quedan subpartidas la suma dará null por lo tanto hay que forzarlo a 0
if(!$subtotalPartida){
    $subtotalPartida=0;
}
if(!$totalPartida){
    $totalPartida=0;
}

$sql1 = "UPDATE presupuestos_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_presupuesto_partidas";
mysqli_query($mysqli, $sql1);


$arrayResult = ['subtotalPartida' => $subtotalPartida, 'totalPartida' => $totalPartida];
echo json_encode($arrayResult);