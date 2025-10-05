<?php
include "../../conexion.php";

$id_subpartida = filter_input(INPUT_POST, 'id_subpartida', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM gestor_subpartidas where id=$id_subpartida");
while ($row = $c0->fetch_assoc()) {
    $id_partida = $row['id_partida'];
    $id_categoria = $row['id_categoria'];
    $concepto = $row['concepto'];
    $descripcion_subpartida = $row['descripcion'];
    $id_unidad_subpartida = $row['id_unidad'];
    $cantidad = $row['cantidad'];
    $id_iva_subpartida = $row['id_iva'];
    $precio = $row['precio'];
}
$c1 = $mysqli->query("SELECT * FROM iva where id=$id_iva_subpartida");
while ($row = $c1->fetch_assoc()) {
    $iva = $row['iva'];
}

// Calculo subtotal y total de la subpartida
$subtotalSubpartida = round($cantidad * $precio, 2);
$totalSubpartida = round($subtotalSubpartida + ($subtotalSubpartida * ($iva / 100)), 2);

$sql = "UPDATE gestor_subpartidas set subtotal=$subtotalSubpartida,total=$totalSubpartida where id=$id_subpartida";
mysqli_query($mysqli, $sql);


// Calculo subtotal y total de la partida
$getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM gestor_subpartidas where id_partida='$id_partida'");
$result = mysqli_fetch_assoc($getSubtotalPartida);
$subtotalPartida = $result['subtotalSubpartidas'];

$getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM gestor_subpartidas where id_partida='$id_partida'");
$result = mysqli_fetch_assoc($getTotalPartida);
$totalPartida = $result['totalSubpartidas'];

$sql1 = "UPDATE gestor_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_partida";
mysqli_query($mysqli, $sql1);

$arrayResult = ['subtotalSubpartida' => $subtotalSubpartida, 'totalSubpartida' => $totalSubpartida, 'subtotalPartida' => $subtotalPartida, 'totalPartida' => $totalPartida];
echo json_encode($arrayResult);