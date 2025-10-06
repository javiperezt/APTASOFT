<?php
session_start();

include "../conexion.php";
require_once "../DateClass.php";
require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}



$dateClass = new DateClass();

$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_empresa = $row['id_empresa'];
    $id_canal_entrada = $row['id_canal_entrada'];
    $id_usuario_asignado = $row['id_usuario_asignado'];
    $id_estado = $row['id_estado'];
    $titulo = $row['titulo'];
    $fecha_inicio = $row['fecha_inicio'];
    $fecha_fin = $row['fecha_fin'];
    $localizacion = $row['localizacion'];
}

// ========== CALCULAR MÉTRICAS TOTALES DE LA OBRA ==========
$total_obra_costes = 0;
$total_obra_ingresos = 0;
$total_obra_resultado = 0;
$total_obra_margen_porcentaje = 0;
$partidas_con_perdidas = 0;
$partidas_totales = 0;

// Variables para acumular por categoría
$total_MO_costes = 0;
$total_MO_ingresos = 0;
$total_MAT_costes = 0;
$total_MAT_ingresos = 0;
$total_EXT_costes = 0;
$total_EXT_ingresos = 0;

?>
<html lang="es">
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
    <style>
        /* Diseño minimalista estilo Apple */
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
        }

        .kpi-card:hover {
            border-color: #d1d1d1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .kpi-label {
            font-size: 13px;
            font-weight: 500;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-icon {
            font-size: 20px;
            opacity: 0.6;
        }

        .kpi-value {
            font-size: 32px;
            font-weight: 600;
            line-height: 1.1;
            margin-bottom: 8px;
            color: #1d1d1f;
        }

        .kpi-footer {
            font-size: 12px;
            color: #86868b;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .kpi-meta {
            font-size: 12px;
        }

        .kpi-progress-bar {
            width: 100%;
            height: 4px;
            background: #f5f5f7;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 4px;
        }

        .kpi-progress-fill {
            height: 100%;
            background: #0071e3;
            transition: width 0.3s ease;
        }

        .kpi-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
        }

        .kpi-badge.success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .kpi-badge.danger {
            background: #ffebee;
            color: #c62828;
        }

        /* Tabla minimalista */
        .tableComon {
            font-size: 13px;
        }

        .tableComon thead th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #86868b;
            background-color: #fafafa;
            border-bottom: 1px solid #e5e5e5;
            border-top: none;
            white-space: nowrap;
            padding: 12px 8px;
        }

        .tableComon tbody tr {
            border-bottom: 1px solid #f5f5f7;
            transition: background-color 0.15s ease;
        }

        .tableComon tbody tr:hover {
            background-color: #fafafa;
        }

        .tableComon tbody td {
            padding: 12px 8px;
            vertical-align: middle;
        }

        /* Badges minimalistas */
        .badge {
            font-weight: 500;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
        }

        .badge.bg-success {
            background: #e8f5e9 !important;
            color: #2e7d32 !important;
        }

        .badge.bg-warning {
            background: #fff3e0 !important;
            color: #e65100 !important;
        }

        .badge.bg-danger {
            background: #ffebee !important;
            color: #c62828 !important;
        }

        .badge.bg-info {
            background: #e3f2fd !important;
            color: #1565c0 !important;
        }

        /* Filtros minimalistas */
        .filter-btn {
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px;
            padding: 6px 14px;
            border: 1px solid #e5e5e5;
            background: #ffffff;
            color: #1d1d1f;
            transition: all 0.2s ease;
        }

        .filter-btn:hover {
            border-color: #d1d1d1;
            background: #fafafa;
        }

        .filter-btn.active {
            background: #0071e3;
            color: #ffffff;
            border-color: #0071e3;
        }

        /* Progress bars minimalistas */
        .progress {
            height: 3px;
            background: #f5f5f7;
            border-radius: 2px;
            margin-top: 4px;
        }

        .progress-bar {
            background: #0071e3;
            border-radius: 2px;
        }

        .progress-bar.bg-success {
            background: #34c759;
        }

        .progress-bar.bg-warning {
            background: #ff9500;
        }

        .progress-bar.bg-danger {
            background: #ff3b30;
        }
    </style>
