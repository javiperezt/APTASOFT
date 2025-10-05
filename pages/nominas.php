<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";

// Query para la paginación
$query = "SELECT * FROM nominas where id>=0";
include "../components/pagination/paginationQuery.php";


$filteredQuery = "SELECT * FROM nominas LIMIT $offset, $no_of_records_per_page";

if (!$_GET['pageno']) {
    $filteredQuery = "SELECT * FROM nominas WHERE id>=0"; // inicializa la consulta con una condición siempre verdadera

    $filtered_get = array_filter($_GET); // elimina los valores vacíos de $_GET

    if (count($filtered_get)) { // si hay algún parámetro de búsqueda
        // añade las condiciones a la consulta
        foreach ($filtered_get as $key => $value) {
            if ($value != "NULL" && $value != "null" && $value != "Null" && isset($value) && $value != null) {
                $filteredQuery .= " AND $key = '$value'";
            }
        }
    }
    $filteredQuery .= " ORDER by fecha LIMIT $offset, $no_of_records_per_page";
    $f_id_empleado = $_GET['id_empleado'];
}
?>

<html lang="es">
<head>
    <title>Nominas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>
<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <p class="text-black fw-bold fs-3">Nóminas</p>
    <div>
        <button onclick="nominaCreate()" class="btn btn-sm btn-primary">Registrar nómina<i
                    class="bi bi-file-earmark-plus ms-2"></i></button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <!----------- FILTROS ----------->
    <div class="d-flex align-items-center">
        <div class="ms-auto align-items-center d-flex gap-2">
            <select style="min-width: 250px" class="selectizeSearch" id="id_empleado" onchange="filterHandler()">
                <option selected value="">Todos los empleados</option>
                <?php
                $s_contactos = $mysqli->query("SELECT * FROM empleados where is_active=1");
                while ($row = $s_contactos->fetch_assoc()) {
                    $s_id_empleado = $row['id'];
                    $s_nombre_empleado = $row['nombre'];
                    $selected = "";
                    if ($f_id_empleado == $s_id_empleado) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_empleado'>$s_nombre_empleado</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <!----------- END FILTROS ----------->


    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Concepto</th>
                <th scope="col">Empleado</th>
                <th scope="col">Total S.S.</th>
                <th scope="col">S.S.</th>
                <th scope="col">Retención</th>
                <th scope="col">A pagar</th>
                <th scope="col">Estado</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("$filteredQuery");
            while ($row = $c0->fetch_assoc()) {
                $id_nomina = $row['id'];
                $id_empleado = $row['id_empleado'];
                $estado = $row['estado'];
                $fecha = $row['fecha'];
                $comentario = $row['comentario'];
                $salario = $row['salario'];
                $total_ss = $row['total_ss'];
                $gastos_ss_empresa = $row['gastos_ss_empresa'];
                $irpf = $row['irpf'];

                if ($id_empleado) {
                    $c1 = $mysqli->query("SELECT * FROM empleados where id=$id_empleado");
                    while ($row = $c1->fetch_assoc()) {
                        $nombre_empleado = $row['nombre'];
                    }
                }

                $classEtiq = "text-bg-primary";
                if ($estado == "pendiente") {
                    $classEtiq = "text-bg-warning";
                }

                include "../components/nominas/nominasLine.php";
            }
            if ($c0->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->


    <!------------ PAGINACIÓN ---------->
    <?php include "../components/pagination/pagination.php"; ?>
    <!------------ END PAGINACIÓN ---------->
</div>


</body>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    const filterHandler = () => {
        var id_empleado = $('#id_empleado').val();

        location.href = "nominas.php?id_empleado=" + id_empleado;
    }

    const nominaCreate = () => {
        $.ajax({
            method: "POST",
            url: "../backend/nominas/nominaCreate.php",
            data: {}
        }).done(function (data) {
            location.href = "nominaDetail.php?id_nomina=" + data;
        });
    }
</script>
</html>