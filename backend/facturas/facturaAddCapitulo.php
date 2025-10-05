<?php
include "../../conexion.php";

$capitulo = filter_input(INPUT_POST, 'capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_factura = filter_input(INPUT_POST, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO capitulos (capitulo) VALUE ('$capitulo')";
mysqli_query($mysqli, $sql);
$id_capitulo = mysqli_insert_id($mysqli);

$sql1 = "INSERT INTO facturas_capitulos (id_factura, id_capitulo) VALUE ('$id_factura','$id_capitulo')";
mysqli_query($mysqli, $sql1);

header("Location: ../../pages/facturaDetail.php?id_factura=$id_factura");

