<?php
include "../../conexion.php";
// Connect to the database
header('Content-Type: application/json');

$inicioSemana = $_GET['start'];
$finSemana = $_GET['end'];

$query = "
    SELECT 
        e.id as id_empleado, 
        o.id as id_obra,
        o.titulo as titulo_obra,
        e.nombre as empleado, 
        ps.concepto,
        ps.descripcion,
        ps.fecha_vencimiento,
        pp.partida
    FROM obras_trabajadores_subpartidas ots
    INNER JOIN empleados e ON ots.id_empleado = e.id
    INNER JOIN presupuestos_subpartidas ps ON ots.id_presupuestos_subpartidas = ps.id
    INNER JOIN obras o ON ots.id_obra = o.id
    INNER JOIN presupuestos_partidas pp ON ps.id_presupuesto_partidas = pp.id  -- Añadido para acceder al campo partida
    WHERE ps.id_categoria = 1 AND ps.fecha_vencimiento BETWEEN '$inicioSemana' AND '$finSemana'
";


$resultado = $mysqli->query($query);
$eventos = [];

$coloresEmpleado = [
    1 => '#FF5733',  // Rojo
    2 => '#33FF57',  // Verde
    3 => '#5733FF',  // Azul
    4 => '#FFC300',  // Amarillo
    5 => '#FF33D1',  // Rosa
    6 => '#33FFF5',  // Cyan
    7 => '#9140FF',  // Púrpura
    8 => '#FF8C33',  // Naranja
    9 => '#33FF90',  // Verde claro
    10 => '#4055FF', // Azul medio
    11 => '#D4FF33', // Amarillo verdoso
    12 => '#FF3360', // Rojo rosa
    13 => '#33B2FF', // Azul claro
    14 => '#A933FF', // Morado
    15 => '#FF6F33', // Naranja rojizo
    16 => '#5CFF33', // Verde lima
    17 => '#6E33FF'  // Azul violeta
];

while ($fila = $resultado->fetch_assoc()) {
    $colorEvento = isset($coloresEmpleado[$fila['id_empleado']]) ? $coloresEmpleado[$fila['id_empleado']] : '#000000';  // Negro por defecto si no encontramos el empleado

    $eventos[] = [
        'title' => strtoupper($fila['titulo_obra']) . "\n" .
            $fila['empleado'] . "\n\n" .
            $fila['partida'] . "\n" .
            $fila['concepto'],
        'start' => $fila['fecha_vencimiento'],
        'url' => "obraTareas.php?id_obra=" . $fila['id_obra'],
        'color' => $colorEvento  // Color del evento
    ];
}


echo json_encode($eventos);
?>
