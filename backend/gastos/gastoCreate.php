<?php
include "../../conexion.php";

$codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empresa = filter_input(INPUT_POST, 'id_empresa', FILTER_SANITIZE_SPECIAL_CHARS);
$id_contacto = filter_input(INPUT_POST, 'id_contacto', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO gastos (codigo,fecha_inicio,id_obra,id_empresa,id_contacto,id_estado) VALUES ('$codigo','$fecha_inicio','$id_obra','$id_empresa','$id_contacto',1)";
mysqli_query($mysqli, $sql);
$id_gasto = mysqli_insert_id($mysqli);

header("Location: ../../pages/gastoDetail.php?id_gasto=$id_gasto");
