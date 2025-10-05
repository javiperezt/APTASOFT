<?php
include "../../conexion.php";
include "../../DateClass.php";
$dateClass = new DateClass();

$fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
$hora_inicio = filter_input(INPUT_POST, 'hora_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$hora_fin = filter_input(INPUT_POST, 'hora_fin', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);

//diferencia en segundos
$secondsinit = $dateClass->getSecondsFromFormatedHour("$hora_inicio");
$secondsfin = $dateClass->getSecondsFromFormatedHour("$hora_fin");
$secondsdiff = $secondsfin - $secondsinit;

$total = gmdate("H:i", "$secondsdiff");

$sql = "INSERT INTO jornadas (id_empleado, fecha, hora_inicio, hora_fin, total,is_active) VALUES ('$id_empleado', '$fecha', '$hora_inicio', '$hora_fin', '$secondsdiff',0)";
mysqli_query($mysqli, $sql);

header("Location: ../../pages/empleadoJornadas.php?id_empleado=$id_empleado");

