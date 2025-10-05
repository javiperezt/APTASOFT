<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "../conexion.php";
require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
    exit;
}

$year = date("Y");
$f_id_empresa = $_GET['id_empresa'] ?? 1;

// Obtener años disponibles de facturas, gastos y certificaciones
$years = [];
$res = $mysqli->query("SELECT DISTINCT YEAR(fecha_inicio) as y FROM facturas UNION SELECT DISTINCT YEAR(fecha_inicio) FROM gastos UNION SELECT DISTINCT YEAR(fecha) FROM certificaciones ORDER BY y DESC");
if ($res) {
    while ($row = $res->fetch_row()) {
        $years[] = $row[0];
    }
}

// Seleccionar el año en curso por defecto si no se pasa year por GET
$defaultYear = date('Y');
$year = isset($_GET['year']) ? intval($_GET['year']) : (in_array($defaultYear, $years) ? $defaultYear : (count($years) ? $years[0] : date('Y')));

// INGRESOS (facturas)
$sql = "SELECT SUM(fp.subtotal) AS total_ingresos
        FROM facturas f
        JOIN facturas_partidas fp ON fp.id_factura = f.id
        WHERE YEAR(f.fecha_inicio) = $year
          AND f.id_empresa = $f_id_empresa
          AND f.is_active = 1";
$res = $mysqli->query($sql);
if (!$res) { die("Error en consulta de ingresos: " . $mysqli->error); }
$totalIngresos = $res->fetch_assoc()['total_ingresos'] ?? 0;

// GASTOS
$sql = "SELECT SUM(gl.subtotal) AS total_gastos
        FROM gastos g
        JOIN gastos_lineas gl ON gl.id_gasto = g.id
        WHERE YEAR(g.fecha_inicio) = $year
          AND g.id_empresa = $f_id_empresa
          AND g.is_active = 1";
$res = $mysqli->query($sql);
if (!$res) { die("Error en consulta de gastos: " . $mysqli->error); }
$totalGastos = $res->fetch_assoc()['total_gastos'] ?? 0;

// CERTIFICACIONES
$sql = "SELECT SUM(cp.total) AS total_certificaciones
        FROM certificaciones c
        JOIN certificaciones_partidas cp ON cp.id_certificacion = c.id
        JOIN obras o ON c.id_obra = o.id
        WHERE YEAR(c.fecha) = $year
          AND o.id_empresa = $f_id_empresa
          AND c.is_active = 1";
$res = $mysqli->query($sql);
if (!$res) { die('Error en consulta de certificaciones: ' . $mysqli->error); }
$totalCertificaciones = $res->fetch_assoc()['total_certificaciones'] ?? 0;

// OBRAS ACTIVAS (todas)
$sql = "SELECT o.id, o.titulo, c.nombre as cliente
        FROM obras o
        LEFT JOIN contactos c ON o.id_contacto = c.id
        WHERE o.is_active = 1
          AND o.id_empresa = $f_id_empresa
        ORDER BY o.fecha_inicio DESC";
$res = $mysqli->query($sql);
if (!$res) { die('Error en consulta de obras activas: ' . $mysqli->error); }
$obras = [];
while ($row = $res->fetch_assoc()) {
    $obras[] = $row;
}
$numObras = count($obras);

// PAGOS PENDIENTES (gastos con estado 1 o 3)
$sql = "SELECT g.codigo, c.nombre as contacto, g.fecha_inicio, g.fecha_vencimiento, g.id as id_gasto,
               (SELECT SUM(gl.subtotal) FROM gastos_lineas gl WHERE gl.id_gasto = g.id) as total_gasto,
               (SELECT COALESCE(SUM(gp.importe), 0) FROM gastos_pagos gp WHERE gp.id_gasto = g.id AND gp.estado = 'pagado') as total_pagado
        FROM gastos g
        LEFT JOIN contactos c ON g.id_contacto = c.id
        WHERE g.id_estado IN (1,3)
          AND g.is_active = 1
          AND YEAR(g.fecha_inicio) = $year
          AND g.id_empresa = $f_id_empresa
        HAVING total_gasto > total_pagado
        ORDER BY g.fecha_inicio ASC";
