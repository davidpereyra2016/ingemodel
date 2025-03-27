<?php
ob_start(); // Inicia el output buffering
session_start();

// Si no hay sesión activa y no estamos en la página de login, redirigir al login
if (!isset($_SESSION['usuario_id']) && 
    (!isset($_GET['controlador']) || $_GET['controlador'] !== 'usuarios' || 
     !isset($_GET['accion']) || $_GET['accion'] !== 'login')) {
    header('Location: index.php?controlador=usuarios&accion=login');
    exit;
}

$controlador = "paginas";
$accion = "inicio";

if (isset($_GET['controlador']) && isset($_GET['accion'])) {
    if (($_GET['controlador'] != "") && ($_GET['accion'] != "")) {
        $controlador = $_GET['controlador'];
        $accion = $_GET['accion'];
    }
}

include_once("vistas/template.php");

// Al final del archivo
ob_end_flush();

