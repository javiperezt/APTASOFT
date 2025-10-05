<?php
include "../../conexion.php";

$id_capitulo = filter_input(INPUT_POST, 'id_capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_factura = filter_input(INPUT_POST, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);


$sql = "INSERT INTO facturas_partidas (id_factura, id_capitulo) VALUES ('$id_factura','$id_capitulo')";
mysqli_query($mysqli, $sql);

header("Location: ../../pages/facturaDetail.php?id_factura=$id_factura");

