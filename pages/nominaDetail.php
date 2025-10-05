<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_nomina = filter_input(INPUT_GET, 'id_nomina', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM nominas where id=$id_nomina");
while ($row = $c0->fetch_assoc()) {
    $id_empleado = $row['id_empleado'];
    $estado = $row['estado'];
    $fecha = $row['fecha'];
    $comentario = $row['comentario'];
    $salario = $row['salario'];
    $total_ss = $row['total_ss'];
    $gastos_ss_empresa = $row['gastos_ss_empresa'];
    $irpf = $row['irpf'];
}


if ($id_empleado) {
    $c1 = $mysqli->query("SELECT * FROM empleados where id=$id_empleado");
    while ($row = $c1->fetch_assoc()) {
        $nombre_empleado = $row['nombre'];
    }
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
        <a class="pointer" onclick="location.href=document.referrer"><i
                    class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Editar partida</p>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-7">
            <div class="form-floating">
                <select onchange="updateGenerico('nominas','id_empleado',<?= $id_nomina; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Empleado</option>
                    <?php
                    $c2 = $mysqli->query("SELECT * FROM empleados");
                    while ($row = $c2->fetch_assoc()) {
                        $s_id_empleado = $row['id'];
                        $s_nombre_empleado = $row['nombre'];

                        $selected = "";
                        if ($id_empleado == $s_id_empleado) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$s_id_empleado'>$s_nombre_empleado</option>";
                    }
                    ?>
                </select>
                <label>Empleado</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onchange="updateGenerico('nominas','fecha',<?= $id_nomina; ?>,this.value)"
                       type="date" class="form-control" value="<?= $fecha; ?>">
                <label>Fecha</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('nominas','estado',<?= $id_nomina; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Estado</option>
                    <option value="pendiente" <?= $estado == "pendiente" ? "selected" : ""; ?>>Pendiente</option>
                    <option value="pagado" <?= $estado == "pagado" ? "selected" : ""; ?>>Pagado</option>
                </select>
                <label>Estado</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <textarea onfocusout="updateGenerico('nominas','comentario',<?= $id_nomina; ?>,this.value)"
                          class="form-control" placeholder="Comentario"
                          style="height: 100px"><?= $comentario; ?></textarea>
                <label>Comentario</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- MAIN ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">

    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th width="200px" scope="col">Concepto</th>
            <th width="120px" scope="col">Importe</th>
            <th scope="col">Cuenta</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Salario</td>
            <td><input type="number" class="form-control" value="<?= $salario; ?>"
                       onchange="updateGenerico('nominas','salario',<?= $id_nomina; ?>,this.value)"></td>
            <td><b>640</b> Sueldos y salarios</td>
        </tr>
        <tr>
            <td>Total S.S.</td>
            <td><input type="number" class="form-control text-danger" value="<?= $total_ss; ?>"
                       onchange="updateGenerico('nominas','total_ss',<?= $id_nomina; ?>,this.value)"></td>
            <td><b>476</b> Organismos de la Seguridad Social, acreedores</td>
        </tr>
        <tr>
            <td>Gastos S.S. Empresa</td>
            <td><input type="number" class="form-control" value="<?= $gastos_ss_empresa; ?>"
                       onchange="updateGenerico('nominas','gastos_ss_empresa',<?= $id_nomina; ?>,this.value)"></td>
            <td><b>642</b> Seguridad Social a cargo de la empresa</td>
        </tr>
        <tr>
            <td>IRPF</td>
            <td><input type="number" class="form-control  text-danger" value="<?= $irpf; ?>"
                       onchange="updateGenerico('nominas','irpf',<?= $id_nomina; ?>,this.value)"></td>
            <td><b>475</b> Hacienda Pública, acreedores</td>
        </tr>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END MAIN ----------->


<!----------- RESUMEN ----------->
<div class="container-md">
    <div class="d-flex gap-4 bg-white p-4 rounded-3 ms-auto" style="width: fit-content">
        <div>
            <p id="result" class="fs-3 text-black fw-bold"><?= $salario + $gastos_ss_empresa - $total_ss - $irpf; ?>
                €</p>
            <p class="letraPeq" style="color: #AEAEAE">A pagar</p>
        </div>
    </div>
</div>
<!----------- END RESUMEN ----------->


<div class="container text-end mt-3">
    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#nominaDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>


<?php include "../components/updateConfirmation.php"; ?>
<?php include "../components/modals/nominaDelete.php"; ?>
</body>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>

<script>
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
            showMessage();
            $("#result").load(location.href + " #result");
        });
    }

    const nominaDelete = (id_nomina) => {
        $.ajax({
            method: "POST",
            url: "../backend/nominas/nominaDelete.php",
            data: {
                id_nomina: id_nomina
            }
        }).done(function () {
            location.href = "nominas.php";
        });
    }

</script>
</html>