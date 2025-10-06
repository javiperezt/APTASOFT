<?php
include "../../conexion.php";
header('Content-Type: application/json');

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$info = filter_input(INPUT_POST, 'info', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID requerido']);
    exit;
}

$info_value = $info ? "'$info'" : "NULL";

$sql = "UPDATE obras_trabajadores_subpartidas
        SET info = $info_value
        WHERE id = '$id'";

if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Notas guardadas correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $mysqli->error]);
}
?>
