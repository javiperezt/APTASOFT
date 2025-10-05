<?php
include "../../conexion.php";


$capitulo = filter_input(INPUT_POST, 'capitulo', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO capitulos (capitulo) VALUE ('$capitulo')";
mysqli_query($mysqli, $sql);
$id_capitulo = mysqli_insert_id($mysqli);

$sql1 = "INSERT INTO presupuestos_capitulos (id_presupuesto, id_capitulo) VALUE ('$id_presupuesto','$id_capitulo')";
mysqli_query($mysqli, $sql1);

header("Location: ../../pages/presupuestoDetail.php?id_presupuesto=$id_presupuesto");

