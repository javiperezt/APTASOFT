<?php
include "../../conexion.php";
header('Content-Type: application/json');

$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

// Si no hay id_obra, traer todas las obras activas
$whereClause = "ps.id_categoria = 1";
if ($id_obra && $id_obra !== 'all') {
    $whereClause .= " AND p.id_obra = '$id_obra'";
} else {
    $whereClause .= " AND o.is_active = 1";
}

$query = "
    SELECT
        ps.id as id_presupuestos_subpartidas,
        ps.concepto,
        ps.descripcion,
        ps.cantidad,
        ps.id_unidad,
        ps.precio,
        ps.subtotal,
        pp.partida,
        pp.cantidad as cantidad_partida,
        pp.id as id_presupuestos_partidas,
        p.id as id_presupuesto,
        p.id_obra,
        o.titulo as titulo_obra,
        o.color as color_obra,
        u.simbolo as simbolo_unidad
    FROM presupuestos_subpartidas ps
    INNER JOIN presupuestos_partidas pp ON pp.id = ps.id_presupuesto_partidas
    INNER JOIN presupuestos p ON p.id = pp.id_presupuesto
    INNER JOIN obras o ON o.id = p.id_obra
    LEFT JOIN unidades u ON u.id = ps.id_unidad
    WHERE $whereClause
    ORDER BY o.titulo, pp.partida, ps.concepto
";

$resultado = $mysqli->query($query);
$tareas = [];

while ($fila = $resultado->fetch_assoc()) {
    $cantidad_total = round($fila['cantidad_partida'] * $fila['cantidad'], 2);

    $tareas[] = [
        'id' => $fila['id_presupuestos_subpartidas'],
        'id_presupuesto' => $fila['id_presupuesto'],
        'id_presupuestos_partidas' => $fila['id_presupuestos_partidas'],
        'id_obra' => $fila['id_obra'],
        'titulo_obra' => $fila['titulo_obra'],
        'color_obra' => $fila['color_obra'] ?? '#0d6efd',
        'partida' => $fila['partida'],
        'concepto' => $fila['concepto'],
        'descripcion' => $fila['descripcion'],
        'cantidad' => $cantidad_total,
        'simbolo_unidad' => $fila['simbolo_unidad'] ?? '',
        'precio' => $fila['precio'],
        'subtotal' => $fila['subtotal']
    ];
}

echo json_encode($tareas);
?>
