<?php
include "../../conexion.php";


$partida = filter_input(INPUT_POST, 'partida', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO gestor_partidas (partida) VALUE ('$partida')";
mysqli_query($mysqli, $sql);
$id_partida = mysqli_insert_id($mysqli);

header("Location: ../../pages/partidaDetail.php?id_partida=$id_partida");

