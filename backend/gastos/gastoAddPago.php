<?php
include "../../conexion.php";

$importe = filter_input(INPUT_POST, 'importe', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
$comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_SPECIAL_CHARS);
$id_gasto = filter_input(INPUT_POST, 'id_gasto', FILTER_SANITIZE_SPECIAL_CHARS);
$forma_pago = filter_input(INPUT_POST, 'forma_pago', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO gastos_pagos (id_gasto, importe, fecha, comentario,forma_pago,estado) VALUES ('$id_gasto', '$importe', '$fecha', '$comentario','$forma_pago','pendiente')";
mysqli_query($mysqli, $sql);

header("Location: ../../pages/gastoDetail.php?id_gasto=$id_gasto");
