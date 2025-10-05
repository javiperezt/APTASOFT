<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_empleado = filter_input(INPUT_GET, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM empleados where id=$id_empleado");
while ($row = $c0->fetch_assoc()) {
    $id_rol = $row['id_rol'];
    $id_proveedor = $row['id_proveedor'];
    $id_empresa = $row['id_empresa'];
    $nombre = $row['nombre'];
    $correo = $row['correo'];
    $password = $row['password'];
    $tel = $row['tel'];
    $movil = $row['movil'];
    $nif = $row['nif'];
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
        <a href="empleados.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Editar empleado</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="empleadoDetail.php?id_empleado=<?= $id_empleado; ?>">Ficha</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="empleadoJornadas.php?id_empleado=<?= $id_empleado; ?>">Jornadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="empleadoRegistroPartes.php?id_empleado=<?= $id_empleado; ?>">Registros
                    partes</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- CONTAINER ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-5">
            <div class="form-floating">
                <input onfocusout="updateGenerico('empleados','nombre',<?= $id_empleado; ?>,this.value)" type="text"
                       class="form-control" placeholder="Nombre y apellidos" value="<?= $nombre; ?>">
                <label>Nombre y apellidos</label>
            </div>
        </div>
        <div class="col-4">
            <div class="form-floating">
                <input onfocusout="updateGenerico('empleados','correo',<?= $id_empleado; ?>,this.value)" type="email"
                       class="form-control" placeholder="Correo electrónico" value="<?= $correo; ?>">
                <label>Correo electrónico</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('empleados','nif',<?= $id_empleado; ?>,this.value)" type="text"
                       class="form-control" placeholder="NIF" value="<?= $nif; ?>">
                <label>NIF</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('empleados','tel',<?= $id_empleado; ?>,this.value)" type="tel"
                       class="form-control" placeholder="Teléfono" value="<?= $tel; ?>">
                <label>Teléfono</label>
            </div>
        </div>
        <div class="col-5">
            <form action="../backend/empleados/empleadoChangePassw.php" method="post">
                <div class="input-group mb-3">
                    <div class="form-floating">
                        <input type="hidden" value="<?= $id_empleado; ?>" name="id_empleado">
                        <input required type="password" name="password" class="form-control" placeholder="Contraseña">
                        <label>Contraseña</label>
                    </div>
                    <span class="input-group-text p-0 bg-white"><button type="submit"
                                                                        class="btn btn-outline-secondary h-100"
                                                                        style="border-radius: 0  5px 5px 0;">Cambiar contraseña</button></span>
                </div>
            </form>
        </div>
    </div>
</div>
<!----------- END CONTAINER ----------->


<div class="container text-end">
    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#empleadoDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>

</body>
<?php include "../components/modals/empleadoDelete.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>
<script src="../js/updateGenerico.js"></script>
<script>
    const empleadoDelete = (id_empleado) => {
        $.ajax({
            method: "POST",
            url: "../backend/empleados/empleadoDelete.php",
            data: {
                id_empleado: id_empleado
            }
        }).done(function () {
            location.href = "empleados.php";
        });
    }
</script>
</html>