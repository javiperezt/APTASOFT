<?php
include "../../conexion.php";
header('Content-Type: application/json');

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empleado_new = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_inicio_new = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_fin_new = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$id || !$id_empleado_new || !$fecha_inicio_new || !$fecha_fin_new) {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros requeridos']);
    exit;
}

// Obtener valores actuales
$currentQuery = "SELECT id_empleado, fecha_inicio, fecha_fin FROM obras_trabajadores_subpartidas WHERE id = '$id'";
$currentResult = $mysqli->query($currentQuery);

if ($currentResult->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Asignación no encontrada']);
    exit;
}

$current = $currentResult->fetch_assoc();
$id_empleado_old = $current['id_empleado'];
$fecha_inicio_old = $current['fecha_inicio'];
$fecha_fin_old = $current['fecha_fin'];

// Determinar qué cambió
$empleado_changed = ($id_empleado_new != $id_empleado_old);
$fecha_inicio_changed = ($fecha_inicio_new != $fecha_inicio_old);
$fecha_fin_changed = ($fecha_fin_new != $fecha_fin_old);

// Actualizar solo los campos que cambiaron
$updateFields = [];

if ($fecha_inicio_changed) {
    $updateFields[] = "fecha_inicio = '$fecha_inicio_new'";
}

if ($fecha_fin_changed) {
    $updateFields[] = "fecha_fin = '$fecha_fin_new'";
}

if ($empleado_changed) {
    $updateFields[] = "id_empleado = '$id_empleado_new'";
}

if (empty($updateFields)) {
    echo json_encode(['success' => true, 'message' => 'Sin cambios']);
    exit;
}

$sql = "UPDATE obras_trabajadores_subpartidas
        SET " . implode(', ', $updateFields) . "
        WHERE id = '$id'";

if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Asignación actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $mysqli->error]);
}
?>
