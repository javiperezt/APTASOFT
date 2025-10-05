<?php
include "../../conexion.php";

$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_gasto_linea = filter_input(INPUT_POST, 'id_gasto_linea', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE gastos_lineas set id_presupuestos_partidas='$id_presupuestos_partidas' WHERE id='$id_gasto_linea'";
mysqli_query($mysqli, $sql);

if(!$id_presupuestos_partidas){
    $sql = "UPDATE gastos_lineas set id_presupuestos_partidas=NULL WHERE id='$id_gasto_linea'";
    mysqli_query($mysqli, $sql);
}