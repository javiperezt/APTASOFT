<?php
include "../../conexion.php";

$id_gasto_linea = filter_input(INPUT_POST, 'id_gasto_linea', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM gastos_lineas WHERE id=$id_gasto_linea";
mysqli_query($mysqli, $sql);

