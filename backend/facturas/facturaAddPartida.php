<?php
include "../../conexion.php";

$id_capitulo = filter_input(INPUT_POST, 'id_capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);
$id_factura = filter_input(INPUT_POST, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM gestor_partidas where id=$id_partida");
while ($row = $c0->fetch_assoc()) {
    $partida = $row['partida'];
    $codigo = $row['codigo'];
    $id_unidad = $row['id_unidad'];
    $descripcion = $row['descripcion'];
}

if ($id_unidad) {
    $c1 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
    while ($row = $c1->fetch_assoc()) {
        $simbolo = $row['simbolo'];
        $unidad = $row['unidad'];
    }
}


$sql = "INSERT INTO facturas_partidas (id_partida, id_factura, id_capitulo, id_unidad, partida, descripcion) 
VALUES ('$id_partida','$id_factura','$id_capitulo','$id_unidad','$partida','$descripcion')";
mysqli_query($mysqli, $sql);
$id_facturas_partidas = mysqli_insert_id($mysqli);


//Calculamos subtotal de la partida
$getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM gestor_subpartidas where id_partida='$id_partida'");
$result = mysqli_fetch_assoc($getSubtotalPartida);
$subtotalPartida = $result['subtotalSubpartidas'];

$sql2 = "UPDATE facturas_partidas set precio=$subtotalPartida where id=$id_facturas_partidas";
mysqli_query($mysqli, $sql2);

header("Location: ../../pages/facturaDetail.php?id_factura=$id_factura");

