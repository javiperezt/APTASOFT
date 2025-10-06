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
$incidenciasAbiertas = $num - $checkedsqq;
?>
<nav class="navbar navbar-expand-lg navbar-dark mainBgColor shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="inicio_dashboard.php">
            <img src="../img/logoAptaBlanco.png" alt="Logo" width="70">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Obras Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="obrasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-building"></i> Obras
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="obrasDropdown">
                        <li><a class="dropdown-item" href="obras.php">Obras</a></li>
                        <li><a class="dropdown-item" href="presupuestos.php">Presupuestos</a></li>
                    </ul>
                </li>

                <!-- Facturación Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="facturacionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-receipt"></i> Facturación
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="facturacionDropdown">
                        <li><a class="dropdown-item" href="facturas.php">Facturas</a></li>
                        <li><a class="dropdown-item" href="gastos.php">Gastos</a></li>
                    </ul>
                </li>

                <!-- Contactos Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="contactosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-people"></i> Contactos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="contactosDropdown">
                        <li><a class="dropdown-item" href="contactos.php">Contactos</a></li>
                        <li><a class="dropdown-item" href="empleados.php">Empleados</a></li>
                    </ul>
                </li>

                <!-- Paneles -->
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-bar-chart"></i> Resultados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendario.php"><i class="bi bi-calendar3"></i> Planificación</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="tareas.php"> Panel tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="empleadosTareas.php"> Panel empleados</a>
                </li>-->
                <li class="nav-item">
                    <a class="nav-link text-white position-relative" href="incidencias.php">
                        <i class="bi bi-exclamation-triangle"></i> Incidencias
                        <?php if($incidenciasAbiertas > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?= $incidenciasAbiertas ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <!-- Usuario -->
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="usuarioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= $NOMBRE_USUARIO ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="usuarioDropdown">
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>