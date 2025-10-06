<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
require_once "../ConsecutiveNumbers.php";

//genera numero automatico en presupuesto
$consecutiveNumber = new ConsecutiveNumbers();

include "../backend/presupuestos/presupuestoGetConsecutiveNumber.php";


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
    <p class="text-black fw-bold fs-3">Presupuestos</p>
    <div>
        <a href="partidas.php" class="btn btn-outline-primary btn-sm">Partidas<i class="bi bi-sliders2 ms-2"></i></a>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#presupuestoNew">Nuevo presupuesto<i
                    class="bi bi-file-earmark-text-fill ms-2"></i></button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">

    <div class="d-flex align-items-center g-2 justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <p>Filtro por fechas:</p>
            <input style="width: 200px" type="text" id="min" name="min" class="form-control form-control-sm"
                   placeholder="Desde">
            <input style="width: 200px" type="text" id="max" name="max" class="form-control form-control-sm"
                   placeholder="Hasta">
        </div>
        <div>
            <a id="exportar"
               class="btn btn-outline-secondary d-flex align-items-center" style="height: 38px"> <i
                        class="bi bi-cloud-arrow-down fs-6"></i></a></div>
    </div>

    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Empresa</th>
                <th scope="col">Fecha</th>
                <th scope="col">Vence</th>
                <th scope="col">Referencia</th>
                <th scope="col">Cliente</th>
                <th scope="col">Obra</th>
                <th scope="col">Subtotal</th>
                <th scope="col">Total</th>
                <th scope="col">Estado</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT * FROM presupuestos where is_active=1");
            while ($row = $c0->fetch_assoc()) {
                $id_presupuesto = $row['id'];
                $id_contacto = $row['id_contacto'];
                $id_obra = $row['id_obra'];
                $id_estado = $row['id_estado'];
                $pref_ref = $row['pref_ref'];
                $id_empresa = $row['id_empresa'];
                $pref_ref_year = $row['pref_ref_year'];
                $ref = $row['ref'];
                $ref = $pref_ref . $pref_ref_year . $ref;
                $fecha_inicio = $row['fecha_inicio'];
                $fecha_vencimiento = $row['fecha_vencimiento'];

                // subtotal presupuesto
                $q = mysqli_query($mysqli, "SELECT SUM(cantidad*subtotal) AS x FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
                $result = mysqli_fetch_assoc($q);
                $subtotal = round($result['x'] ?? 0, 2);

                // total presupuesto
                $q = mysqli_query($mysqli, "SELECT SUM(cantidad*total) AS x FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
                $result = mysqli_fetch_assoc($q);
                $total = round($result['x'] ?? 0, 2);

                $c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
                while ($row = $c1->fetch_assoc()) {
                    $nombre_contacto = $row['nombre'];
                }

                $c1222 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
                while ($row = $c1222->fetch_assoc()) {
                    $empresa = $row['empresa'];
                }

                $c2 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
                while ($row = $c2->fetch_assoc()) {
                    $titulo_obra = $row['titulo'];
                }

                $estado = "";
                if ($id_estado) {
                    $c3 = $mysqli->query("SELECT * FROM presupuestos_estados where id=$id_estado");
                    while ($row = $c3->fetch_assoc()) {
                        $estado = $row['estado'];
                    }
                }

                $classEtiq = "text-bg-warning";
                if ($id_estado == 1) {
                    $classEtiq = "text-bg-warning";
                }
                if ($id_estado == 2) {
                    $classEtiq = "text-bg-primary";
                }
                if ($id_estado == 3) {
                    $classEtiq = "text-bg-danger";
                }
                include "../components/presupuestos/presupuestoLine.php";
            }
            if ($c0->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->


</div>


</body>
<?php include "../components/modals/presupuestoNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    // Evitamos que puedan usar el espacio en el input REF del presupuesto
    $('#ref').keypress(function (e) {
        if (e.which === 32)
            return false;
    });

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
            var obraHtml = row[5]; // Obtener el HTML de la columna de estado
            var obra = obraHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas

            var estadoHtml = row[8]; // Obtener el HTML de la columna de estado
            var estado = estadoHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas
            data.push([row[0], row[1], row[2], row[3], row[4], obra, row[6], row[7], estado]); // Agregar fila de datos con el valor de estado procesado
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "presupuesto.xlsx");
    });
</script>
</html>