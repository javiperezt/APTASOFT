<?php
include "../../conexion.php";


$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas";
mysqli_query($mysqli, $sql);

$sql1 = "DELETE FROM presupuestos_partidas where id=$id_presupuestos_partidas";
mysqli_query($mysqli, $sql1);

