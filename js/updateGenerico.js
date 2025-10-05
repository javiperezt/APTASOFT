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
    });
}