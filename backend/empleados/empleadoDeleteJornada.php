<?php
include "../../conexion.php";


$id_jornada = filter_input(INPUT_POST, 'id_jornada', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM jornadas WHERE id='$id_jornada'";
mysqli_query($mysqli, $sql);

