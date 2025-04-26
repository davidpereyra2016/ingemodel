<?php

// Las variables $controlador y $accion deben venir definidas desde index.php

// Validar que las variables existen y no están vacías (por seguridad)
if (!isset($controlador) || empty($controlador) || !isset($accion) || empty($accion)) {
    // Si no vienen definidas, redirigir a una página por defecto segura o mostrar error
    // Esto no debería pasar si index.php funciona bien, pero es una salvaguarda.
    error_log("Error en ruteador.php: Controlador o acción no definidos.");
    // Podrías redirigir a inicio o mostrar un error genérico aquí.
    // Por ahora, salimos para evitar errores graves más adelante.
    // header('Location: index.php?controlador=paginas&accion=inicio'); 
    die("Error interno: Controlador o acción no especificados.");
}

$archivoControlador = "controladores/controlador_" . $controlador . ".php";

if (file_exists($archivoControlador)) {
    include_once($archivoControlador);

    // Construir el nombre de la clase (Ej: ControladorReservas)
    $nombreClase = "Controlador" . ucfirst($controlador);

    if (class_exists($nombreClase)) {
        // Instanciar el controlador
        $instanciaControlador = new $nombreClase();

        // Verificar si la acción (método) existe en el controlador
        if (method_exists($instanciaControlador, $accion)) {
            // Llamar a la acción
            $instanciaControlador->$accion();
        } else {
            error_log("Error en ruteador.php: La acción '{$accion}' no existe en el controlador '{$nombreClase}'.");
            // Mostrar error 404 o redirigir
            die("Error: Acción no encontrada.");
        }
    } else {
        error_log("Error en ruteador.php: La clase '{$nombreClase}' no existe en el archivo '{$archivoControlador}'.");
        // Mostrar error 404 o redirigir
        die("Error: Controlador no válido (clase no encontrada).");
    }
} else {
    error_log("Error en ruteador.php: El archivo controlador '{$archivoControlador}' no existe.");
    // Mostrar error 404 o redirigir
    die("Error: Controlador no encontrado.");
}

?>
