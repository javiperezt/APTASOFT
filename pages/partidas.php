<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";

// Query para la paginación
$query = "SELECT * FROM gestor_partidas where is_active=1";
include "../components/pagination/paginationQuery.php";

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
        <a href="presupuestos.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Gestor de partidas</p>
    </div>
    <div>
        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#partidaNew">+ Nueva
            partida</a>
    </div>
</div>
<!----------- END HEAD PAGE ----------->




<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5">Partidas</p>
    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Partida</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Unidad</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT * FROM gestor_partidas where is_active=1 order by partida LIMIT $offset, $no_of_records_per_page");
            while ($row = $c0->fetch_assoc()) {
                $id_partida = $row['id'];
                $codigo = $row['codigo'];
                $partida = $row['partida'];
                $id_unidad = $row['id_unidad'];
                $descripcion = $row['descripcion'];
                $subtotal = $row['subtotal'];
                $total = $row['total'];

                if($id_unidad) {
                    $c1 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
                    while ($row = $c1->fetch_assoc()) {
                        $simbolo = $row['simbolo'];
                        $unidad = $row['unidad'];
                    }
                }

                include "../components/gestorPartidas/partidaLine.php";
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
<?php include "../components/modals/partidaNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
</html>