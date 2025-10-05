<?php
include "../../conexion.php";


$id_incidencia = filter_input(INPUT_POST, 'id_incidencia', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM incidencias where id='$id_incidencia'";
mysqli_query($mysqli, $sql);