</head>
<body>
<?php include "../components/navbar.php"; ?>


<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <div class="d-flex gap-2 align-items-center">
        <a href="obras.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Obra Resultados</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <?php include "../components/obras/navObra.php" ;?>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->

<?php
// Necesitamos hacer un primer loop para calcular los totales antes de mostrar los KPIs
$temp_total_costes = 0;
$temp_total_ingresos = 0;
$temp_total_resultado = 0;
$temp_partidas_perdidas = 0;
$temp_partidas_totales = 0;

$temp_presupuestos = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
while ($temp_row = $temp_presupuestos->fetch_assoc()) {
    $temp_id_presupuesto = $temp_row['id'];

    $temp_partidas = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$temp_id_presupuesto");
    while ($temp_row2 = $temp_partidas->fetch_assoc()) {
        $temp_id_presupuestos_partidas = $temp_row2['id'];
        $temp_cantidad_partida = $temp_row2['cantidad'];

        // MO
        $temp_horas_total = 0;
        $temp_obras_partes = $mysqli->query("SELECT * FROM obras_registro_partes where id_presupuesto=$temp_id_presupuesto and id_presupuestos_partidas=$temp_id_presupuestos_partidas");
        while ($temp_row3 = $temp_obras_partes->fetch_assoc()) {
            $temp_horas_total += $dateClass->getSecondsFromFormatedHour($temp_row3['horas']);
        }
        $temp_coste_MO = round(($temp_horas_total / 3600) * 25, 2);

        $temp_ingresos_MO = 0;
        $temp_sub_MO = $mysqli->query("SELECT SUM(subtotal) as total FROM presupuestos_subpartidas where id_presupuesto_partidas=$temp_id_presupuestos_partidas and id_categoria=1");
        if ($temp_row4 = $temp_sub_MO->fetch_assoc()) {
            $temp_ingresos_MO = round(floatval($temp_row4['total']) * floatval($temp_cantidad_partida), 2);
        }

        // MAT - Incluye categorías 1 y 2 (material/gastos generales)
        $temp_coste_MAT = 0;
        $temp_gastos_MAT = $mysqli->query("SELECT gl.subtotal FROM gastos g INNER JOIN gastos_lineas gl ON g.id = gl.id_gasto WHERE g.id_obra=$id_obra AND g.id_categoria_gasto IN (1,2) AND g.is_active=1 AND gl.id_presupuestos_partidas=$temp_id_presupuestos_partidas");
        while ($temp_row5 = $temp_gastos_MAT->fetch_assoc()) {
            $temp_coste_MAT += floatval($temp_row5['subtotal']);
        }

        $temp_ingresos_MAT = 0;
        $temp_sub_MAT = $mysqli->query("SELECT SUM(subtotal) as total FROM presupuestos_subpartidas where id_presupuesto_partidas=$temp_id_presupuestos_partidas and id_categoria=2");
        if ($temp_row6 = $temp_sub_MAT->fetch_assoc()) {
            $temp_ingresos_MAT = floatval($temp_row6['total']) * floatval($temp_cantidad_partida);
        }

        // EXT
        $temp_coste_EXT = 0;
        $temp_gastos_EXT = $mysqli->query("SELECT gl.subtotal FROM gastos g INNER JOIN gastos_lineas gl ON g.id = gl.id_gasto WHERE g.id_obra=$id_obra AND g.id_categoria_gasto=3 AND g.is_active=1 AND gl.id_presupuestos_partidas=$temp_id_presupuestos_partidas");
        while ($temp_row7 = $temp_gastos_EXT->fetch_assoc()) {
            $temp_coste_EXT += floatval($temp_row7['subtotal']);
        }

        $temp_ingresos_EXT = 0;
        $temp_sub_EXT = $mysqli->query("SELECT SUM(subtotal) as total FROM presupuestos_subpartidas where id_presupuesto_partidas=$temp_id_presupuestos_partidas and id_categoria=3");
        if ($temp_row8 = $temp_sub_EXT->fetch_assoc()) {
            $temp_ingresos_EXT = floatval($temp_row8['total']) * floatval($temp_cantidad_partida);
        }

        $temp_coste_total = $temp_coste_MO + $temp_coste_MAT + $temp_coste_EXT;
        $temp_ingreso_total = $temp_ingresos_MO + $temp_ingresos_MAT + $temp_ingresos_EXT;
        $temp_resultado_total = $temp_ingreso_total - $temp_coste_total;

        $temp_total_costes += $temp_coste_total;
        $temp_total_ingresos += $temp_ingreso_total;
        $temp_total_resultado += $temp_resultado_total;
        $temp_partidas_totales++;
        if ($temp_resultado_total < 0) {
            $temp_partidas_perdidas++;
        }
    }
}

