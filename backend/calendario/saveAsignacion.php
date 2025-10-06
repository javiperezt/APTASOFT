<?php
include "../../conexion.php";
header('Content-Type: application/json');

$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuestos_subpartidas = filter_input(INPUT_POST, 'id_presupuestos_subpartidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$info = filter_input(INPUT_POST, 'info', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$id_obra || !$id_presupuesto || !$id_presupuestos_subpartidas || !$id_empleado || !$fecha_inicio) {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros requeridos']);
    exit;
}

// Por defecto, fecha_fin = fecha_inicio (1 día)
$fecha_fin = $fecha_inicio;

// Verificar si ya existe la misma asignación
$checkQuery = "SELECT id FROM obras_trabajadores_subpartidas
               WHERE id_obra = '$id_obra'
               AND id_presupuestos_subpartidas = '$id_presupuestos_subpartidas'
               AND id_empleado = '$id_empleado'
               AND fecha_inicio = '$fecha_inicio'";

$checkResult = $mysqli->query($checkQuery);

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Esta asignación ya existe']);
    exit;
}

// Verificar si existe la columna info
$checkColumn = $mysqli->query("SHOW COLUMNS FROM obras_trabajadores_subpartidas LIKE 'info'");
$hasInfoColumn = $checkColumn->num_rows > 0;

// Insertar nueva asignación
if ($hasInfoColumn) {
    $info_value = $info ? "'$info'" : "NULL";
    $sql = "INSERT INTO obras_trabajadores_subpartidas
            (id_obra, id_presupuesto, id_presupuestos_subpartidas, id_empleado, fecha_inicio, fecha_fin, info)
            VALUES ('$id_obra', '$id_presupuesto', '$id_presupuestos_subpartidas', '$id_empleado', '$fecha_inicio', '$fecha_fin', $info_value)";
} else {
    $sql = "INSERT INTO obras_trabajadores_subpartidas
            (id_obra, id_presupuesto, id_presupuestos_subpartidas, id_empleado, fecha_inicio, fecha_fin)
            VALUES ('$id_obra', '$id_presupuesto', '$id_presupuestos_subpartidas', '$id_empleado', '$fecha_inicio', '$fecha_fin')";
}

if ($mysqli->query($sql)) {
    $inserted_id = $mysqli->insert_id;
    echo json_encode([
        'success' => true,
        'id' => $inserted_id,
        'message' => 'Asignación guardada correctamente'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $mysqli->error]);
}
?>
