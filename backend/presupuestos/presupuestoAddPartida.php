<?php
include "../../conexion.php";


$id_capitulo = filter_input(INPUT_POST, 'id_capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_partida = filter_input(INPUT_POST, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM gestor_partidas where id=$id_partida");
while ($row = $c0->fetch_assoc()) {
    $partida = $row['partida'];
    $codigo = $row['codigo'];
    $id_unidad = $row['id_unidad'];
    $descripcion = $row['descripcion'];
}

if ($id_unidad) {
    $c1 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
    while ($row = $c1->fetch_assoc()) {
        $simbolo = $row['simbolo'];
        $unidad = $row['unidad'];
    }
}


$sql = "INSERT INTO presupuestos_partidas (id_partida, id_presupuesto, id_capitulo, id_unidad, partida, descripcion) 
VALUES ('$id_partida','$id_presupuesto','$id_capitulo','$id_unidad','$partida','$descripcion')";
mysqli_query($mysqli, $sql);
$id_presupuesto_partidas = mysqli_insert_id($mysqli);


$id_subpartida = "";$id_categoria = "";$concepto = "";$descripcion_subpartida = "";$id_unidad_subpartida = "";$cantidad = "";$id_iva_subpartida = "";$precio = "";$subtotal = "";$total = "";
$c3 = $mysqli->query("SELECT * FROM gestor_subpartidas where id_partida=$id_partida");
while ($row = $c3->fetch_assoc()) {
    $id_subpartida = $row['id'];
    $id_categoria = $row['id_categoria'];
    $concepto = $row['concepto'];
    $descripcion_subpartida = $row['descripcion'];
    $id_unidad_subpartida = $row['id_unidad'];
    $cantidad = $row['cantidad'];
    $id_iva_subpartida = $row['id_iva'];
    $precio = $row['precio'];
    $subtotal = $row['subtotal'];
    $total = $row['total'];

    $sql1 = "INSERT INTO presupuestos_subpartidas 
    (id_presupuesto_partidas, id_partida, id_subpartida, id_categoria, concepto, descripcion, id_unidad, cantidad, precio, id_iva, subtotal, total) 
    VALUES ('$id_presupuesto_partidas','$id_partida','$id_subpartida','$id_categoria','$concepto','$descripcion_subpartida','$id_unidad_subpartida','$cantidad','$precio','$id_iva_subpartida','$subtotal','$total')";
    mysqli_query($mysqli, $sql1);
}

//Recalculamos totales y subtotales de la partida
$getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuesto_partidas'");
$result = mysqli_fetch_assoc($getSubtotalPartida);
$subtotalPartida = $result['subtotalSubpartidas'];

$getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuesto_partidas'");
$result = mysqli_fetch_assoc($getTotalPartida);
$totalPartida = $result['totalSubpartidas'];

$sql2 = "UPDATE presupuestos_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_presupuesto_partidas";
mysqli_query($mysqli, $sql2);

header("Location: ../../pages/presupuestoDetail.php?id_presupuesto=$id_presupuesto");