$temp_margen_porcentaje = $temp_total_ingresos > 0 ? (($temp_total_resultado / $temp_total_ingresos) * 100) : 0;
$temp_desviacion_total = $temp_total_ingresos > 0 ? ((($temp_total_costes - $temp_total_ingresos) / $temp_total_ingresos) * 100) : 0;

// Determinar estado de salud
$estado_salud = 'excelente';
$estado_color = 'success';
$estado_icono = 'bi-emoji-smile-fill';
if ($temp_margen_porcentaje < 0) {
    $estado_salud = 'crítico';
    $estado_color = 'danger';
    $estado_icono = 'bi-emoji-frown-fill';
} elseif ($temp_margen_porcentaje < 10) {
    $estado_salud = 'bajo';
    $estado_color = 'warning';
    $estado_icono = 'bi-emoji-neutral-fill';
} elseif ($temp_margen_porcentaje < 20) {
    $estado_salud = 'bueno';
    $estado_color = 'info';
    $estado_icono = 'bi-emoji-smile';
}
?>

<!----------- KPIs RESUMEN ----------->
<div class="container-md my-3">
    <div class="row g-2">
        <!-- KPI 1: Rentabilidad Total -->
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Rentabilidad</span>
                    <span class="kpi-icon <?= $temp_total_resultado >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="bi bi-currency-euro"></i>
                    </span>
                </div>
                <div class="kpi-value <?= $temp_total_resultado >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= number_format($temp_total_resultado, 2, ',', '.') ?>€
                </div>
                <div class="kpi-footer">
                    <span class="kpi-meta">↑ <?= number_format($temp_total_ingresos, 2, ',', '.') ?>€</span>
                    <span class="kpi-meta">↓ <?= number_format($temp_total_costes, 2, ',', '.') ?>€</span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Margen % -->
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Margen</span>
                    <span class="kpi-icon text-primary">
                        <i class="bi bi-percent"></i>
                    </span>
                </div>
                <div class="kpi-value">
                    <?= number_format($temp_margen_porcentaje, 1, ',', '.') ?>%
                </div>
                <div class="kpi-footer">
                    <div class="kpi-progress-bar" style="width: 100%;">
                        <div class="kpi-progress-fill" style="width: <?= min(abs($temp_margen_porcentaje), 100) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Desviación Presupuestaria -->
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Desviación</span>
                    <span class="kpi-icon text-secondary">
                        <i class="bi bi-graph-<?= $temp_desviacion_total > 0 ? 'up' : 'down' ?>-arrow"></i>
                    </span>
                </div>
                <div class="kpi-value <?= abs($temp_desviacion_total) > 15 ? 'text-danger' : '' ?>">
                    <?= $temp_desviacion_total > 0 ? '+' : '' ?><?= number_format($temp_desviacion_total, 1, ',', '.') ?>%
                </div>
                <div class="kpi-footer">
                    <span class="kpi-meta"><?= $temp_partidas_perdidas ?>/<?= $temp_partidas_totales ?> con pérdidas</span>
                </div>
            </div>
        </div>

        <!-- KPI 4: Partidas -->
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Partidas</span>
                    <span class="kpi-icon text-dark">
                        <i class="bi bi-list-check"></i>
                    </span>
                </div>
                <div class="kpi-value">
                    <?= $temp_partidas_totales ?>
                </div>
                <div class="kpi-footer">
                    <?php if ($temp_partidas_perdidas > 0): ?>
                        <span class="kpi-badge danger"><?= $temp_partidas_perdidas ?> en pérdidas</span>
                    <?php else: ?>
                        <span class="kpi-badge success">Todas rentables</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!----------- END KPIs RESUMEN ----------->

