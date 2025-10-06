<?php
include "../../conexion.php";

$id_capitulo = filter_input(INPUT_POST, 'id_capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

// Crear partida en blanco con valores predeterminados
$partida = "Nueva partida";
$descripcion = "";
$id_unidad = 1; // Unidad por defecto (asumiendo que existe)
$cantidad = 1;
$subtotal = 0;
$total = 0;

$sql = "INSERT INTO presupuestos_partidas (id_presupuesto, id_capitulo, id_unidad, partida, descripcion, cantidad, subtotal, total)
VALUES ('$id_presupuesto','$id_capitulo','$id_unidad','$partida','$descripcion','$cantidad','$subtotal','$total')";

if (mysqli_query($mysqli, $sql)) {
    echo json_encode(['success' => true, 'id_partida' => mysqli_insert_id($mysqli)]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($mysqli)]);
}
