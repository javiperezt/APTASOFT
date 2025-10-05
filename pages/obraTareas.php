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
    <div class="d-flex gap-2 align-items-center">
        <a href="obras.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Tareas</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <?php include "../components/obras/navObra.php" ;?>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">

    <div class="d-flex justify-content-end">
        <a id="exportar"
           class="btn btn-outline-secondary d-flex align-items-center" style="height: 38px"> <i
                    class="bi bi-cloud-arrow-down fs-6"></i></a></div>
    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th width="10px" scope="col"></th>
                <th scope="col">Partida</th>
                <th scope="col">Concepto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Empleados</th>
                <th scope="col">Vencimiento</th>
                <th scope="col">Prox. Intervención</th>
                <th scope="col">Ppto</th>
                <th scope="col">Registro</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c3 = $mysqli->query("SELECT 
                    ps.id as id,
                    ps.concepto as concepto,
                    ps.descripcion as descripcion,
                    ps.id_unidad as id_unidad,
                    ps.cantidad as cantidad,
                    ps.precio as precio,
                    ps.descuento as descuento,
                    ps.id_iva as id_iva,
                    ps.subtotal as subtotal,
                    ps.total as total,
                    ps.fecha_vencimiento as fecha_vencimiento,
                    ps.fecha_prox_intervencion as fecha_prox_intervencion,
                    ps.is_checked as is_checked,
                    pp.cantidad as cantidadPartida,
                    p.id as id_presupuesto,
                    pp.id as id_presupuestos_partidas,
                    pp.partida as partida
                    FROM presupuestos_subpartidas ps 
                    INNER JOIN presupuestos_partidas pp on pp.id=ps.id_presupuesto_partidas 
                    INNER JOIN presupuestos p on p.id=pp.id_presupuesto 
                    where p.id_obra='$id_obra' and ps.id_categoria=1
                    order by  ps.is_checked desc, ps.fecha_vencimiento");
            while ($row = $c3->fetch_assoc()) {
                $id_presupuesto = $row['id_presupuesto'];
                $id_presupuestos_subpartidas = $row['id'];
                $id_presupuestos_partidas = $row['id_presupuestos_partidas'];
                $concepto_subpartida = $row['concepto'];
                $descripcion_subpartida = $row['descripcion'];
                $id_unidad_subpartida = $row['id_unidad'];
                $cantidad_subpartida = $row['cantidad'];
                $cantidad_partida = $row['cantidadPartida'];
                $precio_subpartida = $row['precio'];
                $descuento_subpartida = $row['descuento'];
                $id_iva_subpartida = $row['id_iva'];
                $subtotal_subpartida = $row['subtotal'];
                $total_subpartida = $row['total'];
                $fecha_vencimiento_subpartida = $row['fecha_vencimiento'];
                $fecha_prox_intervencion = $row['fecha_prox_intervencion'];
                $is_checked = $row['is_checked'];
                $partida = $row['partida'];

                $cantidad_presupuesto = round($cantidad_partida * $cantidad_subpartida, 2);

                // solo si es horas (h)
                if ($id_unidad_subpartida == 6) {
                    // para hacer la equivalencia por ejemplo de 2.50 a 2:30h
                    $horas = "$cantidad_presupuesto";
                    $horas = str_replace(".", ",", $horas);
                    list($horas, $minutos) = explode(",", $horas);
                    $minutos = round($minutos * 0.6);
                }


                if ($id_unidad_subpartida) {
                    $c5 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad_subpartida");
                    while ($row = $c5->fetch_assoc()) {
                        $simbolo_ud_material = $row['simbolo'];
                    }
                }

                $horas_registradas = "";
                $total_horasRegistradas = 0;
                $c6 = $mysqli->query("SELECT * FROM obras_registro_partes where id_presupuestos_subpartidas='$id_presupuestos_subpartidas' and id_obra=$id_obra");
                while ($row = $c6->fetch_assoc()) {
                    $horas_registradas = $row['horas'];
                    $horas_registradas = $dateClass->getSecondsFromFormatedHour("$horas_registradas");
                    $total_horasRegistradas += $horas_registradas;
                }
                if ($total_horasRegistradas) {
                    // $total_horasRegistradas = gmdate("H:i", "$total_horasRegistradas");

                    $horas23 = floor($total_horasRegistradas / 3600); // Calcula las horas completas
                    $minutos23 = floor(($total_horasRegistradas % 3600) / 60); // Calcula los minutos restantes

                    $total_horasRegistradas = sprintf("%02d:%02d", $horas23, $minutos23);
                }

                include "../components/modals/obraAssignEmpleadoToTask2.php";
                include "../components/obras/tareaLine3.php";
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
<?php include "../components/modals/obraNew.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>

<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>

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
            pageLength: 50,
            pageLength: 50
        });

        // Refilter the table
        $('#min, #max').on('change', function () {
            table.draw();
        });
    });
    $.fn.dataTable.ext.errMode = 'none'; // Desactiva alertas de error de DataTables
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
            data.push(['-', row[1], row[2], row[3], '-', '-', '-', row[7], row[8]]); // Agregar fila de datos con el valor de estado procesado
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "tareas.xlsx");
    });


    const updateGenerico = (tabla, columna, fila, valor) => {
        $.ajax({
            method: "POST",
            url: "../backend/updateGenerico.php",
            data: {
                tabla: tabla,
                columna: columna,
                fila: fila,
                valor: valor
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
            showMessage();
        });
    }
</script>
</html>