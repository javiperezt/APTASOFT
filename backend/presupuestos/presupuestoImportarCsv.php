<?php
include "../../conexion.php";

// Inicializar archivo de log
$logFile = "debug_import_" . date('Y-m-d_H-i-s') . ".txt";
$logPath = __DIR__ . "/" . $logFile;

function writeLog($message) {
    global $logPath;
    file_put_contents($logPath, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}

writeLog("=== INICIO DE IMPORTACIÓN CSV ===");

$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);
writeLog("ID Presupuesto: $id_presupuesto");

$file = $_FILES["file"]["tmp_name"];
writeLog("Archivo temporal: $file");

$file_open = fopen($file, "r");
$file_content = file_get_contents($file);
$encoding = mb_detect_encoding($file_content, mb_list_encodings(), true);
writeLog("Encoding detectado: $encoding");

$utf8_content = mb_convert_encoding($file_content, 'UTF-8', $encoding);
$temp = fopen("temp.csv", "w");
fwrite($temp, $utf8_content);
fclose($temp);
$file_open = fopen("temp.csv", "r");

$id_presupuestos_partidas = null;
$lineNumber = 0;

while (($csv = fgetcsv($file_open, 10000000, ";")) !== false) {
    $lineNumber++;
    writeLog("\n--- LÍNEA $lineNumber ---");
    writeLog("Datos CSV completos: " . json_encode($csv));
    // do something with $csv
    $codigo = $csv[0];
    $tipo = $csv[1];
    $ud_medida = strtolower("$csv[2]");
    $concepto = $csv[3];
    $cantidad = $csv[10];
    $precio = $csv[11];
    $importe = $csv[12];

    writeLog("Código: '$codigo' | Tipo: '$tipo' | Unidad: '$ud_medida' | Concepto: '$concepto'");
    writeLog("Cantidad (original): '$cantidad' | Precio (original): '$precio' | Importe (original): '$importe'");

    $cantidad = str_replace('.', '', $cantidad);
    $cantidad = str_replace(',', '.', $cantidad);
    $cantidad = round(floatval($cantidad), 3);
    $precio = str_replace('.', '', $precio);
    $precio = str_replace(',', '.', $precio);
    $precio = round(floatval($precio), 3);
    $importe = str_replace('.', '', $importe);
    $importe = str_replace(',', '.', $importe);
    $importe = round(floatval($importe), 3);

    writeLog("Cantidad (convertida): $cantidad | Precio (convertido): $precio | Importe (convertido): $importe");


    if ($tipo == "Capítulo" || substr("$tipo", 0, 3) == "Cap") {
        writeLog(">>> TIPO: CAPÍTULO");
        // registramos el capítulo
        $sql = "INSERT INTO capitulos (capitulo) VALUES ('$concepto')";
        writeLog("SQL Capítulo: $sql");
        $result = mysqli_query($mysqli, $sql);
        if ($result) {
            $id_capitulo = mysqli_insert_id($mysqli);
            writeLog("✓ Capítulo insertado con ID: $id_capitulo");
        } else {
            writeLog("✗ ERROR insertando capítulo: " . mysqli_error($mysqli));
        }

        // asignamos el capitulo al presupuesto
        $sql1 = "INSERT INTO presupuestos_capitulos (id_presupuesto, id_capitulo) VALUES ('$id_presupuesto','$id_capitulo')";
        writeLog("SQL Presupuesto-Capítulo: $sql1");
        $result1 = mysqli_query($mysqli, $sql1);
        if ($result1) {
            writeLog("✓ Capítulo asignado al presupuesto");
        } else {
            writeLog("✗ ERROR asignando capítulo al presupuesto: " . mysqli_error($mysqli));
        }
    }

    if ($tipo == "Partida") {
        writeLog(">>> TIPO: PARTIDA");
        writeLog("Buscando unidad con símbolo: '$ud_medida'");

        $getUnidades = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
        $id_unidad = null;
        while ($row = $getUnidades->fetch_assoc()) {
            $id_unidad = $row['id'];
        }

        if (!$id_unidad) {
            $id_unidad = 2;
            writeLog("⚠ Unidad no encontrada, usando ID por defecto: 2");
        } else {
            writeLog("✓ Unidad encontrada con ID: $id_unidad");
        }

        $sql2 = "INSERT INTO presupuestos_partidas (id_presupuesto,id_capitulo,partida,id_unidad,cantidad) VALUES ('$id_presupuesto','$id_capitulo','$concepto','$id_unidad','$cantidad')";
        writeLog("SQL Partida: $sql2");
        $result2 = mysqli_query($mysqli, $sql2);

        if ($result2) {
            $id_presupuestos_partidas = mysqli_insert_id($mysqli);
            writeLog("✓ Partida insertada con ID: $id_presupuestos_partidas");
        } else {
            writeLog("✗ ERROR insertando partida: " . mysqli_error($mysqli));
            $id_presupuestos_partidas = null;
        }

    }



    if ($codigo == "MO" || $codigo == "M0" || substr("$codigo", 0, 2) == "MO") {
        writeLog(">>> TIPO: MANO DE OBRA (MO)");
        writeLog("ID Partida asociada: " . ($id_presupuestos_partidas ?? 'NULL'));

        if (!$id_presupuestos_partidas) {
            writeLog("✗ ERROR: No hay partida activa para asociar la mano de obra");
        } else {
            $getUnidades2 = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
            $id_unidad = null;
            while ($row = $getUnidades2->fetch_assoc()) {
                $id_unidad = $row['id'];
            }
            if (!$id_unidad) {
                $id_unidad = 2;
                writeLog("⚠ Unidad no encontrada, usando ID por defecto: 2");
            }

            $subtotal = round($cantidad * $precio, 2);
            $total = round($subtotal * 1.21, 2); // forzamos iva a 21%
            writeLog("Cálculos: Cantidad=$cantidad * Precio=$precio = Subtotal=$subtotal | Total (con IVA)=$total");

            $sql3 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_categoria,concepto,id_unidad,cantidad,precio,subtotal,id_iva,total) VALUES ('$id_presupuestos_partidas',1,'$concepto','$id_unidad','$cantidad','$precio','$subtotal',1,'$total')";
            writeLog("SQL Subpartida MO: $sql3");
            $result3 = mysqli_query($mysqli, $sql3);

            if ($result3) {
                writeLog("✓ Subpartida MO insertada con ID: " . mysqli_insert_id($mysqli));
            } else {
                writeLog("✗ ERROR insertando subpartida MO: " . mysqli_error($mysqli));
            }
        }
    }

    if (substr("$codigo", 0, 3) == "MAT") {
        writeLog(">>> TIPO: MATERIAL (MAT)");
        writeLog("ID Partida asociada: " . ($id_presupuestos_partidas ?? 'NULL'));

        if (!$id_presupuestos_partidas) {
            writeLog("✗ ERROR: No hay partida activa para asociar el material");
        } else {
            $getUnidades3 = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
            $id_unidad = null;
            while ($row = $getUnidades3->fetch_assoc()) {
                $id_unidad = $row['id'];
            }
            if (!$id_unidad) {
                $id_unidad = 2;
                writeLog("⚠ Unidad no encontrada, usando ID por defecto: 2");
            }

            $subtotal = round($cantidad * $precio, 2);
            $total = round($subtotal * 1.21, 2); // forzamos iva a 21%
            writeLog("Cálculos: Cantidad=$cantidad * Precio=$precio = Subtotal=$subtotal | Total (con IVA)=$total");

            $sql4 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_categoria,concepto,id_unidad,cantidad,precio,subtotal,id_iva,total) VALUES ('$id_presupuestos_partidas',2,'$concepto','$id_unidad','$cantidad','$precio','$subtotal',1,'$total')";
            writeLog("SQL Subpartida MAT: $sql4");
            $result4 = mysqli_query($mysqli, $sql4);

            if ($result4) {
                writeLog("✓ Subpartida MAT insertada con ID: " . mysqli_insert_id($mysqli));
            } else {
                writeLog("✗ ERROR insertando subpartida MAT: " . mysqli_error($mysqli));
            }
        }
    }

    if (substr("$codigo", 0, 3) == "EXT") {
        writeLog(">>> TIPO: EXTERNO (EXT)");
        writeLog("ID Partida asociada: " . ($id_presupuestos_partidas ?? 'NULL'));

        if (!$id_presupuestos_partidas) {
            writeLog("✗ ERROR: No hay partida activa para asociar el externo");
        } else {
            $getUnidades4 = $mysqli->query("SELECT * FROM unidades where simbolo='$ud_medida'");
            $id_unidad = null;
            while ($row = $getUnidades4->fetch_assoc()) {
                $id_unidad = $row['id'];
            }
            if (!$id_unidad) {
                $id_unidad = 2;
                writeLog("⚠ Unidad no encontrada, usando ID por defecto: 2");
            }

            $subtotal = round($cantidad * $precio, 2);
            $total = round($subtotal * 1.21, 2); // forzamos iva a 21%
            writeLog("Cálculos: Cantidad=$cantidad * Precio=$precio = Subtotal=$subtotal | Total (con IVA)=$total");

            $sql5 = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_categoria,concepto,id_unidad,cantidad,precio,subtotal,id_iva,total) VALUES ('$id_presupuestos_partidas',3,'$concepto','$id_unidad','$cantidad','$precio','$subtotal',1,'$total')";
            writeLog("SQL Subpartida EXT: $sql5");
            $result5 = mysqli_query($mysqli, $sql5);

            if ($result5) {
                writeLog("✓ Subpartida EXT insertada con ID: " . mysqli_insert_id($mysqli));
            } else {
                writeLog("✗ ERROR insertando subpartida EXT: " . mysqli_error($mysqli));
            }
        }
    }



    if ($id_presupuestos_partidas) {
        writeLog(">>> RECALCULANDO TOTALES DE PARTIDA ID: $id_presupuestos_partidas");
        //Recalculamos totales y subtotales de la partida para asignarlo
        $getSubtotalPartida = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas'");
        $result = mysqli_fetch_assoc($getSubtotalPartida);
        $subtotalPartida = $result['subtotalSubpartidas'] ?? 0;
        writeLog("Subtotal calculado: $subtotalPartida");

        $getTotalPartida = mysqli_query($mysqli, "SELECT SUM(total) AS totalSubpartidas FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas'");
        $result = mysqli_fetch_assoc($getTotalPartida);
        $totalPartida = $result['totalSubpartidas'] ?? 0;
        writeLog("Total calculado: $totalPartida");

        $sql12 = "UPDATE presupuestos_partidas set subtotal=$subtotalPartida,total=$totalPartida where id=$id_presupuestos_partidas";
        writeLog("SQL Actualizar totales: $sql12");
        $result12 = mysqli_query($mysqli, $sql12);
        if ($result12) {
            writeLog("✓ Totales actualizados correctamente");
        } else {
            writeLog("✗ ERROR actualizando totales: " . mysqli_error($mysqli));
        }
    }

    if (!$codigo && !empty($concepto)) {
        writeLog(">>> ACTUALIZANDO DESCRIPCIÓN DE PARTIDA");
        writeLog("ID Partida: " . ($id_presupuestos_partidas ?? 'NULL'));
        writeLog("Descripción: '$concepto'");
        $sqlww3 = "UPDATE presupuestos_partidas set descripcion='$concepto' where id=$id_presupuestos_partidas";
        writeLog("SQL Descripción: $sqlww3");
        $result_desc = mysqli_query($mysqli, $sqlww3);
        if ($result_desc) {
            writeLog("✓ Descripción actualizada");
        } else {
            writeLog("✗ ERROR actualizando descripción: " . mysqli_error($mysqli));
        }
    }

}

