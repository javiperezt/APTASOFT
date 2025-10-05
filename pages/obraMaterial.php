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
        <p class="text-black fw-bold fs-4 m-0">Material de obra</p>
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
                <th scope="col">Capítulo</th>
                <th scope="col">Partida</th>
                <th scope="col">Concepto</th>
                <th scope="col">Cant</th>
                <th scope="col">Ud</th>
                <th scope="col">€/Und</th>
                <th scope="col">Ppto</th>
                <th width="100px" scope="col">Fecha</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c1 = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
            while ($row = $c1->fetch_assoc()) {
                $id_presupuesto = $row['id'];

                $c2 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
                while ($row = $c2->fetch_assoc()) {
                    $id_presupuestos_partidas = $row['id'];
                    $id_capitulo = $row['id_capitulo'];
                    $partida = $row['partida'];
                    $cantidad_partida = $row['cantidad'];

                    $c4 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
                    while ($row = $c4->fetch_assoc()) {
                        $capitulo = $row['capitulo'];
                    }

                    $c3 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=2 order by fecha_vencimiento");
                    while ($row = $c3->fetch_assoc()) {
                        $id_presupuestos_subpartidas = $row['id'];
                        $concepto_material = $row['concepto'];
                        $descripcion_material = $row['descripcion'];
                        $fecha_vencimiento = $row['fecha_vencimiento'];
                        $is_checked = $row['is_checked'];
                        $cantidad_material = $row['cantidad'];
                        $precio_material = $row['precio'];
                        $descuento_material = $row['descuento'];
                        $id_unidad_material = $row['id_unidad'];
                        $is_checked = $row['is_checked'];

                        if ($id_unidad_material) {
                            $c5 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad_material");
                            while ($row = $c5->fetch_assoc()) {
                                $simbolo_ud_material = $row['simbolo'];
                            }
                        }

                        $importe = round($cantidad_material * $precio_material - ($cantidad_material * $precio_material * $descuento_material / 100), 2);
                        $cantidadTotal = $importe * $cantidad_partida;


                        include "../components/obras/materialObraLine.php";
                    }
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
            data.push(['-', row[1], row[2], row[3], row[4], row[5], row[6], row[7]]); // Agregar fila de datos con el valor de estado procesado
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "materiales.xlsx");
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