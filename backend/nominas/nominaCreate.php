<?php
include "../../conexion.php";

$sql = "INSERT INTO nominas () VALUES ()";
mysqli_query($mysqli, $sql);
$id_nomina = mysqli_insert_id($mysqli);

echo $id_nomina;