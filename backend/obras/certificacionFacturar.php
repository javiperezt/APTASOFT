<?php
include "../../conexion.php";
include "../../ConsecutiveNumbers.php";

//genera numero automatico en presupuesto
$consecutiveNumber = new ConsecutiveNumbers();

$id_certificacion = filter_input(INPUT_POST, 'id_certificacion', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empresa = filter_input(INPUT_POST, 'id_empresa', FILTER_SANITIZE_SPECIAL_CHARS);
$id_contacto = filter_input(INPUT_POST, 'id_contacto', FILTER_SANITIZE_SPECIAL_CHARS);

include "../facturas/facturaGetConsecutiveNumber.php";

$sql = "INSERT INTO facturas (id_obra,id_empresa,pref_ref,pref_ref_year,ref,id_contacto) VALUES ('$id_obra','$id_empresa','$pref','$year','$documentNumber','$id_contacto')";
mysqli_query($mysqli, $sql);
$id_factura = mysqli_insert_id($mysqli);


$c1 = $mysqli->query("SELECT MIN(id), id_capitulo FROM certificaciones_partidas where id_certificacion='$id_certificacion' GROUP BY id_capitulo");
while ($row = $c1->fetch_assoc()) {
    $id_capitulo = $row['id_capitulo'];

    $sql1 = "INSERT INTO facturas_capitulos (id_factura, id_capitulo) VALUES ('$id_factura', '$id_capitulo')";
    mysqli_query($mysqli, $sql1);
}

$c0 = $mysqli->query("SELECT * FROM certificaciones_partidas where id_certificacion=$id_certificacion");
while ($row = $c0->fetch_assoc()) {
    $id_certificaciones_partidas = $row['id'];
    $id_certificacion = $row['id_certificacion'];
    $id_presupuesto = $row['id_presupuesto'];
    $id_capitulo = $row['id_capitulo'];
    $id_partida_presupuesto = $row['id_partida_presupuesto'];
    $partida = $row['partida'];
    $descripcion = $row['descripcion'];
    $id_unidad = $row['id_unidad'];
    $cantidad = $row['cantidad'];
    $precio = $row['precio'];
    $total = $row['total'];
    $cantidad_certificada = $row['cantidad_certificada'];
    $precio_certificado = $row['precio_certificado'];
    $total_cert = $cantidad_certificada * $precio_certificado;

    $q = mysqli_query($mysqli, "SELECT SUM(cantidad_certificada) AS x FROM certificaciones_partidas where  id_capitulo=$id_capitulo  and id_certificacion=$id_certificacion");
    $result = mysqli_fetch_assoc($q);
    $cantidadCertificadaCapitulo = round($result['x'] ?? 0, 2);

    if ($cantidadCertificadaCapitulo == 0) {
        $sql23 = "DELETE FROM facturas_capitulos where id_factura=$id_factura and id_capitulo=$id_capitulo";
        mysqli_query($mysqli, $sql23);
    }

    if ($cantidad_certificada != 0) {
        $sql2 = "INSERT INTO facturas_partidas (id_factura, id_capitulo, id_unidad, partida, descripcion, cantidad, precio, subtotal) 
                VALUES  ('$id_factura', '$id_capitulo', '$id_unidad', '$partida', '$descripcion', '$cantidad_certificada', '$precio_certificado', '$total_cert')";
        mysqli_query($mysqli, $sql2);
    }
}

$sql23 = "UPDATE certificaciones set id_estado=2 WHERE id=$id_certificacion";
mysqli_query($mysqli, $sql23);


$c011 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura='$id_factura'");
while ($row = $c011->fetch_assoc()) {
    $id_facturas_partidas = $row['id'];
    $id_partida = $row['id_partida'];
    $id_factura = $row['id_factura'];
    $id_capitulo = $row['id_capitulo'];
    $id_unidad = $row['id_unidad'];
    $partida = $row['partida'];
    $descripcion = $row['descripcion'];
    $cantidad = $row['cantidad'];
    $precio = $row['precio'];
    $descuento = $row['descuento'];
    $id_iva = $row['id_iva'];
    $subtotal = $row['subtotal'];
    $total = $row['total'];

    if ($id_iva) {
        $c1 = $mysqli->query("SELECT * FROM iva where id=$id_iva");
        while ($row = $c1->fetch_assoc()) {
            $iva = $row['iva'];
        }
    }

    // Calculo subtotal y total de la partida teniendo en cuenta el descuento
    $subtotalPartida = round($cantidad * $precio - ($cantidad * $precio * $descuento / 100), 2);
    $totalPartida = round($subtotalPartida + ($subtotalPartida * ($iva / 100)), 2);

    $sql = "UPDATE facturas_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_facturas_partidas";
    mysqli_query($mysqli, $sql);
}


echo $id_factura;

