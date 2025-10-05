<?php
include "../../conexion.php";


$id_capitulo = filter_input(INPUT_POST, 'id_capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto and id_capitulo=$id_capitulo");
while ($row = $c0->fetch_assoc()) {
    $id_presupuestos_partidas = $row['id'];

    $sql = "DELETE FROM presupuestos_partidas where id=$id_presupuestos_partidas";
    mysqli_query($mysqli, $sql);

    $c1 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas");
    while ($row = $c1->fetch_assoc()) {
        $id_presupuestos_subpartidas = $row['id'];

        $sql1 = "DELETE FROM presupuestos_subpartidas where id=$id_presupuestos_subpartidas";
        mysqli_query($mysqli, $sql1);
    }
}

$sql2 = "DELETE FROM presupuestos_capitulos where id_capitulo=$id_capitulo and id_presupuesto=$id_presupuesto";
mysqli_query($mysqli, $sql2);