<div class="bg-white container-md my-3 rounded-3 p-4">
    <!----------- FILTROS ----------->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">
                <i class="bi bi-list-ul"></i> Todas
            </button>
            <button class="btn btn-sm btn-outline-success filter-btn" data-filter="ok">
                <i class="bi bi-check-circle"></i> Rentables
            </button>
            <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="warning">
                <i class="bi bi-exclamation-triangle"></i> Alertas
            </button>
            <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="danger">
                <i class="bi bi-x-circle"></i> Pérdidas
            </button>
        </div>
        <div>
            <a id="exportar"
               class="btn btn-outline-secondary d-flex align-items-center" style="height: 38px"> <i
                        class="bi bi-cloud-arrow-down fs-6"></i></a>
        </div>
    </div>
    <!----------- END FILTROS ----------->


    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Estado</th>
                <th scope="col">Sección</th>
                <th scope="col">Partida</th>
                <th scope="col">MO Gasto</th>
                <th scope="col">MO Prev</th>
                <tH scope="col">MO Desv%</tH>
                <th scope="col">MAT Gasto</th>
                <th scope="col">MAT Prev</th>
                <th scope="col">MAT Desv%</th>
                <th scope="col">EXT Gasto</th>
                <th scope="col">EXT Prev</th>
                <th scope="col">EXT Desv%</th>
                <th scope="col">TOTAL Gasto</th>
                <th scope="col">TOTAL Prev</th>
                <th scope="col">MARGEN</th>
                <th scope="col">MARGEN %</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $presupuestos = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
            while ($row = $presupuestos->fetch_assoc()) {
                $id_presupuesto = $row['id'];

                $presupuestos_partidas = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto order by id_capitulo");
                while ($row = $presupuestos_partidas->fetch_assoc()) {
                    $id_presupuestos_partidas = $row['id'];
                    $id_capitulo = $row['id_capitulo'];
                    $partida = $row['partida'];
                    $descripcion = $row['descripcion'];
                    $fecha_inicio = $row['fecha_inicio'];
                    $fecha_vencimiento = $row['fecha_vencimiento'];
                    $cantidad_partida = $row['cantidad'];


                    //  ----------- CALCULO COSTE MANO DE OBRA -------------- //
                    //  Extraemos el tiempo imputado a cada subpartida por los trajadores
                    $horas = 0;
                    $seconds = 0;
                    $totalSeconds = 0;
                    $totalHoras = 0;
                    $costeManoObra = 0;
                    $obras_registro_partes = $mysqli->query("SELECT * FROM obras_registro_partes where id_presupuesto=$id_presupuesto and id_presupuestos_partidas=$id_presupuestos_partidas");
                    while ($row = $obras_registro_partes->fetch_assoc()) {
                        $horas = $row['horas'];
                        $seconds = $dateClass->getSecondsFromFormatedHour("$horas");
                        $totalSeconds += $seconds;
                    }

                    $totalHoras = $totalSeconds / 3600;
                    $precioHoraTrabajador = 25; // SE PONE 25€/h A MANO ESTIMADO
                    $costeManoObra = round($totalHoras * $precioHoraTrabajador, 2);


                    //  ----------- CALCULO INGRESOS MANO DE OBRA -------------- //
                    $subtotal_subpartida_MObra = 0;
                    $subtotales_subpartida_MObra = 0;
                    $ingresosManoObra = 0;
                    $totalManoObra = 0;
                    $presupuestos_subpartidas = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=1");
                    while ($row = $presupuestos_subpartidas->fetch_assoc()) {
                        $subtotal_subpartida_MObra = $row['subtotal'];
                        $subtotales_subpartida_MObra += $subtotal_subpartida_MObra;
                    }

                    $ingresosManoObra = round($subtotales_subpartida_MObra * $cantidad_partida, 2);

                    $totalManoObra = $ingresosManoObra - $costeManoObra;


                    //  ----------- CALCULO COSTE MATERIAL -------------- //
                    // Incluye categorías 1 y 2 (material/gastos generales)
                    $costeMaterial = 0;
                    $gastos_material_query = $mysqli->query("SELECT gl.subtotal FROM gastos g INNER JOIN gastos_lineas gl ON g.id = gl.id_gasto WHERE g.id_obra=$id_obra AND g.id_categoria_gasto IN (1,2) AND g.is_active=1 AND gl.id_presupuestos_partidas=$id_presupuestos_partidas");
                    while ($row = $gastos_material_query->fetch_assoc()) {
                        $costeMaterial += floatval($row['subtotal']);
                    }


                    //  ----------- CALCULO INGRESOS MATERIAL -------------- //
                    $subtotal_subpartida_Material = 0;
                    $subtotales_subpartida_Material = 0;
                    $ingresosMaterial = 0;
                    $totalMaterial = 0;
                    $presupuestos_subpartidas2 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=2");
                    while ($row = $presupuestos_subpartidas2->fetch_assoc()) {
                        $subtotal_subpartida_Material = $row['subtotal'];
                        $subtotales_subpartida_Material += floatval($subtotal_subpartida_Material);
                    }

                    $ingresosMaterial = floatval($subtotales_subpartida_Material) * floatval($cantidad_partida);

                    $totalMaterial = $ingresosMaterial - $costeMaterial;


                    //  ----------- CALCULO COSTE OTROS -------------- //
                    $costeOtros = 0;
                    $id_gasto2 = "";
                    $subtotal_Otros = 0;
                    $gastos2 = $mysqli->query("SELECT * FROM gastos where id_obra=$id_obra and id_categoria_gasto=3 and is_active=1");
                    while ($row = $gastos2->fetch_assoc()) {
                        $id_gasto2 = $row['id'];
                        if ($id_gasto2) {
                            $gastos_lineas2 = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto='$id_gasto2' and id_presupuestos_partidas='$id_presupuestos_partidas'");
                            while ($row = $gastos_lineas2->fetch_assoc()) {
                                $subtotal_Otros = $row['subtotal'];
                                $costeOtros += $subtotal_Otros;
                            }
                        }
                    }

                    //  ----------- CALCULO INGRESOS OTROS -------------- //
                    $subtotal_subpartida_Otros = 0;
                    $subtotales_subpartida_Otros = 0;
                    $ingresosOtros = 0;
                    $totalOtros = 0;
                    $presupuestos_subpartidas3 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=3");
                    while ($row = $presupuestos_subpartidas3->fetch_assoc()) {
                        $subtotal_subpartida_Otros = $row['subtotal'];
                        $subtotales_subpartida_Otros += floatval($subtotal_subpartida_Otros);
                    }

                    $ingresosOtros = floatval($subtotales_subpartida_Otros) * floatval($cantidad_partida);

                    $totalOtros = $ingresosOtros - $costeOtros;

                    //  ----------- CALCULO RESULTADO TOTAL -------------- //
                    $totalCostes = 0;
                    $totalIngresos = 0;
                    $totalResultado = 0;
                    $totalCostes = $costeManoObra + $costeMaterial + $costeOtros;
                    $totalIngresos = $ingresosManoObra + $ingresosMaterial + $ingresosOtros;
                    $totalResultado = $totalIngresos - $totalCostes;

                    // Acumular totales de la obra
                    $total_obra_costes += $totalCostes;
                    $total_obra_ingresos += $totalIngresos;
                    $total_obra_resultado += $totalResultado;

                    // Acumular por categoría
                    $total_MO_costes += $costeManoObra;
                    $total_MO_ingresos += $ingresosManoObra;
                    $total_MAT_costes += $costeMaterial;
                    $total_MAT_ingresos += $ingresosMaterial;
                    $total_EXT_costes += $costeOtros;
                    $total_EXT_ingresos += $ingresosOtros;

                    // Contar partidas
                    $partidas_totales++;
                    if ($totalResultado < 0) {
                        $partidas_con_perdidas++;
                    }

                    // Calcular métricas adicionales por partida
                    $margen_partida = $totalIngresos > 0 ? (($totalResultado / $totalIngresos) * 100) : 0;
                    $desviacion_MO = $ingresosManoObra > 0 ? ((($costeManoObra - $ingresosManoObra) / $ingresosManoObra) * 100) : 0;
                    $desviacion_MAT = $ingresosMaterial > 0 ? ((($costeMaterial - $ingresosMaterial) / $ingresosMaterial) * 100) : 0;
                    $desviacion_EXT = $ingresosOtros > 0 ? ((($costeOtros - $ingresosOtros) / $ingresosOtros) * 100) : 0;

                    $c4 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                    while ($row = $c4->fetch_assoc()) {
                        $capitulo = $row['capitulo'];
                    }

                    include "../components/obras/dashboardLine.php";
                }
            }
            if ($presupuestos->num_rows == 0) {
                include "../components/noDataLine.php";
            }

            // Calcular margen porcentual total de la obra
            $total_obra_margen_porcentaje = $total_obra_ingresos > 0 ? (($total_obra_resultado / $total_obra_ingresos) * 100) : 0;
            ?>


            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->
