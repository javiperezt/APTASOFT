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
        <p class="text-black fw-bold fs-4 m-0">Notas Obra</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <?php include "../components/obras/navObra.php" ;?>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <!----------- FILTROS ----------->
    <div class="d-flex align-items-center">
        <div class="ms-auto">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#obraAddNota">+ Añadir
                nota
            </button>
        </div>
    </div>
    <!----------- END FILTROS ----------->


    <!----------- CONTENT ----------->
    <div id="notas" class="row gap-2 mt-3">
        <?php
        $c1 = $mysqli->query("SELECT * FROM obras_notas where id_obra=$id_obra");
        while ($row = $c1->fetch_assoc()) {
            $id_nota = $row['id'];
            $id_empleado = $row['id_empleado'];
            $id_obra = $row['id_obra'];
            $titulo = $row['titulo'];
            $comentario = $row['comentario'];
            $creation_date = $row['creation_date'];

            $empleados = $mysqli->query("SELECT * FROM empleados where id=$id_empleado");
            while ($row = $empleados->fetch_assoc()) {
                $nombre_empleado = $row['nombre'];
            }

            include "../components/obras/notaLine.php";
        }
        if($c1->num_rows==0){
            echo "<p class='fs-6 fw-light text-center'>NO HAY NOTAS CREADAS</p>";
        }
        ?>
    </div>
    <!----------- END CONTENT ----------->
</div>


</body>
<?php include "../components/modals/obraAddNota.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script>
    const obraDeleteNota = (id_nota) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/obraDeleteNota.php",
            data: {
                id_nota: id_nota
            }
        }).done(function () {
            $("#notas").load(location.href + " #notas");
        });
    }
</script>
</html>