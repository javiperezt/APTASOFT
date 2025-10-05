<?php
include "../../conexion.php";


$titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
$comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO obras_notas (id_empleado, id_obra, titulo, comentario) VALUES ('$id_empleado', '$id_obra', '$titulo', '$comentario')";
mysqli_query($mysqli, $sql);

header("Location: ../../pages/obraNotas.php?id_obra=$id_obra");