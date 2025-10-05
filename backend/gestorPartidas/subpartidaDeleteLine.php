<?php
include "../../conexion.php";

$id_subpartida = filter_input(INPUT_POST, 'id_subpartida', FILTER_SANITIZE_SPECIAL_CHARS);
$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM gestor_subpartidas where id=$id_subpartida";
mysqli_query($mysqli, $sql);

//Recalculamos totales y subtotales de la partida
$getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM gestor_subpartidas where id_partida='$id_partida'");
$result = mysqli_fetch_assoc($getSubtotalPartida);
$subtotalPartida = $result['subtotalSubpartidas'];

$getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM gestor_subpartidas where id_partida='$id_partida'");
$result = mysqli_fetch_assoc($getTotalPartida);
$totalPartida = $result['totalSubpartidas'];


// Si ya no quedan subpartidas la suma dará null por lo tanto hay que forzarlo a 0
if(!$subtotalPartida){
    $subtotalPartida=0;
}
if(!$totalPartida){
    $totalPartida=0;
}

$sql1 = "UPDATE gestor_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_partida";
mysqli_query($mysqli, $sql1);


$arrayResult = ['subtotalPartida' => $subtotalPartida, 'totalPartida' => $totalPartida];
echo json_encode($arrayResult);