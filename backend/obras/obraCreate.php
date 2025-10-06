<?php
include "../../conexion.php";


$titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_contacto = filter_input(INPUT_POST, 'id_contacto', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empresa = filter_input(INPUT_POST, 'id_empresa', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO obras (id_contacto, id_empresa, titulo, id_estado, is_active) VALUES ('$id_contacto', '$id_empresa', '$titulo', 1, 1)";
mysqli_query($mysqli, $sql);
$id_obra = mysqli_insert_id($mysqli);

header("Location: ../../pages/obraDetail.php?id_obra=$id_obra");
