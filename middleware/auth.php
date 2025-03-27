<?php
function verificarSesion() {
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php?controlador=usuarios&accion=login');
        exit;
    }
}
