<?php
include "../../conexion.php";


$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuestos_subpartidas = filter_input(INPUT_POST, 'id_presupuestos_subpartidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);
$horas = filter_input(INPUT_POST, 'horas', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO obras_registro_partes (id_obra, id_presupuesto, id_presupuestos_subpartidas, id_empleado, fecha, horas,id_presupuestos_partidas) VALUES ('$id_obra', '$id_presupuesto', '$id_presupuestos_subpartidas', '$id_empleado', '$fecha', '$horas','$id_presupuestos_partidas')";
mysqli_query($mysqli, $sql);

header("Location: ../../pages/obraPlanningDetailPartes.php?id_presupuestos_subpartidas=$id_presupuestos_subpartidas&id_obra=$id_obra&id_presupuesto=$id_presupuesto&id_presupuestos_partidas=$id_presupuestos_partidas");