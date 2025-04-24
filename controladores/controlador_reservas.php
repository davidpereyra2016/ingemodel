<?php
include_once("modelos/modelo_reservas.php");
include_once("conexion.php");

class ControladorReservas
{
    private $modelo;
    private $conexion;

    public function __construct() {
        $this->modelo = new ModeloReservas();
        $this->conexion = BD::crearInstancia();
    }

    public function listar() {
        $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
        $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
        
        if ($rol == 'administrador') {
            $reservas = $this->modelo->obtenerReservas();
            $tipo = 2;
        } else {
            $reservas = $this->modelo->obtenerReservasPorUsuario($id_usuario);
            $tipo = 1;
        }
        
        include_once("vistas/reservas/listar.php");
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['id_usuario'];
            $fecha_evento = $_POST['fecha_evento'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];
            $tipo_uso = $_POST['tipo_uso'];
            $motivo_de_uso = $_POST['motivo_de_uso'];
            
            
            $id_reserva = $this->modelo->crearReserva($id_usuario, $fecha_evento, $hora_inicio, $hora_fin, $tipo_uso, $motivo_de_uso);
            
            if ($id_reserva) {
                // Si hay matriculados adicionales en la solicitud
                if (!empty($_POST['matriculas']) && is_array($_POST['matriculas'])) {
                    foreach ($_POST['matriculas'] as $index => $matricula) {
                        $nombre_completo = $_POST['nombres'][$index];
                        if (!empty($matricula) && !empty($nombre_completo)) {
                            $this->modelo->agregarMatriculadoGrupo($id_reserva, $matricula, $nombre_completo);
                        }
                    }
                }
                
                // Registrar en historial
                $this->modelo->registrarHistorial($id_reserva, $id_usuario, 'creación', null, 'pendiente', 'Solicitud de reserva creada');
                
                // Redireccionar a subir formulario
                header("Location: index.php?controlador=reservas&accion=subirFormulario&id=".$id_reserva);
                exit();
            } else {
                $_SESSION['error'] = "No se pudo crear la reserva. El horario ya está ocupado.";
                include_once("vistas/reservas/crear.php");
            }
        } else {
            include_once("vistas/reservas/crear.php");
        }
    }
    
    public function subirFormulario() {
        $id_reserva = $_GET['id'];
        $reserva = $this->modelo->obtenerReserva($id_reserva);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dir_uploads = "assets/uploads/";
            
            // Asegurarse de que el directorio existe
            if (!file_exists($dir_uploads)) {
                mkdir($dir_uploads, 0777, true);
            }
            
            $archivo_formulario = null;
            if (isset($_FILES['formulario']) && $_FILES['formulario']['error'] == 0) {
                $archivo_temp = $_FILES['formulario']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['formulario']['name'];
                $ruta_destino = $dir_uploads . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_formulario = $nombre_archivo;
                }
            }
            
            $archivo_municipal = null;
            if (isset($_FILES['formulario_municipal']) && $_FILES['formulario_municipal']['error'] == 0) {
                $archivo_temp = $_FILES['formulario_municipal']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['formulario_municipal']['name'];
                $ruta_destino = $dir_uploads . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_municipal = $nombre_archivo;
                }
            }
            
            $archivo_comprobante = null;
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
                $archivo_temp = $_FILES['comprobante']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['comprobante']['name'];
                $ruta_destino = $dir_uploads . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_comprobante = $nombre_archivo;
                }
            }
            
            $archivo_comprobante_total = null;
            if (isset($_FILES['comprobante_total']) && $_FILES['comprobante_total']['error'] == 0) {
                $archivo_temp = $_FILES['comprobante_total']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['comprobante_total']['name'];
                $ruta_destino = $dir_uploads . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_comprobante_total = $nombre_archivo;
                }
            }
            
            if ($this->modelo->subirArchivos($id_reserva, $archivo_formulario, $archivo_municipal, $archivo_comprobante, $archivo_comprobante_total)) {
                // Registrar pago de anticipo si se cargó comprobante
                if ($archivo_comprobante) {
                    $this->modelo->registrarPago($id_reserva, 'anticipo');
                    
                    // Registrar en historial
                    $this->modelo->registrarHistorial($id_reserva, $_SESSION['id_usuario'], 'pago', null, 'anticipo', 'Pago de anticipo registrado');
                }
                
                if ($archivo_comprobante_total) {
                    $this->modelo->registrarPago($id_reserva, 'saldo_pagado');
                    
                    // Registrar en historial
                    $this->modelo->registrarHistorial($id_reserva, $_SESSION['id_usuario'], 'pago', null, 'comprobante_total', 'Pago de comprobante total registrado');
                }
                
                $_SESSION['mensaje'] = "Documentos subidos correctamente. Su solicitud será revisada por un administrador.";
                header("Location: index.php?controlador=reservas&accion=listar");
                exit();
            } else {
                $_SESSION['error'] = "Error al subir los archivos.";
            }
        }
        
        include_once("vistas/reservas/subir_formulario.php");
    }
    
    public function ver() {
        $id_reserva = $_GET['id'];
        $reserva = $this->modelo->obtenerReserva($id_reserva);
        
        // Verificar si el usuario puede ver esta reserva
        if ($_SESSION['rol'] != 'administrador' && $_SESSION['id_usuario'] != $reserva['id_usuario']) {
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        include_once("vistas/reservas/ver.php");
    }
    
    public function aprobarRechazar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['rol'] == 'administrador') {
            $id_reserva = $_POST['id_reserva'];
            $estado = $_POST['estado'];
            $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : null;
            
            // Obtener estado anterior
            $reserva = $this->modelo->obtenerReserva($id_reserva);
            $estado_anterior = $reserva['estado'];
            
            if ($this->modelo->actualizarEstadoReserva($id_reserva, $estado, $motivo)) {
                // Registrar en historial
                $this->modelo->registrarHistorial($id_reserva, $_SESSION['id_usuario'], 'cambio_estado', $estado_anterior, $estado, $motivo);
                
                $_SESSION['mensaje'] = "El estado de la reserva ha sido actualizado.";
            } else {
                $_SESSION['error'] = "Error al actualizar el estado de la reserva.";
            }
            
            header("Location: index.php?controlador=reservas&accion=ver&id=".$id_reserva);
            exit();
        }
    }
    
    public function calendario() {
        include_once("vistas/reservas/calendario.php");
    }
    
    public function obtenerEventos() {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->modelo->obtenerEventosCalendario());
        exit();
    }
    
    public function generarPDF() {
        $id_reserva = $_GET['id'];
        $reserva = $this->modelo->obtenerReserva($id_reserva);
        
        // Verificar si el usuario puede acceder a esta reserva
        if ($_SESSION['rol'] != 'administrador' && $_SESSION['id_usuario'] != $reserva['id_usuario']) {
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        // Aquí iría el código para generar el PDF con la biblioteca FPDF o similar
        // Por simplicidad, redirigimos a la vista de ver
        header("Location: index.php?controlador=reservas&accion=ver&id=".$id_reserva);
        exit();
    }

    public function enviarCorreos() {
        $id_reserva = $_GET['id'];
        $reserva = $this->modelo->obtenerReserva($id_reserva);
        
        // Verificar si el usuario puede acceder a esta reserva
        if ($_SESSION['rol'] != 'administrador' && $_SESSION['id_usuario'] != $reserva['id_usuario']) {
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        // Obtener los matriculados adicionales si los hay
        $matriculados = $this->modelo->obtenerMatriculadosGrupo($id_reserva);
        
        // Incluir el archivo de envío de correos y pasar los datos
        include_once("vistas/reservas/enviosCorreos.php");
        
        // Redireccionar a la vista de ver después de intentar enviar los correos
        $_SESSION['mensaje'] = "Correos enviados correctamente.";
        header("Location: index.php?controlador=reservas&accion=listar");
        exit();
    }
}
