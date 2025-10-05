<?php
include "../../conexion.php";
require_once "../../ConsecutiveNumbers.php";
$consecutiveNumber = new ConsecutiveNumbers();

$ref = filter_input(INPUT_POST, 'ref', FILTER_SANITIZE_SPECIAL_CHARS);
$fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empresa = filter_input(INPUT_POST, 'id_empresa', FILTER_SANITIZE_SPECIAL_CHARS);
$id_contacto = filter_input(INPUT_POST, 'id_contacto', FILTER_SANITIZE_SPECIAL_CHARS);

// Generacion del numero de la factura
$year = $consecutiveNumber->getCurrentYear();
// Buscamos la referencia de la ultima factura insertada
$getFirstInvoice = $mysqli->query("SELECT * FROM facturas where pref_ref='F' AND pref_ref_year='$year' order by ref");
while ($row = $getFirstInvoice->fetch_assoc()) {
    $lastRef = $row['ref'];
}

//Limpiamos espacios y caracteres especiales
$ref = str_replace(' ', '', $ref);
$ref = str_replace('-', '', $ref);
$ref = str_replace('_', '', $ref);
$ref = str_replace('.', '', $ref);
$ref = str_replace(',', '', $ref);

//En caso de ser la primera factura creada hay que asignarle el número 0001 automaticamente (siempre que el  el usuario no ha puesto una referencia manualmente)
if ($getFirstInvoice->num_rows == 0 && !$ref) {
    $documentNumber = $consecutiveNumber->generateNumbers(0000, 2, 4);
    $year = $consecutiveNumber->getCurrentYear();
    $pref = $consecutiveNumber->getPref(2);
} //En caso de que haya puesto una referencia manualmente se pone esa y se omite el orden consecutivo
if ($getFirstInvoice->num_rows > 0 && !$ref) {
    $documentNumber = $consecutiveNumber->generateNumbers("$lastRef", 2, 4);
    $year = $consecutiveNumber->getCurrentYear();
    $pref = $consecutiveNumber->getPref(2);
}
if ($ref || $ref != "") {
    $getPref = substr($ref, 0, 1);
    $getYear = substr($ref, 1, 2);
    $currentYear = $consecutiveNumber->getCurrentYear();
    // Si lo que viene introducido es una referencia con la estructura adecuada seguimos manteniendo el orden establecido
    if ($getPref == "F" && $getYear == $currentYear) {
        $documentNumber = substr($ref, 3);
        // Añadimos ceros (0) a la izquierda para que en caso de que haya introducido un numero tipo 25 convertirlo en 0025
        $documentNumber = str_pad($documentNumber, 4, '0', STR_PAD_LEFT);
        $year = $consecutiveNumber->getCurrentYear();
        $pref = "F";
    } else {
        //Si no sigue la estructura adecuada, se inserta lo que ha introducido el usuario directamente como referencia
        $documentNumber = $ref;
        //$year = NULL;
        $year = NULL;
        $pref = NULL;
    }
}

$sql = "INSERT INTO facturas (id_obra,id_empresa,pref_ref,pref_ref_year,ref,fecha_inicio,id_contacto) VALUES ('$id_obra','$id_empresa','$pref','$year','$documentNumber','$fecha_inicio','$id_contacto')";
mysqli_query($mysqli, $sql);
$id_factura = mysqli_insert_id($mysqli);

header("Location: ../../pages/facturaDetail.php?id_factura=$id_factura");

