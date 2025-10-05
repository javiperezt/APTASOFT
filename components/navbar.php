<?php
$qqq = mysqli_query($mysqli, "SELECT SUM(is_checked) AS x FROM incidencias");
$resultqq = mysqli_fetch_assoc($qqq);
$checkedsqq = $resultqq['x'];

$c0qqq = $mysqli->query("SELECT * FROM incidencias");
$num=$c0qqq->num_rows;
$ratio=0;
if(!$ratio){
	$ratio=0;
}
if($num!=0) {
    $ratio = $checkedsqq / $num;
}
?>
<nav class="navbar navbar-expand-md mainBgColor">
    <div class="container">
        <div class="navbar-brand"><img width="80px" src="../img/logoAptaBlanco.png" alt=""></div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse " id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="inicio_dashboard.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="obras.php">Obras</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="contactos.php">Contactos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="presupuestos.php">Presupuestos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="facturas.php">Facturas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="gastos.php">Gastos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="empleados.php">Empleados</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="nominas.php">Nóminas</a>
                </li>-->
               <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="dashboard.php"> Resultados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="tareas.php"> Panel tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="empleadosTareas.php"> Panel empleados</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link <?= $ratio == 1 ? 'text-white' : 'text-danger text-decoration-underline'  ?>" aria-current="page" href="incidencias.php">Incidencias <?= $ratio == 1 ? '' : '(‼️)' ?></a>
                </li>

                <li class="nav-item">
                    <a class="bi bi-calendar3 fs-5 text-white ms-3" aria-current="page" href="calendario.php"></a>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="#"><?= $NOMBRE_USUARIO ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" aria-current="page" href="../logout.php"><i class="bi bi-box-arrow-right fs-5"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>