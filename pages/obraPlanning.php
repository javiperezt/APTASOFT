<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

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
        <p class="text-black fw-bold fs-4 m-0">Planning obra</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <?php include "../components/obras/navObra.php"; ?>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">

    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Presupuesto</th>
                <th width="20%" scope="col">Capítulo</th>
                <th scope="col">Partida</th>
                <th scope="col">Inicio</th>
                <th scope="col">Cierre</th>
                <th width="170px" scope="col">Progreso</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c1 = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
            while ($row = $c1->fetch_assoc()) {
                $id_presupuesto = $row['id'];
                $pref_ref = $row['pref_ref'];
                $pref_ref_year = $row['pref_ref_year'];
                $ref = $row['ref'];
                $ref = $pref_ref . $pref_ref_year . $ref;

                $c22 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
                while ($row = $c22->fetch_assoc()) {
                    $id_presupuestos_partidas = $row['id'];
                    $id_capitulo = $row['id_capitulo'];
                    $partida = $row['partida'];
                    $descripcion = $row['descripcion'];
                    $fecha_inicio = $row['fecha_inicio'];
                    $fecha_vencimiento = $row['fecha_vencimiento'];
                    $cantidad_partida = $row['cantidad'];

                    $c4 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                    while ($row = $c4->fetch_assoc()) {
                        $capitulo = $row['capitulo'];
                    }

                    // para calcular el porcentaje
                    $countSubpartidas = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria!=2");
                    $countSubpartidas = $countSubpartidas->num_rows;

                    // contamos los checks en mano de obra (excluimos material (2))
                    $countSubpartidasChecked = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and is_checked=1 and id_categoria!=2");
                    $countSubpartidasChecked = $countSubpartidasChecked->num_rows;

                    if($countSubpartidas == 0) {
                        $porcentaje = 0; // O puedes establecer cualquier otro valor que desees para esta condición
                    } else {
                        $porcentaje = round($countSubpartidasChecked / $countSubpartidas * 100, 2);
                    }

                    include "../components/obras/obraPlanningLine.php";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->

</div>


</body>
<?php include "../components/modals/obraNew.php"; ?>
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

</script>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

</html>