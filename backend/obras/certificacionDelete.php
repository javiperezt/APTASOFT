<?php
include "../../conexion.php";

$id_certificacion = filter_input(INPUT_POST, 'id_certificacion', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM certificaciones_partidas where id_certificacion='$id_certificacion'";
mysqli_query($mysqli, $sql);

$sql2 = "DELETE FROM certificaciones where id='$id_certificacion'";
mysqli_query($mysqli, $sql2);
