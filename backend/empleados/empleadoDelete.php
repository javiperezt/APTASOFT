<?php
include "../../conexion.php";


$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE empleados set is_active=0 where id=$id_empleado";
mysqli_query($mysqli, $sql);

