<?php
include "../../conexion.php";

$id_gasto = filter_input(INPUT_POST, 'id_gasto', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO gastos_lineas (id_gasto) VALUE ('$id_gasto')";
mysqli_query($mysqli, $sql);
