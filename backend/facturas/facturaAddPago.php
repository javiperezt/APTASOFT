<?php
include "../../conexion.php";

$importe = filter_input(INPUT_POST, 'importe', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
$comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_SPECIAL_CHARS);
$id_factura = filter_input(INPUT_POST, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);
$forma_pago = filter_input(INPUT_POST, 'forma_pago', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO facturas_pagos (id_factura, importe, fecha, comentario, forma_pago) VALUES ('$id_factura', '$importe', '$fecha', '$comentario' , '$forma_pago')";
mysqli_query($mysqli, $sql);

header("Location: ../../pages/facturaDetail.php?id_factura=$id_factura");
