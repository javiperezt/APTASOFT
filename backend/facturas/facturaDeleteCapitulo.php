<?php
include "../../conexion.php";

$id_capitulo = filter_input(INPUT_POST, 'id_capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_factura = filter_input(INPUT_POST, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura and id_capitulo=$id_capitulo");
while ($row = $c0->fetch_assoc()) {
    $id_facturas_partidas = $row['id'];

    $sql = "DELETE FROM facturas_partidas where id=$id_facturas_partidas";
    mysqli_query($mysqli, $sql);
}

$sql2 = "DELETE FROM facturas_capitulos where id_capitulo=$id_capitulo and id_factura=$id_factura";
mysqli_query($mysqli, $sql2);