$res = $mysqli->query($sql);
if (!$res) { die('Error en consulta de pagos pendientes: ' . $mysqli->error); }
$pagos = [];
while ($row = $res->fetch_assoc()) {
    $row['importe_pendiente'] = $row['total_gasto'] - $row['total_pagado'];
    $pagos[] = $row;
}
$numPagos = count($pagos);

$totalImportePagos = 0;
foreach ($pagos as $pago) {
    $totalImportePagos += $pago['importe_pendiente'];
}

// COBROS PENDIENTES (facturas con estado 1 o 3)
$sql = "SELECT CONCAT(f.pref_ref, f.pref_ref_year, f.ref) AS codigo, c.nombre as contacto, f.fecha_inicio, f.fecha_vencimiento, f.id as id_factura,
               (SELECT SUM(fp.subtotal) FROM facturas_partidas fp WHERE fp.id_factura = f.id) as total_factura,
               (SELECT COALESCE(SUM(fp.importe), 0) FROM facturas_pagos fp WHERE fp.id_factura = f.id AND fp.estado = 'cobrado') as total_cobrado
        FROM facturas f
        LEFT JOIN contactos c ON f.id_contacto = c.id
        WHERE f.id_estado IN (1,3)
          AND f.is_active = 1
          AND YEAR(f.fecha_inicio) = $year
          AND f.id_empresa = $f_id_empresa
        HAVING total_factura > total_cobrado
        ORDER BY f.fecha_inicio ASC";