writeLog("\n=== FIN DE IMPORTACIÓN CSV ===");
writeLog("Total de líneas procesadas: $lineNumber");
writeLog("Archivo de log generado: $logPath");

// Mostrar mensaje con ubicación del archivo de log
echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Importación Completada</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .container { max-width: 600px; margin: 0 auto; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 5px; color: #155724; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin-top: 20px; color: #0c5460; }
        .button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .button:hover { background: #0056b3; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success'>
            <h2>✓ Importación completada</h2>
            <p>Se han procesado <strong>$lineNumber líneas</strong> del archivo CSV.</p>
        </div>
        <div class='info'>
            <h3>📋 Archivo de debugging generado:</h3>
            <p><code>$logFile</code></p>
            <p><small>Ubicación: <code>$logPath</code></small></p>
            <p>Revisa este archivo para ver el detalle de cada operación y detectar posibles errores.</p>
        </div>
        <a href='../../pages/presupuestoDetail.php?id_presupuesto=$id_presupuesto' class='button'>Ver presupuesto</a>
    </div>
    <script>
        // Redirigir automáticamente después de 5 segundos
        setTimeout(function() {
            window.location.href = '../../pages/presupuestoDetail.php?id_presupuesto=$id_presupuesto';
        }, 5000);
    </script>
</body>
</html>";
?>