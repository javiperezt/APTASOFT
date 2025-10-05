<?php
$year = $consecutiveNumber->getCurrentYear();
// Buscamos la referencia del ultimo presupuesto insertado para predecirle el proximo numero
$getFirstBudget2 = $mysqli->query("SELECT * FROM presupuestos where pref_ref_year='$year' AND pref_ref='E' order by ref ");
while ($row = $getFirstBudget2->fetch_assoc()) {
    $lastRef = $row['ref'];
}

if ($getFirstBudget2->num_rows > 0) {
    $documentNumber = $consecutiveNumber->generateNumbers("$lastRef", 2, 4);
    $year = $consecutiveNumber->getCurrentYear();
    $pref = $consecutiveNumber->getPref(1);
} else {
    $documentNumber = $consecutiveNumber->generateNumbers(0000, 2, 4);
    $year = $consecutiveNumber->getCurrentYear();
    $pref = $consecutiveNumber->getPref(1);
}

// Le damos predefinido el proximo numero de presupuesto que se va a insertar
$nextRefBudget = $pref . $year . $documentNumber;