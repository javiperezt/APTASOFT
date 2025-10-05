<?php
include "../conexion.php";
include "../authCookieSessionValidate.php";
if (!$isLoggedIn) {header("Location: ../index.php");}


$tabla = filter_input(INPUT_POST, 'tabla', FILTER_SANITIZE_SPECIAL_CHARS);
$columna = filter_input(INPUT_POST, 'columna', FILTER_SANITIZE_SPECIAL_CHARS);
$fila = filter_input(INPUT_POST, 'fila', FILTER_SANITIZE_SPECIAL_CHARS);
$valor = filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE $tabla set $columna='$valor' where id=$fila";
mysqli_query($mysqli, $sql);

