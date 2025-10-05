<?php
include "../../conexion.php";


$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

$cFacturas = $mysqli->query("SELECT * FROM facturas where id_obra=$id_obra");
while ($row = $cFacturas->fetch_assoc()) {
    $id_factura = $row['id'];

    if ($id_factura) {
        $sql = "UPDATE facturas set is_active=0 where id='$id_factura'";
        mysqli_query($mysqli, $sql);
    }
}

$cPresupuestos = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
while ($row = $cPresupuestos->fetch_assoc()) {
    $id_presupuesto = $row['id'];

    if ($id_presupuesto) {
        $sql1 = "UPDATE presupuestos set is_active=0 where id='$id_presupuesto'";
        mysqli_query($mysqli, $sql1);
    }
}

$cGastos = $mysqli->query("SELECT * FROM gastos where id_obra=$id_obra");
while ($row = $cGastos->fetch_assoc()) {
    $id_gasto = $row['id'];

    if ($id_gasto) {
        $sql2 = "UPDATE gastos set is_active=0 where id='$id_gasto'";
        mysqli_query($mysqli, $sql2);
    }
}

$cCertif = $mysqli->query("SELECT * FROM certificaciones where id_obra=$id_obra");
while ($row = $cCertif->fetch_assoc()) {
    $id_certificacion = $row['id'];

    if ($id_certificacion) {
        $sql3 = "UPDATE certificaciones set is_active=0 where id='$id_certificacion'";
        mysqli_query($mysqli, $sql3);
    }
}

$sql = "UPDATE obras set is_active=0 where id='$id_obra'";
mysqli_query($mysqli, $sql);
