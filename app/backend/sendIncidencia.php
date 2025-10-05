<?php
include "../../conexion.php";

$incidencia = filter_input(INPUT_POST, 'incidencia', FILTER_SANITIZE_SPECIAL_CHARS);
$id_user = filter_input(INPUT_POST, 'id_user', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO incidencias (incidencia, id_empleado) VALUES ('$incidencia', '$id_user')";
mysqli_query($mysqli, $sql);
