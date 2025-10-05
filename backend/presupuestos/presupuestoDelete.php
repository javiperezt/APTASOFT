<?php
include "../../conexion.php";


$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos where id=$id_presupuesto");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_cuenta = $row['id_cuenta'];
    $id_obra = $row['id_obra'];
    $id_estado = $row['id_estado'];
    $id_empresa = $row['id_empresa'];
    $pref_ref = $row['pref_ref'];
    $pref_ref_year = $row['pref_ref_year'];
    $ref = $row['ref'];
    $ref = $pref_ref . $pref_ref_year . $ref;
    $asunto = $row['asunto'];
    $fecha_inicio = $row['fecha_inicio'];
    $fecha_vencimiento = $row['fecha_vencimiento'];
}

$c1 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
while ($row = $c1->fetch_assoc()) {
    $id_presupuestos_partidas = $row['id'];

    $sql3 = "DELETE from presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas";
    mysqli_query($mysqli, $sql3);
}

$sql4 = "DELETE from presupuestos_capitulos where id_presupuesto=$id_presupuesto";
mysqli_query($mysqli, $sql4);

$sql2 = "DELETE from presupuestos_partidas where id_presupuesto=$id_presupuesto";
mysqli_query($mysqli, $sql2);

$sql11 = "DELETE from presupuestos where id=$id_presupuesto";
mysqli_query($mysqli, $sql11);

$sql5 = "INSERT INTO eventos (id_empleado, descripcion) VALUES ('$ID_USER','Presupuesto: $id_presupuesto eliminado. Cliente: $id_contacto. Obra: $id_obra')";
mysqli_query($mysqli, $sql5);

