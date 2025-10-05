<?php
include "../../conexion.php";


$id_nomina = filter_input(INPUT_POST, 'id_nomina', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM nominas where id=$id_nomina";
mysqli_query($mysqli, $sql);

