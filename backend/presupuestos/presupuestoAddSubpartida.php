<?php
include "../../conexion.php";


$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);

if($id_partida) {
    $sql = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_partida) VALUES ('$id_presupuestos_partidas','$id_partida')";
    mysqli_query($mysqli, $sql);
}else{
    $sql1 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas) VALUE ('$id_presupuestos_partidas')";
    mysqli_query($mysqli, $sql1);
}

