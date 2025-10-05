<?php
if (isset($_GET['pageno'])) { $pageno = $_GET['pageno']; } else { $pageno = 1; }

// Parametros iniciales
$no_of_records_per_page = 50;
$offset = ($pageno - 1) * $no_of_records_per_page;

// Calculo del total de registros
$totalRegistros=$mysqli->query("$query");
$total_rows = $totalRegistros->num_rows;

// Paginal totales
$total_pages = ceil($total_rows / $no_of_records_per_page);

