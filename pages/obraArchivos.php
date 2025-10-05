<?php
session_start();

include "../conexion.php";
require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_directorio = filter_input(INPUT_GET, 'id_directorio', FILTER_SANITIZE_SPECIAL_CHARS);

$filter="";
if($id_directorio){
    $filter="AND id_directorio=$id_directorio";
}

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
        <p class="text-black fw-bold fs-4 m-0">Archivos obra</p>
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
        <div>
            <div class="form-floating">
                <select onchange="filterHandler()" name="id_directorio" id="id_directorio" required class="form-select">
                    <option selected disabled hidden>Carpeta</option>
                    <?php
                    $getCarpet = $mysqli->query("SELECT * FROM obras_directorios");
                    while ($row = $getCarpet->fetch_assoc()) {
                        $s_id_directorio = $row['id'];
                        $s_directorio = $row['directorio'];

                        $selected="";
                        if($id_directorio==$s_id_directorio){
                            $selected="selected";
                        }
                        echo "<option $selected value='$s_id_directorio'>$s_directorio</option>";
                    }
                    ?>
                </select>
                <label>Carpeta</label>
            </div>
        </div>
        <div class="ms-auto">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#obraAddDocument">+
                Añadir archivo
            </button>
        </div>
    </div>
    <!----------- END FILTROS ----------->


    <!----------- CONTENT ----------->
    <div id="docs" class="d-flex flex-column gap-2 mt-3">
        <?php
        $c1 = $mysqli->query("SELECT * FROM obras_archivos where id_obra=$id_obra $filter order by titulo");
        while ($row = $c1->fetch_assoc()) {
            $id_archivo = $row['id'];
            $id_directorio = $row['id_directorio'];
            $titulo = $row['titulo'];
            $src = $row['src'];
            $id_empleado = $row['id_empleado'];
            $creation_date = $row['creation_date'];

            $obras_directorios = $mysqli->query("SELECT * FROM obras_directorios where id='$id_directorio'");
            while ($row = $obras_directorios->fetch_assoc()) {
                $directorio = $row['directorio'];
            }

            include "../components/obras/docLine.php";
        }
        if($c1->num_rows==0){
            echo "<p class='fs-6 fw-light text-center'>NO HAY ARCHIVOS</p>";
        }
        ?>
    </div>
    <!----------- END CONTENT ----------->
</div>


</body>
<?php include "../components/modals/obraAddDocument.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    const obraDeleteDoc = (id_archivo) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/obraDeleteDoc.php",
            data: {
                id_archivo: id_archivo
            }
        }).done(function () {
            $("#docs").load(location.href + " #docs");
        });
    }

    const filterHandler = () => {
        var id_directorio = $("#id_directorio").val();

        location.href = "obraArchivos.php?id_obra=<?=$id_obra;?>" + "&id_directorio=" + id_directorio;
    }
</script>
</html>