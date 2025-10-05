<ul class="nav nav-pills">
    <li class="nav-item">
        <a class="nav-link text-black" href="obraDetail.php?id_obra=<?= $id_obra; ?>">General</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraPlanning.php?id_obra=<?= $id_obra; ?>">Planning</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraExternos.php?id_obra=<?= $id_obra; ?>">Externos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraTareas.php?id_obra=<?= $id_obra; ?>">Tareas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraMaterial.php?id_obra=<?= $id_obra; ?>">Material</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraDashboard.php?id_obra=<?= $id_obra; ?>">Resultados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraNotas.php?id_obra=<?= $id_obra; ?>">Notas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-black" href="obraArchivos.php?id_obra=<?= $id_obra; ?>">Archivos</a>
    </li>
</ul>
<script>
    function setActiveNavItem() {
        // Obtiene la URL de la página actual
        var currentUrl = window.location.href;

        // Obtiene todos los elementos de navegación en la lista
        var navItems = document.querySelectorAll('.nav-item');

        // Itera sobre los elementos de navegación
        navItems.forEach(function (navItem) {
            // Obtiene el enlace dentro del elemento de navegación
            var link = navItem.querySelector('a');

            // Obtiene la URL del enlace
            var linkUrl = link.href;

            // Comprueba si la URL de la página actual coincide con la URL del enlace
            if (currentUrl === linkUrl) {
                // Agrega la clase 'active' y elimina la clase 'text-black'
                link.classList.add('active');
                link.classList.remove('text-black');
            }
        });
    }

    // Llama a la función cuando se carga la página
    window.onload = setActiveNavItem;

</script>