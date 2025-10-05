<?php
include "../../conexion.php";


$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO gestor_subpartidas (id_partida) VALUE ('$id_partida')";
mysqli_query($mysqli, $sql);

