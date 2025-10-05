<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";


?>

<html lang="es">
<head>
    <title>Gastos | AptaSoft</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>
<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <p class="text-black fw-bold fs-3">Gastos</p>
    <div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gastoNew">Nuevo gasto<i
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
        <table id="tabla_proyectos" class="table tableComon table-hover">
            <thead>
            <tr>
                <th scope="col">Empresa</th>
                <th width="200px" scope="col">Fecha</th>
                <th width="200px" scope="col">Vence</th>
                <th scope="col">Código</th>
                <th scope="col">Proveedor</th>
                <th scope="col">Obra</th>
                <th scope="col">Tipo</th>
                <th scope="col">Subtotal</th>
                <th scope="col">Total</th>
                <th scope="col">Estado</th>
                <th width="10px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT * FROM gastos where is_active=1");
            while ($row = $c0->fetch_assoc()) {
                $id_gasto = $row['id'];
                $id_contacto = $row['id_contacto'];
                $id_categoria_gasto = $row['id_categoria_gasto'];
                $id_cuenta = $row['id_cuenta'];
                $id_obra = $row['id_obra'];
                $id_estado = $row['id_estado'];
                $id_empresa = $row['id_empresa'];
                $codigo = $row['codigo'];
                $fecha_inicio = $row['fecha_inicio'];
                $fecha_vencimiento = $row['fecha_vencimiento'];
                $comentario = $row['comentario'];
                $creation_date = $row['creation_date'];

                if ($id_categoria_gasto) {
                    $c1_gastos_categorias = $mysqli->query("SELECT * FROM gastos_categorias where id=$id_categoria_gasto");
                    while ($row = $c1_gastos_categorias->fetch_assoc()) {
                        $categoria_gasto = $row['categoria'];
                    }
                }

                $c1222 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
                while ($row = $c1222->fetch_assoc()) {
                    $empresa = $row['empresa'];
                }

                // subtotal gastos
                $q = mysqli_query($mysqli, "SELECT SUM(subtotal) AS x FROM gastos_lineas where id_gasto='$id_gasto'");
                $result = mysqli_fetch_assoc($q);
                $subtotal = round($result['x'], 2);

                // total gastos
                $q = mysqli_query($mysqli, "SELECT SUM(total) AS x FROM gastos_lineas where id_gasto='$id_gasto'");
                $result = mysqli_fetch_assoc($q);
                $total = round($result['x'], 2);

                if ($id_contacto) {
                    $c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
                    while ($row = $c1->fetch_assoc()) {
                        $nombre_contacto = $row['nombre'];
                    }
                }

                $c2 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
                while ($row = $c2->fetch_assoc()) {
                    $titulo_obra = $row['titulo'];
                }

                if ($id_estado) {
                    $c3 = $mysqli->query("SELECT * FROM gastos_estados where id=$id_estado");
                    while ($row = $c3->fetch_assoc()) {
                        $estado = $row['estado'];
                    }
                }

                $classEtiq = "text-bg-warning";
                if ($id_estado == 2) {
                    $classEtiq = "text-bg-primary";
                }

                include "../components/gastos/gastoLine.php";
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
<?php include "../components/modals/gastoNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    const filterHandler = () => {
        var id_empresa = $("input[name='btnradio']:checked").val();
        var id_estado = $('#id_estado').val();
        var id_contacto = $('#id_contacto').val();
        var fecha_inicio = $('#fecha_inicio').val();
        var fecha_fin = $('#fecha_fin').val();

        location.href = "gastos.php?id_empresa=" + id_empresa + "&id_estado=" + id_estado + "&id_contacto=" + id_contacto + "&fecha_inicio=" + fecha_inicio + "&fecha_fin=" + fecha_fin;
    }

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

            var estadoHtml = row[9]; // Obtener el HTML de la columna de estado
            var estado = estadoHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas

            var col7 = row[7].replace('€', '').replace('.', ',');
            var col8 = row[8].replace('€', '').replace('.', ',');

            data.push([row[0], row[1], row[2], row[3], row[4], obra, row[6], col7, col8, estado]);
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "gastos.xlsx");
    });



</script>
</html>