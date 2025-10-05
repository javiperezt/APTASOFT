<?php
include "../../conexion.php";


$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
$correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO empleados (nombre, correo) VALUE ('$nombre','$correo')";
mysqli_query($mysqli, $sql);
$id_empleado = mysqli_insert_id($mysqli);

header("Location: ../../pages/empleadoDetail.php?id_empleado=$id_empleado");

