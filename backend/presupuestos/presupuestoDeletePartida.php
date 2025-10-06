<?php
include "../../conexion.php";

$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

// Eliminar subpartidas primero
$sql = "DELETE FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas";
$result1 = mysqli_query($mysqli, $sql);

// Luego eliminar la partida
$sql1 = "DELETE FROM presupuestos_partidas where id=$id_presupuestos_partidas";
$result2 = mysqli_query($mysqli, $sql1);

if ($result1 && $result2) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($mysqli)]);
}

