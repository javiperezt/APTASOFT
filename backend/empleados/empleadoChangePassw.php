<?php
include "../../conexion.php";


$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
$password = password_hash($password, PASSWORD_BCRYPT);
$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE empleados set password='$password' where id=$id_empleado";
mysqli_query($mysqli, $sql);

header( "Location:../../pages/empleadoDetail.php?id_empleado=$id_empleado");