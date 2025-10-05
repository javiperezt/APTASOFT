<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_contacto = filter_input(INPUT_GET, 'id_contacto', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
while ($row = $c0->fetch_assoc()) {
    $id_tipo_organizacion = $row['id_tipo_organizacion'];
    $id_tipo_contacto = $row['id_tipo_contacto'];
    $nombre = $row['nombre'];
    $correo = $row['correo'];
    $tel = $row['tel'];
    $movil = $row['movil'];
    $direccion = $row['direccion'];
    $poblacion = $row['poblacion'];
    $provincia = $row['provincia'];
    $pais = $row['pais'];
    $cp = $row['cp'];
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
<div class="container mt-3">
    <div class="d-flex gap-2 align-items-center">
        <a href="contactos.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Editar contacto</p>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- CONTAINER ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-4">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','nombre',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="Nombre" value="<?= $nombre; ?>">
                <label>Nombre</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','nif',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="NIF" value="<?= $nif; ?>">
                <label>NIF</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('contactos','id_tipo_organizacion',<?= $id_contacto; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Tipo de organización</option>
                    <?php
                    $c2 = $mysqli->query("SELECT * FROM contactos_tipos_organizacion");
                    while ($row = $c2->fetch_assoc()) {
                        $id_contactos_tipos_organizacion = $row['id'];
                        $tipo_contacto = $row['organizacion'];

                        $selected = "";
                        if ($id_tipo_organizacion == $id_contactos_tipos_organizacion) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_contactos_tipos_organizacion'>$tipo_contacto</option>";
                    }
                    ?>
                </select>
                <label>Tipo de empresa</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('contactos','id_tipo_contacto',<?= $id_contacto; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Tipo de contacto</option>
                    <?php
                    $c2 = $mysqli->query("SELECT * FROM contactos_tipos");
                    while ($row = $c2->fetch_assoc()) {
                        $id_contactos_tipos = $row['id'];
                        $tipo_contacto = $row['tipo_contacto'];

                        $selected = "";
                        if ($id_tipo_contacto == $id_contactos_tipos) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_contactos_tipos'>$tipo_contacto</option>";
                    }
                    ?>
                </select>
                <label>Tipo de contacto</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','direccion',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="Dirección" value="<?= $direccion; ?>">
                <label>Dirección</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','poblacion',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="Población" value="<?= $poblacion; ?>">
                <label>Población</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','cp',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="Código Posta" value="<?= $cp; ?>">
                <label>Código Posta</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','provincia',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="Provincia" value="<?= $provincia; ?>">
                <label>Provincia</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','pais',<?= $id_contacto; ?>,this.value)" type="text"
                       class="form-control" placeholder="País" value="<?= $pais; ?>">
                <label>País</label>
            </div>
        </div>
        <div class="col-5">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','correo',<?= $id_contacto; ?>,this.value)" type="email"
                       class="form-control" placeholder="Correo electrónico" value="<?= $correo; ?>">
                <label>Correo electrónico</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('contactos','tel',<?= $id_contacto; ?>,this.value)" type="tel"
                       class="form-control" placeholder="Teléfono" value="<?= $tel; ?>">
                <label>Teléfono</label>
            </div>
        </div>
    </div>
</div>
<!----------- END CONTAINER ----------->


<div class="container text-end">
    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#contactoDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>

</body>
<?php include "../components/modals/contactoDelete.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>
<script src="../js/updateGenerico.js"></script>
<script>
    const contactoDelete = (id_contacto) => {
        $.ajax({
            method: "POST",
            url: "../backend/contactos/contactoDelete.php",
            data: {
                id_contacto: id_contacto
            }
        }).done(function () {
            location.href="contactos.php";
        });
    }
</script>
</html>