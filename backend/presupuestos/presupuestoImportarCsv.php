<?php
include "../../conexion.php";

$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$file = $_FILES["file"]["tmp_name"];

$file_open = fopen($file, "r");
$file_content = file_get_contents($file);
$encoding = mb_detect_encoding($file_content, mb_list_encodings(), true);
$utf8_content = mb_convert_encoding($file_content, 'UTF-8', $encoding);
$temp = fopen("temp.csv", "w");
fwrite($temp, $utf8_content);
fclose($temp);
$file_open = fopen("temp.csv", "r");


while (($csv = fgetcsv($file_open, 10000000, ";")) !== false) {
    // do something with $csv
    $codigo = $csv[0];
    $tipo = $csv[1];
    $ud_medida = strtolower("$csv[2]");
    $concepto = $csv[3];
    $cantidad = $csv[10];
    $precio = $csv[11];
    $importe = $csv[12];

    $cantidad = str_replace('.', '', $cantidad);
    $cantidad = str_replace(',', '.', $cantidad);
    $cantidad = round(floatval($cantidad), 3);
    $precio = str_replace('.', '', $precio);
    $precio = str_replace(',', '.', $precio);
    $precio = round(floatval($precio), 3);
    $importe = str_replace('.', '', $importe);
    $importe = str_replace(',', '.', $importe);
    $importe = round(floatval($importe), 3);


    if ($tipo == "Capítulo" || substr("$tipo", 0, 3) == "Cap") {
        // registramos el capítulo
        $sql = "INSERT INTO capitulos (capitulo) VALUES ('$concepto')";
        mysqli_query($mysqli, $sql);
        $id_capitulo = mysqli_insert_id($mysqli);

        // asignamos el capitulo al presupuesto
        $sql1 = "INSERT INTO presupuestos_capitulos (id_presupuesto, id_capitulo) VALUES ('$id_presupuesto','$id_capitulo')";
        mysqli_query($mysqli, $sql1);
    }

    if ($tipo == "Partida") {
        $getUnidades = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
        while ($row = $getUnidades->fetch_assoc()) {
            $id_unidad = $row['id'];
        }
        if (!$id_unidad) {
            $id_unidad = 2;
        } //m3 no funciona bien entonces se fuerza

        $sql2 = "INSERT INTO presupuestos_partidas (id_presupuesto,id_capitulo,partida,id_unidad,cantidad) VALUES ('$id_presupuesto','$id_capitulo','$concepto','$id_unidad','$cantidad')";
        mysqli_query($mysqli, $sql2);
        $id_presupuestos_partidas = mysqli_insert_id($mysqli);

    }



    if ($codigo == "MO" || $codigo == "M0" || substr("$codigo", 0, 2) == "MO") {
        $getUnidades2 = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
        while ($row = $getUnidades2->fetch_assoc()) {
            $id_unidad = $row['id'];
        }
        if (!$id_unidad) {
            $id_unidad = 2;
        } //m3 no funciona bien entonces se fuerza

        $subtotal = round($cantidad * $precio, 2);
        $total = round($subtotal * 1.21, 2); // forzamos iva a 21%
        $sql3 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_categoria,concepto,id_unidad,cantidad,precio,subtotal,id_iva,total) VALUES ('$id_presupuestos_partidas',1,'$concepto','$id_unidad','$cantidad','$precio','$subtotal',1,'$total')";
        mysqli_query($mysqli, $sql3);
    }

    if (substr("$codigo", 0, 3) == "MAT") {
        $getUnidades3 = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
        while ($row = $getUnidades3->fetch_assoc()) {
            $id_unidad = $row['id'];
        }
        if (!$id_unidad) {
            $id_unidad = 2;
        } //m3 no funciona bien entonces se fuerza

        $subtotal = round($cantidad * $precio, 2);
        $total = round($subtotal * 1.21, 2); // forzamos iva a 21%
        $sql4 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_categoria,concepto,id_unidad,cantidad,precio,subtotal,id_iva,total) VALUES ('$id_presupuestos_partidas',2,'$concepto','$id_unidad','$cantidad','$precio','$subtotal',1,'$total')";
        mysqli_query($mysqli, $sql4);
    }

    if (substr("$codigo", 0, 3) == "EXT") {

        $getUnidades4 = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
        while ($row = $getUnidades4->fetch_assoc()) {
            $id_unidad = $row['id'];
        }
        if (!$id_unidad) {
            $id_unidad = 2;
        } //m3 no funciona bien entonces se fuerza

        $subtotal = round($cantidad * $precio, 2);
        $total = round($subtotal * 1.21, 2); // forzamos iva a 21%
        $sql5 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_categoria,concepto,id_unidad,cantidad,precio,subtotal,id_iva,total) VALUES ('$id_presupuestos_partidas',3,'$concepto','$id_unidad','$cantidad','$precio','$subtotal',1,'$total')";
        mysqli_query($mysqli, $sql5);
    }



    if ($id_presupuestos_partidas) {
        //Recalculamos totales y subtotales de la partida para asignarlo
        $getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas'");
        $result = mysqli_fetch_assoc($getSubtotalPartida);
        $subtotalPartida = $result['subtotalSubpartidas'];

        $getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas'");
        $result = mysqli_fetch_assoc($getTotalPartida);
        $totalPartida = $result['totalSubpartidas'];

        $sql12 = "UPDATE presupuestos_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_presupuestos_partidas";
        mysqli_query($mysqli, $sql12);
    }

    if (!$codigo && !empty($concepto)) {
        $sqlww3 = "UPDATE presupuestos_partidas set descripcion='$concepto' where id=$id_presupuestos_partidas";
        mysqli_query($mysqli, $sqlww3);
    }

    /*echo "<br>";
    echo "<br> codigo -> $codigo";
    echo "<br> tipo -> $tipo";
    echo "<br> ud_medida -> $ud_medida";
    echo "<br> concepto -> $concepto";
    echo "<br> cantidad -> $cantidad";
    echo "<br> precio -> $precio";
    echo "<br> importe -> $importe";
    echo "<br> id_presupuestos_partidas -> $id_presupuestos_partidas";
    echo "<br> $sqlww3";
    echo "<br>";*/

}

header("Location: ../../pages/presupuestoDetail.php?id_presupuesto=$id_presupuesto");
?>