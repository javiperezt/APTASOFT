<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";


$c2 = $mysqli->query("SELECT * FROM gastos");
while ($row = $c2->fetch_assoc()) {
    $id_contactos_tipos_organizacion = $row['id'];
}

$year = date("Y");
$data = array();

$f_id_empresa = $_GET['id_empresa'];
if (!$f_id_empresa) {
    $f_id_empresa = 1;
}

for ($i = 1; $i < 13; $i++) {
    $monthWritten = date("F", mktime(0, 0, 0, "$i", 1, $year));
    $monthNumber = date("m", mktime(0, 0, 0, "$i", 1, $year));

    $c2 = $mysqli->query("SELECT * FROM gastos where MONTH(fecha_inicio)=$monthNumber and id_empresa=$f_id_empresa and is_active=1");
    $totalGastos = 0;
    while ($row = $c2->fetch_assoc()) {
        $id_gasto = $row['id'];

        if ($c2->num_rows > 0) {
            $c3 = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto='$id_gasto'");
            while ($row = $c3->fetch_assoc()) {
                $subtotal = $row['subtotal'];
                $totalGastos += $subtotal;
            }
        }
    }
    $data[$i - 1] = $totalGastos;
}


$data1 = array();
for ($i = 1; $i < 13; $i++) {
    $monthWritten = date("F", mktime(0, 0, 0, "$i", 1, $year));
    $monthNumber = date("m", mktime(0, 0, 0, "$i", 1, $year));

    $c4 = $mysqli->query("SELECT * FROM facturas where MONTH(fecha_inicio)=$monthNumber and id_empresa=$f_id_empresa and is_active=1");
    $totalIngreso = 0;
    while ($row = $c4->fetch_assoc()) {
        $id_factura = $row['id'];

        if ($c4->num_rows > 0) {
            $c5 = $mysqli->query("SELECT * FROM facturas_partidas where id_factura='$id_factura'");
            while ($row = $c5->fetch_assoc()) {
                $subtotal = $row['subtotal'];
                $totalIngreso += $subtotal;
            }
        }
    }
    $data1[$i - 1] = $totalIngreso;
}

?>

<html lang="es">
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>


<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <p class="text-black fw-bold fs-3">Dashboard</p>

    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">Resultados</a>
            </li>
            <!--<li class="nav-item">
                <a class="nav-link text-black" href="dashboardGeneral.php">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboardObras.php">Obras</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboardCaja.php">Caja</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->

<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <?php include "../components/empresaFilter.php"; ?>
        <!--<div class="d-flex">
            <input type="date" class="form-control">
            <a href="../backend/obras/obraResultadosCsv.php?id_obra=<?= $id_obra; ?>" target="_blank"
               class="btn btn-outline-secondary d-flex align-items-center ms-2" style="height: 38px"> <i
                        class="bi bi-cloud-arrow-down fs-6"></i></a>
        </div>-->
    </div>
</div>

<div class="bg-white container-md my-3 rounded-3 p-4">
    <canvas id="myChart2"></canvas>
</div>


</body>

<script>
    const ctx2 = document.getElementById('myChart2');

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [
                {
                    label: 'Ingresos',
                    data: <?=json_encode($data1);?>
                },
                {
                    label: 'Gastos',
                    data: <?=json_encode($data);?>
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Resultados'
                }
            }
        },
    });

    const filterHandler = () => {
        var id_empresa = $("input[name='btnradio']:checked").val();

        location.href = "dashboard.php?id_empresa=" + id_empresa;
    }
</script>

</html>