<?php
include "../../conexion.php";
header('Content-Type: application/json');

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID de asignación requerido']);
    exit;
}

$sql = "DELETE FROM obras_trabajadores_subpartidas WHERE id = '$id'";

if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Asignación eliminada correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $mysqli->error]);
}
?>
