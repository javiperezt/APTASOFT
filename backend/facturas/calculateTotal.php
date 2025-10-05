<?php
include "../../conexion.php";

$id_facturas_partidas = filter_input(INPUT_POST, 'id_facturas_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM facturas_partidas where id='$id_facturas_partidas'");
while ($row = $c0->fetch_assoc()) {
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
}

if ($id_iva) {
    $c1 = $mysqli->query("SELECT * FROM iva where id=$id_iva");
    while ($row = $c1->fetch_assoc()) {
        $iva = $row['iva'];
    }
}

// Calculo subtotal y total de la partida teniendo en cuenta el descuento
$subtotalPartida = round($cantidad * $precio - ($cantidad * $precio * $descuento / 100), 2);
$totalPartida = round($subtotalPartida + ($subtotalPartida * ($iva / 100)), 2);

$sql = "UPDATE facturas_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_facturas_partidas";
mysqli_query($mysqli, $sql);

$arrayResult = ['subtotalPartida' => $subtotalPartida, 'totalPartida' => $totalPartida];
echo json_encode($arrayResult);