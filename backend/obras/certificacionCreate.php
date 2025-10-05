<?php
include "../../conexion.php";

$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$concepto = filter_input(INPUT_POST, 'concepto', FILTER_SANITIZE_SPECIAL_CHARS);
$codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO certificaciones (id_obra, concepto, codigo, fecha,id_estado) VALUES ('$id_obra', '$concepto', '$codigo', '$fecha',1)";
mysqli_query($mysqli, $sql);
$id_certificacion = mysqli_insert_id($mysqli);

$c0 = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra and is_active=1");
while ($row = $c0->fetch_assoc()) {
    $id_presupuesto = $row['id'];

    $c1 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
    while ($row = $c1->fetch_assoc()) {
        $id_presupuestos_partidas = $row['id'];
        $id_capitulo = $row['id_capitulo'];
        $id_unidad = $row['id_unidad'];
        $partida = $row['partida'];
        $descripcion = $row['descripcion'];
        $cantidad = $row['cantidad'];
        $subtotal = $row['subtotal'];
        $total = $row['total'];
        $id_estado = $row['id_estado'];
        $fecha_inicio = $row['fecha_inicio'];
        $fecha_vencimiento = $row['fecha_vencimiento'];

        $sql1 = "INSERT INTO certificaciones_partidas 
    (id_certificacion, id_capitulo, id_partida_presupuesto, partida, descripcion, cantidad, precio, total,id_presupuesto,id_unidad,precio_certificado) VALUES 
    ('$id_certificacion', '$id_capitulo', '$id_presupuestos_partidas', '$partida', '$descripcion', '$cantidad', '$subtotal', '$total','$id_presupuesto','$id_unidad','$subtotal')";
        mysqli_query($mysqli, $sql1);
    }
}

header("Location: ../../pages/certificacion.php?id_certificacion=$id_certificacion");

