<?php
include "../../conexion.php";

$beneficio = filter_input(INPUT_POST, 'beneficio', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$beneficio = str_replace(',', '.', $beneficio);

$c0 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
while ($row = $c0->fetch_assoc()) {
    $id_presupuesto_partida = $row['id'];

    $sql = "UPDATE presupuestos_subpartidas set descuento='-$beneficio' where id_presupuesto_partidas=$id_presupuesto_partida";
    mysqli_query($mysqli, $sql);

}

$c0ww = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
while ($row = $c0ww->fetch_assoc()) {
    $id_presupuesto_partida = $row['id'];

    $c22 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuesto_partida");
    while ($row = $c22->fetch_assoc()) {
        $id_presupuestos_subpartidas = $row['id'];
        $id_presupuesto_partidas = $row['id_presupuesto_partidas'];
        $id_categoria = $row['id_categoria'];
        $concepto = $row['concepto'];
        $descripcion_subpartida = $row['descripcion'];
        $id_unidad_subpartida = $row['id_unidad'];
        $cantidad = $row['cantidad'];
        $id_iva_subpartida = $row['id_iva'];
        $precio = $row['precio'];
        $subtotal = $row['subtotal'];
        $descuento = $row['descuento'];
        $total = $row['total'];


        $c1 = $mysqli->query("SELECT * FROM iva where id=$id_iva_subpartida");
        while ($row = $c1->fetch_assoc()) {
            $iva = $row['iva'];
        }

// Calculo subtotal y total de la subpartida teniendo en cuenta el descuento
        $subtotalSubpartida = round($cantidad * $precio - ($cantidad * $precio * $descuento / 100), 2);
        $totalSubpartida = round($subtotalSubpartida + ($subtotalSubpartida * ($iva / 100)), 2);

        $sql = "UPDATE presupuestos_subpartidas set subtotal=$subtotalSubpartida,total=$totalSubpartida where id=$id_presupuestos_subpartidas";
        mysqli_query($mysqli, $sql);

//Recalculamos totales y subtotales de la partida
        $getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuesto_partidas'");
        $result = mysqli_fetch_assoc($getSubtotalPartida);
        $subtotalPartida = $result['subtotalSubpartidas'];

        $getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuesto_partidas'");
        $result = mysqli_fetch_assoc($getTotalPartida);
        $totalPartida = $result['totalSubpartidas'];

        $sql1 = "UPDATE presupuestos_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_presupuesto_partidas";
        mysqli_query($mysqli, $sql1);
    }
}


