$(document).ready(function() {
    $('.js-example-basic-single').select2({
        theme: "classic"
    });

    $('#myTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
        },
        
        order: [
            [0, 'desc']
        ], // Ordenar por la primera columna (ID)
        scrollX: true, // Pantallas pequeñas
        scrollCollapse: true, // Pantallas pequeñas
        paging: true, // Paginación
        pageLength: 10, // Número de filas por página
        lengthMenu: [10, 25, 50, 100], // Opciones de filas por página
        searching: true, // Barra de búsqueda
        ordering: true, // Ordenar columnas
        info: true, // Información de la tabla
        autoWidth: false, // Ancho automático de columnas
        responsive: true, // Responsive
    });
});