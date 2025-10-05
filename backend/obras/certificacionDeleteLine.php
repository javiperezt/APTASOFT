<?php
include "../../conexion.php";

$id_certificaciones_partidas = filter_input(INPUT_POST, 'id_certificaciones_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM certificaciones_partidas where id='$id_certificaciones_partidas'";
mysqli_query($mysqli, $sql);
