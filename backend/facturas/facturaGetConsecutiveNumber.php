<?php
$year = $consecutiveNumber->getCurrentYear();
// Buscamos la referencia de la ultima factura insertada para predecirle el proximo numero
$getFirstInvoce = $mysqli->query("SELECT * FROM facturas where pref_ref_year='$year' AND pref_ref='F' order by ref ");
while ($row = $getFirstInvoce->fetch_assoc()) {
    $lastRef = $row['ref'];
}

if ($getFirstInvoce->num_rows > 0) {
    $documentNumber = $consecutiveNumber->generateNumbers("$lastRef", 2, 4);
    $year = $consecutiveNumber->getCurrentYear();
    $pref = $consecutiveNumber->getPref(2);
} else {
    $documentNumber = $consecutiveNumber->generateNumbers(0000, 2, 4);
    $year = $consecutiveNumber->getCurrentYear();
    $pref = $consecutiveNumber->getPref(2);
}

// Le damos predefinido el proximo numero de factura que se va a insertar
$nextRefInvoice = $pref . $year . $documentNumber;
