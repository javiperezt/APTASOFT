<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";

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
    <p class="text-black fw-bold fs-3">Caja</p>

    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboard.php">Resultados</a>
            </li>
            <!--<li class="nav-item">
                <a class="nav-link text-black" href="dashboardGeneral.php">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="dashboardObras.php">Obras</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link active" href="dashboardCaja.php">Caja</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------------- CONT 1 ----------------->
<div class="container">
    <div class="row">
        <!----------------- GASTOS ----------------->
        <div class="col-12 ps-0">
            <div class="bg-white my-3 rounded-3 p-4 ">
                <?php
                $id_gasto = "";
                $subtotal = 0;
                $TOTALGASTOS = 0;
                $cXX = $mysqli->query("SELECT * FROM gastos where is_active=1");
                while ($row = $cXX->fetch_assoc()) {
                    $id_gasto = $row['id'];

                    if ($id_gasto) {
                        $c1X = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto=$id_gasto");
                        while ($row = $c1X->fetch_assoc()) {
                            $subtotal = $row['subtotal'];
                            $TOTALGASTOS += $subtotal;
                        }
                    }
                }
                ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-bold fs-4">Cobros pendientes</p>
                    <!--<p class="btn btn-danger"><?= $TOTALGASTOS; ?>€</p>-->

                </div>
                <div class="d-flex align-items-center g-2 justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <p>Filtro por fechas:</p>
                        <input style="width: 200px" type="text" id="min2" name="min"
                               class="form-control form-control-sm"
                               placeholder="Desde">
                        <input style="width: 200px" type="text" id="max2" name="max"
                               class="form-control form-control-sm"
                               placeholder="Hasta">
                    </div>
                    <div>
                        <a id="exportar2"
                           class="btn btn-outline-secondary d-flex align-items-center" style="height: 38px"> <i
                                    class="bi bi-cloud-arrow-down fs-6"></i></a></div>
                </div>
                <div>
                    <table id="tabla_proyectos2" class="table tableComon">
                        <thead>
                        <tr>
                            <th scope="col">Empresa</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Factura</th>
                            <th scope="col">Obra</th>
                            <th scope="col">Proveedor</th>
                            <th scope="col">Importe total</th>
                            <th scope="col">Por cobrar</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $c0 = $mysqli->query("SELECT * FROM facturas where is_active=1");
                        while ($row = $c0->fetch_assoc()) {
                            $id_factura = $row['id'];
                            $id_obra = $row['id_obra'];
                            $id_contacto = $row['id_contacto'];
                            $id_empresa = $row['id_empresa'];
                            $fecha_inicio = $row['fecha_inicio'];
                            $pref_ref = $row['pref_ref'];
                            $pref_ref_year = $row['pref_ref_year'];
                            $ref = $row['ref'];
                            $ref = $pref_ref . $pref_ref_year . $ref;
                            $total_factura = 0;

                            // Calcular el total de la factura
                            $c1 = $mysqli->query("SELECT SUM(total) as total_factura FROM facturas_partidas WHERE id_factura = $id_factura");
                            if ($total = $c1->fetch_assoc()) {
                                $total_factura = $total['total_factura'];
                            }

                            $c1222 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
                            while ($row = $c1222->fetch_assoc()) {
                                $empresa = $row['empresa'];
                            }

                            $c12221 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
                            while ($row = $c12221->fetch_assoc()) {
                                $proveedor = $row['nombre'];
                            }

                            // Calcular el total de los pagos realizados para la factura
                            $c2 = $mysqli->query("SELECT SUM(importe) as total_pagos FROM facturas_pagos WHERE id_factura = $id_factura and estado='pagado'");
                            if ($pagos = $c2->fetch_assoc()) {
                                $total_pagos = $pagos['total_pagos'];
                            }

                            if ($id_obra) {
                                $c34 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
                                while ($row = $c34->fetch_assoc()) {
                                    $titulo_obra = $row['titulo'];
                                }
                            }

                            // Si el total de los pagos es menor al total de la factura, la factura tiene pagos pendientes de cobrar
                            if ($total_pagos < $total_factura) {
                                $importe_pendiente = $total_factura - $total_pagos;
                                ?>
                                <tr>
                                    <td class="text-uppercase" scope="col"><?= $empresa; ?></td>
                                    <td scope="col"><?= $fecha_inicio; ?></td>
                                    <td scope="col"><a class="link-primary"
                                                       href="facturaDetail.php?id_factura=<?= $id_factura; ?>"><?= $ref; ?></a>
                                    </td>
                                    <td scope="col"><a class="link-primary"
                                                       href="obraDetail.php?id_obra=<?= $id_obra; ?>"><?= $titulo_obra; ?></a>
                                    </td>
                                    <td><?= $proveedor; ?></td>
                                    <td scope="col"><?= round($total_factura, 2); ?>€</td>
                                    <td scope="col"><?= round($importe_pendiente, 2); ?>€</td>
                                </tr>
                                <?php
                            }
                        }
                        if ($c0->num_rows == 0) {
                            include "../components/noDataLine.php";
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">TOTALES:</th>
                            <th scope="col">Importe total</th>
                            <th scope="col">Por cobrar</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
        <!----------------- END GASTOS ----------------->


        <!----------------- INGRESOS ----------------->
        <div class="col-12 ps-0">
            <div class="bg-white my-3 rounded-3 p-4">
                <?php
                $id_factura = "";
                $subtotal = 0;
                $TOTALINGRESOS = 0;
                $cXXX = $mysqli->query("SELECT * FROM facturas where is_active=1 $filter");
                while ($row = $cXXX->fetch_assoc()) {
                    $id_factura = $row['id'];

                    if ($id_factura) {
                        $c2X = $mysqli->query("SELECT * FROM facturas_partidas where id_factura=$id_factura");
                        while ($row = $c2X->fetch_assoc()) {
                            $subtotal = $row['subtotal'];
                            $TOTALINGRESOS += $subtotal;
                        }
                    }
                }
                ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-bold fs-4">Pagos pendientes</p>
                    <!--<p class="btn btn-success"><?= $TOTALINGRESOS; ?>€</p>-->
                </div>
                <div class="d-flex align-items-center g-2 justify-content-between mb-3">
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
                <div>
                    <table id="tabla_proyectos" class="table tableComon">
                        <thead>
                        <tr>
                            <th scope="col">Empresa</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Factura</th>
                            <th scope="col">Obra</th>
                            <th scope="col">Proveedor</th>
                            <th scope="col">Retencion</th>
                            <th scope="col">Importe total</th>
                            <th scope="col">Por Pagar</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $c011 = $mysqli->query("SELECT * FROM gastos where is_active=1");
                        while ($row = $c011->fetch_assoc()) {
                            $id_gasto = $row['id'];
                            $id_empresa = $row['id_empresa'];
                            $id_contacto = $row['id_contacto'];
                            $id_obra = $row['id_obra'];
                            $retencion = $row['retencion'];
                            $fecha_inicio = $row['fecha_inicio'];
                            $codigo = $row['codigo'];
                            $total_gasto = 0;

                            // Calcular el total de la factura
                            $c1 = $mysqli->query("SELECT SUM(total) as total_factura FROM gastos_lineas WHERE id_gasto = $id_gasto");
                            if ($total = $c1->fetch_assoc()) {
                                $total_gasto = $total['total_factura'];
                            }

                            $getSubtotalGastoGeneral = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalGastoGeneral FROM gastos_lineas where id_gasto='$id_gasto'");
                            $result = mysqli_fetch_assoc($getSubtotalGastoGeneral);
                            $subtotalGastoGeneral = $result['subtotalGastoGeneral'];

                            $c1222 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
                            while ($row = $c1222->fetch_assoc()) {
                                $empresa = $row['empresa'];
                            }

                            $c12221 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
                            while ($row = $c12221->fetch_assoc()) {
                                $proveedor = $row['nombre'];
                            }

                            // Calcular el total de los pagos realizados para la factura
                            $c2 = $mysqli->query("SELECT SUM(importe) as total_pagos FROM gastos_pagos WHERE id_gasto = $id_gasto and estado='pagado'");
                            if ($pagos = $c2->fetch_assoc()) {
                                $total_pagos = $pagos['total_pagos'];
                            }

                            if ($id_obra) {
                                $c34 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
                                while ($row = $c34->fetch_assoc()) {
                                    $titulo_obra = $row['titulo'];
                                }
                            }

                            // Si el total de los pagos es menor al total de la factura, la factura tiene pagos pendientes de cobrar
                            if ($total_pagos < $total_gasto) {
                                $importe_pendiente = $total_gasto - $total_pagos;
                                ?>
                                <tr>
                                    <td class="text-uppercase" scope="col"><?= $empresa; ?></td>
                                    <td scope="col"><?= $fecha_inicio; ?></td>
                                    <td scope="col"><a class="link-primary"
                                                       href="gastoDetail.php?id_gasto=<?= $id_gasto; ?>"><?= $codigo; ?></a>
                                    </td>
                                    <td scope="col"><a class="link-primary"
                                                       href="obraDetail.php?id_obra=<?= $id_obra; ?>"><?= $titulo_obra; ?></a>
                                    </td>
                                    <td><?= $proveedor; ?></td>
                                    <?php $retencionValue= round($subtotalGastoGeneral * $retencion / 100, 2) ?>
                                    <td scope="col"><?= $retencionValue; ?>€</td>
                                    <td scope="col"><?= round($total_gasto-$retencionValue, 2); ?>€</td>
                                    <td scope="col"><?= round($importe_pendiente, 2); ?>€</td>
                                </tr>
                                <?php
                            }
                        }
                        if ($c011->num_rows == 0) {
                            include "../components/noDataLine.php";
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">TOTALES:</th>
                            <th scope="col">Importe total</th>
                            <th scope="col">Por cobrar</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!----------------- END INGRESOS ----------------->
    </div>
    <!----------------- END CONT 1 ----------------->


</div>
</body>

<script>
    const filterHandler = () => {
        var desde = $("#desde").val();
        var id_empresa = $("input[name='btnradio']:checked").val();
        var hasta = $("#hasta").val();

        location.href = "dashboardCaja.php?desde=" + desde + "&hasta=" + hasta + "&id_empresa=" + id_empresa;
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
            pageLength: 50,
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Obtener la columna numérica de Importe total (suponiendo que es la 5ª columna, con índice 4).
                // Si fuera otra columna, modifica el índice adecuadamente.
                var total = api.column(6, {search: 'applied'}).data().reduce(function (a, b) {
                    var number = Number(b.replace('€', '').replace(',', '.'));
                    return a + number;
                }, 0);

                var total2 = api.column(7, {search: 'applied'}).data().reduce(function (a, b) {
                    var number = Number(b.replace('€', '').replace(',', '.'));
                    return a + number;
                }, 0);

                // Actualizar el pie de página.
                $(api.column(6).footer()).html(total.toFixed(2) + '€');
                $(api.column(7).footer()).html(total2.toFixed(2) + '€');
            }
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
            var obraHtml = row[2]; // Obtener el HTML de la columna de estado
            var obra = obraHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas

            var estadoHtml = row[3]; // Obtener el HTML de la columna de estado
            var estado = estadoHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas

            var col4 = row[6].replace('€', '').replace('.', ',');
            var col5 = row[7].replace('€', '').replace('.', ',');

            data.push([row[0], row[1], obra, estado, row[4], col4, col5]); // Agregar fila de datos con el valor de estado procesado
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "pagos_pendientes.xlsx");
    });


    var minDate2, maxDate2;

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = minDate2.val();
            var max = maxDate2.val();
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
        minDate2 = new DateTime($('#min2'), {
            format: 'YYYY/MM/DD'
        });
        maxDate2 = new DateTime($('#max2'), {
            format: 'YYYY/MM/DD'
        });

        // DataTables initialisation
        var table = $('#tabla_proyectos2').DataTable({
            language: {
                url: '../espanol.json'
            },
            pageLength: 50,
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Obtener la columna numérica de Importe total (suponiendo que es la 5ª columna, con índice 4).
                // Si fuera otra columna, modifica el índice adecuadamente.
                var total = api.column(5, {search: 'applied'}).data().reduce(function (a, b) {
                    var number = Number(b.replace('€', '').replace(',', '.'));
                    return a + number;
                }, 0);

                var total2 = api.column(6, {search: 'applied'}).data().reduce(function (a, b) {
                    var number = Number(b.replace('€', '').replace(',', '.'));
                    return a + number;
                }, 0);

                // Actualizar el pie de página.
                $(api.column(5).footer()).html(total.toFixed(2) + '€');
                $(api.column(6).footer()).html(total2.toFixed(2) + '€');
            }
        });

        // Refilter the table
        $('#min2, #max2').on('change', function () {
            table.draw();
        });
    });

    $.fn.dataTable.ext.errMode = 'none'; // Desactiva alertas de error de DataTables
    // Agregar un controlador de eventos de clic al botón "Exportar"
    $('#exportar2').on('click', function () {
        // Obtener la tabla de DataTables
        var table = $('#tabla_proyectos2').DataTable();

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
            var obraHtml = row[2]; // Obtener el HTML de la columna de estado
            var obra = obraHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas

            var estadoHtml = row[3]; // Obtener el HTML de la columna de estado
            var estado = estadoHtml.replace(/<(?:.|\n)*?>/gm, ''); // Eliminar cualquier HTML o etiquetas

            var col4 = row[5].replace('€', '').replace('.', ',');
            var col5 = row[6].replace('€', '').replace('.', ',');

            data.push([row[0], row[1], obra, estado, row[4], col4, col5]); // Agregar fila de datos con el valor de estado procesado
        });

        // Crear un libro de trabajo de Excel
        var wb = XLSX.utils.book_new();

        // Crear una hoja de trabajo y agregar los datos
        var ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Datos");

        // Descargar el archivo de Excel
        XLSX.writeFile(wb, "cobros_pendientes.xlsx");
    });

</script>
</html>