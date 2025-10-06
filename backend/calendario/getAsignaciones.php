<?php
include "../../conexion.php";
header('Content-Type: application/json');

$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_inicio = filter_input(INPUT_GET, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_fin = filter_input(INPUT_GET, 'fecha_fin', FILTER_SANITIZE_SPECIAL_CHARS);

// Permitir filtrar por obra específica o todas
$whereClause = "WHERE 1=1";
if ($id_obra && $id_obra !== 'all') {
    $whereClause .= " AND ots.id_obra = '$id_obra'";
} else {
    $whereClause .= " AND o.is_active = 1";
}

if ($fecha_inicio && $fecha_fin) {
    $whereClause .= " AND ((ots.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin')
                          OR (ots.fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin')
                          OR (ots.fecha_inicio <= '$fecha_inicio' AND ots.fecha_fin >= '$fecha_fin'))";
}

// Verificar si existe la columna info
$checkColumn = $mysqli->query("SHOW COLUMNS FROM obras_trabajadores_subpartidas LIKE 'info'");
$hasInfoColumn = $checkColumn->num_rows > 0;

$infoField = $hasInfoColumn ? "ots.info," : "NULL as info,";

$query = "
    SELECT
        ots.id,
        ots.id_obra,
        ots.id_presupuesto,
        ots.id_presupuestos_subpartidas,
        ots.id_empleado,
        ots.fecha_inicio,
        ots.fecha_fin,
        $infoField
        ps.concepto,
        ps.descripcion,
        pp.partida,
        e.nombre as nombre_empleado,
        o.titulo as titulo_obra,
        o.color as color_obra
    FROM obras_trabajadores_subpartidas ots
    INNER JOIN presupuestos_subpartidas ps ON ps.id = ots.id_presupuestos_subpartidas
    INNER JOIN presupuestos_partidas pp ON pp.id = ps.id_presupuesto_partidas
    INNER JOIN empleados e ON e.id = ots.id_empleado
    INNER JOIN obras o ON o.id = ots.id_obra
    $whereClause
    ORDER BY ots.fecha_inicio, e.nombre
";

$resultado = $mysqli->query($query);
$asignaciones = [];

while ($fila = $resultado->fetch_assoc()) {
    $asignaciones[] = [
        'id' => $fila['id'],
        'id_obra' => $fila['id_obra'],
        'id_presupuesto' => $fila['id_presupuesto'],
        'id_presupuestos_subpartidas' => $fila['id_presupuestos_subpartidas'],
        'id_empleado' => $fila['id_empleado'],
        'fecha_inicio' => $fila['fecha_inicio'],
        'fecha_fin' => $fila['fecha_fin'],
        'info' => $fila['info'],
        'partida' => $fila['partida'],
        'concepto' => $fila['concepto'],
        'descripcion' => $fila['descripcion'],
        'nombre_empleado' => $fila['nombre_empleado'],
        'titulo_obra' => $fila['titulo_obra'],
        'color_obra' => $fila['color_obra'] ?? '#0d6efd'
    ];
}

echo json_encode($asignaciones);
?>