$res = $mysqli->query($sql);
if (!$res) { die('Error en consulta de cobros pendientes: ' . $mysqli->error); }
$cobros = [];
while ($row = $res->fetch_assoc()) {
    $row['importe_pendiente'] = $row['total_factura'] - $row['total_cobrado'];
    $cobros[] = $row;
}
$numCobros = count($cobros);
$totalImporteCobros = 0;
foreach ($cobros as $cobro) {
    $totalImporteCobros += $cobro['importe_pendiente'];
}
?>
<html lang="es">
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>
<div class="container mt-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-black fw-bold fs-3 mb-0">Panel de inicio</p>
    </div>
    <div class="bg-white rounded-3 p-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <?php include "../components/empresaFilter.php"; ?>
            <form method="get" class="d-flex align-items-center gap-2 mb-0">
                <label for="year" class="mb-0">Año:</label>
                <select name="year" id="year" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="id_empresa" value="<?= htmlspecialchars($f_id_empresa) ?>">
            </form>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-4 mb-3">
            <div class="bg-white rounded-3 p-4 text-center">
                <p>Ingresos</p>
                <span class="fs-2 fw-bold"><?= number_format($totalIngresos, 2, ',', '.') ?>€</span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="bg-white rounded-3 p-4 text-center">
                <p>Gastos</p>
                <span class="fs-2 fw-bold"><?= number_format($totalGastos, 2, ',', '.') ?>€</span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="bg-white rounded-3 p-4 text-center">
                <p>Certificaciones</p>
                <span class="fs-2 fw-bold"><?= number_format($totalCertificaciones, 2, ',', '.') ?>€</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="bg-white rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                <span>Obras activas <span class="badge bg-primary ms-2"><?= $numObras ?></span></span>
                <a href="obras.php" class="text-decoration-none">Ver todas &gt;</a>
            </div>
            <div style="max-height: 430px; overflow-y: auto;">
                <table class="table tableComon bg-white rounded-3 mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="position: sticky; top: 0; z-index: 2; ">
                        <tr>
                            <th class="flex-grow-1" style="position: sticky; top: 0; ">Obra</th>
                            <th class="flex-grow-1" style="position: sticky; top: 0; ">Cliente</th>
                            <th style="width: 40px; position: sticky; top: 0; "></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($obras as $obra): ?>
                        <tr>
                            <td><?= htmlspecialchars($obra['titulo']) ?></td>
                            <td><?= htmlspecialchars($obra['cliente']) ?></td>
                            <td class="text-end"><a href="obraDetail.php?id_obra=<?= $obra['id'] ?>"><i class="bi bi-arrow-right fs-5" style="color: #D2D5DA"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bg-white rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                <span>Pagos pendientes <span class="badge bg-primary ms-2"><?= $numPagos ?></span><span class="ms-2 fw-normal text-secondary">(<?= number_format($totalImportePagos, 2, ',', '.') ?>€)</span></span>
                <a href="gastos.php" class="text-decoration-none">Ver todos &gt;</a>
            </div>
            <div style="max-height: 170px; overflow-y: auto; margin-bottom: 1rem;">
                <table class="table tableComon bg-white rounded-3 mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="position: sticky; top: 0; z-index: 2; ">
                        <tr>
                            <th style="width: 90px; position: sticky; top: 0; ">Código</th>
                            <th style="width: 180px; position: sticky; top: 0; ">Contacto</th>
                            <th style="width: 110px; position: sticky; top: 0; ">Fecha</th>
                            <th style="width: 110px; position: sticky; top: 0; ">Vencimiento</th>
                            <th style="width: 110px; position: sticky; top: 0; ">Importe</th>
                            <th style="width: 40px; position: sticky; top: 0; "></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><?= htmlspecialchars($pago['codigo']) ?></td>
                            <td><?= htmlspecialchars($pago['contacto']) ?></td>
                            <td><?= date('d/m/Y', strtotime($pago['fecha_inicio'])) ?></td>
                            <td><?= $pago['fecha_vencimiento'] ? date('d/m/Y', strtotime($pago['fecha_vencimiento'])) : '-' ?></td>
                            <td><?= number_format($pago['importe_pendiente'], 2, ',', '.') ?>€</td>
                            <td class="text-end"><a href="gastoDetail.php?id_gasto=<?= $pago['id_gasto'] ?>"><i class="bi bi-arrow-right fs-5" style="color: #D2D5DA"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-white rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                <span>Cobros pendientes <span class="badge bg-primary ms-2"><?= $numCobros ?></span><span class="ms-2 fw-normal text-secondary">(<?= number_format($totalImporteCobros, 2, ',', '.') ?>€)</span></span>
                <a href="facturas.php" class="text-decoration-none">Ver todos &gt;</a>
            </div>
            <div style="max-height: 170px; overflow-y: auto; margin-bottom: 1rem;">
                <table class="table tableComon bg-white rounded-3 mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="position: sticky; top: 0; z-index: 2; ">
                        <tr>
                            <th style="width: 90px; position: sticky; top: 0; ">Código</th>
                            <th style="width: 180px; position: sticky; top: 0; ">Contacto</th>
                            <th style="width: 110px; position: sticky; top: 0; ">Fecha</th>
                            <th style="width: 110px; position: sticky; top: 0; ">Vencimiento</th>
                            <th style="width: 110px; position: sticky; top: 0; ">Importe</th>
                            <th style="width: 40px; position: sticky; top: 0; "></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cobros as $cobro): ?>
                        <tr>
                            <td><?= htmlspecialchars($cobro['codigo']) ?></td>
                            <td><?= htmlspecialchars($cobro['contacto']) ?></td>
                            <td><?= date('d/m/Y', strtotime($cobro['fecha_inicio'])) ?></td>
                            <td><?= $cobro['fecha_vencimiento'] ? date('d/m/Y', strtotime($cobro['fecha_vencimiento'])) : '-' ?></td>
                            <td><?= number_format($cobro['importe_pendiente'], 2, ',', '.') ?>€</td>
                            <td class="text-end"><a href="facturaDetail.php?id_factura=<?= $cobro['id_factura'] ?>"><i class="bi bi-arrow-right fs-5" style="color: #D2D5DA"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
<script>
function filterHandler() {
    var id_empresa = document.querySelector('input[name="btnradio"]:checked').value;
    var year = document.getElementById('year') ? document.getElementById('year').value : '';
    var url = 'inicio_dashboard.php?id_empresa=' + encodeURIComponent(id_empresa);
    if (year) url += '&year=' + encodeURIComponent(year);
    window.location.href = url;
}
</script>
</html> 