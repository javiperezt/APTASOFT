<?php
session_start();

include "../conexion.php";
require_once "../DateClass.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$dateClass = new DateClass();

// get today's day
$today = $dateClass->getCurrentUnix();
$today = $dateClass->getFormatedDate($today, "Y-m-d");
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
    <p class="text-black fw-bold fs-3">Tareas</p>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">


    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <div class="d-flex align-items-center gap-2 mb-4">
            <p>Filtro por fechas:</p>
            <input style="width: 200px" type="text" id="min" name="min" class="form-control form-control-sm"
                   placeholder="Desde">
            <input style="width: 200px" type="text" id="max" name="max" class="form-control form-control-sm"
                   placeholder="Hasta">
        </div>
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Obra</th>
                <th scope="col">Concepto</th>
                <th scope="col">Empleado</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c3 = $mysqli->query("SELECT ots.id_presupuestos_subpartidas,ps.fecha_vencimiento as fecha_vencimiento,ots.id_obra,ps.id_presupuesto_partidas, ps.concepto, GROUP_CONCAT(e.nombre SEPARATOR ', ') AS empleados_asignados
                FROM obras_trabajadores_subpartidas ots
                LEFT JOIN empleados e ON ots.id_empleado = e.id
                INNER JOIN presupuestos_subpartidas ps ON ots.id_presupuestos_subpartidas = ps.id WHERE ps.is_checked=0
                GROUP BY ots.id_presupuestos_subpartidas, ps.concepto,ots.id_obra");
            while ($row = $c3->fetch_assoc()) {
                $concepto = $row['concepto'];
                $fecha_vencimiento = $row['fecha_vencimiento'];
                $trabajadores = $row['empleados_asignados'];
                $id_obra = $row['id_obra'];
                $id_presupuesto_partidas = $row['id_presupuesto_partidas'];

                $c2 = $mysqli->query("SELECT * FROM obras WHERE id='$id_obra'");
                while ($row2 = $c2->fetch_assoc()) {
                    $titulo_obra = $row2['titulo'];
                }

                $c222 = $mysqli->query("SELECT * FROM presupuestos_partidas WHERE id='$id_presupuesto_partidas'");
                while ($row3 = $c222->fetch_assoc()) {
                    $partida = $row3['partida'];
                }

                echo "<tr>
                        <td>$fecha_vencimiento</td>
                        <td><a href='obraTareas.php?id_obra=$id_obra'>$titulo_obra</a></td>
                        <td>$partida</td>
                        <td>$trabajadores</td>
                        <td><a target='_blank' href='obraTareas.php?id_obra=$id_obra'><bi class='bi bi-arrow-right fs-6'></bi></a></td>
                      </tr>";
            }


            if ($c3->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->


</div>


</body>
<?php include "../components/modals/contactoNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>

    var minDate, maxDate;

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = minDate.val();
            var max = maxDate.val();
            var date = new Date(data[0]);
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
            format: 'DD/MM/YYYY'
        });
        maxDate = new DateTime($('#max'), {
            format: 'DD/MM/YYYY'
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
</script>
</html>