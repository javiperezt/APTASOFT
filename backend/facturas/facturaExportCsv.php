<?php

// Load the database configuration file
include_once '../../conexion.php';

$fecha_inicio = filter_input(INPUT_GET, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_fin = filter_input(INPUT_GET, 'fecha_fin', FILTER_SANITIZE_SPECIAL_CHARS);


// Fetch records from database
$query = $mysqli->query("SELECT * FROM facturas where is_active=1 and fecha_inicio BETWEEN '$fecha_inicio' and '$fecha_fin'");

if ($query->num_rows >= 0) {

    $delimiter = ",";
    $filename = "facturas" . date('Y_m_d') . ".xls";

    // Create a file pointer
    $f = fopen('php://memory', 'w');

    // Set column headers
    $fields = array('fecha_inicio', 'fecha_vencimiento', 'ref', 'Nombre_contacto', 'Titulo_obra', 'subtotal', 'total', 'estado');
    fputcsv($f, $fields, $delimiter);

    // Output each row of the data, format line as csv and write to file pointer
    while ($row = $query->fetch_assoc()) {
        $id_factura = $row['id'];
        $id_contacto = $row['id_contacto'];
        $id_obra = $row['id_obra'];
        $id_estado = $row['id_estado'];
        $pref_ref = $row['pref_ref'];
        $pref_ref_year = $row['pref_ref_year'];
        $ref = $row['ref'];
        $ref = $pref_ref . $pref_ref_year . $ref;
        $fecha_inicio = $row['fecha_inicio'];
        $fecha_vencimiento = $row['fecha_vencimiento'];

        // subtotal factura
        $q = mysqli_query($mysqli, "SELECT SUM(subtotal) AS x FROM facturas_partidas where id_factura='$id_factura'");
        $result = mysqli_fetch_assoc($q);
        $subtotal = round($result['x'] ?? 0, 2);

        // total factura
        $q = mysqli_query($mysqli, "SELECT SUM(total) AS x FROM facturas_partidas where id_factura='$id_factura'");
        $result = mysqli_fetch_assoc($q);
        $total = round($result['x'] ?? 0, 2);

        $c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
        while ($row = $c1->fetch_assoc()) {
            $nombre_contacto = $row['nombre'];
        }

        $c2 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
        while ($row = $c2->fetch_assoc()) {
            $titulo_obra = $row['titulo'];
        }

        if ($id_estado) {
            $c3 = $mysqli->query("SELECT * FROM facturas_estados where id=$id_estado");
            while ($row = $c3->fetch_assoc()) {
                $estado = $row['estado'];
            }
        }

        $lineData = array($fecha_inicio, $fecha_vencimiento, $ref, $nombre_contacto, $titulo_obra, $subtotal, $total, $estado);
        fputcsv($f, $lineData, $delimiter);
    }

    // Move back to beginning of file
    fseek($f, 0);

    // Set headers to download file rather than displayed
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    //output all remaining data on a file pointer
    fpassthru($f);
}
exit;
?>