</div>

<script>
    var minDate, maxDate;

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = minDate.val();
            var max = maxDate.val();
            var date = new Date(data[1]);
            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        }
    );

    $(document).ready(function () {
        // Create date inputs
        minDate = new DateTime($('#min'), {
            format: 'YYYY/MM/DD'
        });
        maxDate = new DateTime($('#max'), {
            format: 'YYYY/MM/DD'
        });

        // DataTables initialisation
        var table = $('#tabla_proyectos').DataTable({
            language: {
                url: '../espanol.json'
            },
            pageLength: 50
        });

        // Refilter the table
        $('#min, #max').on('change', function () {
            table.draw();
        });
    });

    $.fn.dataTable.ext.errMode = 'none'; // Desactiva alertas de error de DataTables

    // Filtros de partidas
    $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');

        // Actualizar botones activos
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Filtrar filas
        if (filter === 'all') {
            $('#tabla_proyectos tbody tr').show();
        } else {
            $('#tabla_proyectos tbody tr').each(function() {
                const estado = $(this).data('estado');
                if (estado === filter) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Redibujar DataTable si está activo
        if ($.fn.DataTable.isDataTable('#tabla_proyectos')) {
            table.draw();
        }
    });

    // Agregar un controlador de eventos de clic al botón "Exportar"
    $('#exportar').on('click', function () {
        // Obtener la tabla de DataTables
        var table = $('#tabla_proyectos').DataTable();

        // Obtener los datos filtrados
        var filteredData = table.rows({search: 'applied'}).data();

        // Crear un arreglo con las cabeceras y los datos filtrados
        var data = [];
        var headers = [];
        table.columns().every(function () {
            headers.push(this.header().textContent);
        });
        data.push(headers);
        filteredData.each(function (row) {
            data.push([row[0], row[1], row[2], row[3], row[4], row[5], row[6], row[7], row[8], row[9],  row[10], row[11], row[12], row[13]]); // Agregar fila de datos con el valor de estado procesado
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "resultados.xlsx");
    });
</script>
</body>
<?php include "../components/modals/obraNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
</html>