<?php
include "../../conexion.php";
include "../../authCookieSessionValidate.php";
require_once "../../DateClass.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}
$dateClass = new DateClass();

$id_user = filter_input(INPUT_POST, 'id_user', FILTER_SANITIZE_SPECIAL_CHARS);

$fecha = date("Y-m-d");
$hora = date("H:i:s");

$c1 = $mysqli->query("SELECT * FROM jornadas where id_empleado='$id_user' and is_active=1");

// si jornada = 0  es que no hay jornada activa, si jornada da 1 es que hay una jornada activa
$jornada = $c1->num_rows;

// Si no hay jornadas activas se crea una
if ($jornada == 0) {
    $sql = "INSERT INTO jornadas (id_empleado, fecha, hora_inicio,is_active) VALUES ('$id_user', '$fecha', '$hora',1)";
    mysqli_query($mysqli, $sql);
}

// SI ya hay una activa se graba la hora de finalizacion y se cierra
if ($jornada == 1) {
    while ($row = $c1->fetch_assoc()) {
        $id_jornada = $row['id'];
        $hora_inicio = $row['hora_inicio'];
        $is_active = $row['is_active'];
    }

    $secondsinit = $dateClass->getSecondsFromFormatedHour("$hora_inicio");
    $secondsfin = $dateClass->getSecondsFromFormatedHour("$hora");
    $secondsdiff = $secondsfin - $secondsinit;

    $sql1 = "UPDATE jornadas SET hora_fin='$hora', is_active=0, total='$secondsdiff' WHERE id=$id_jornada";
    mysqli_query($mysqli, $sql1);
}
