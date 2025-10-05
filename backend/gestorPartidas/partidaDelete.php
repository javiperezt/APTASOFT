<?php
include "../../conexion.php";


$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE gestor_partidas set is_active=0 where id=$id_partida";
mysqli_query($mysqli, $sql);
