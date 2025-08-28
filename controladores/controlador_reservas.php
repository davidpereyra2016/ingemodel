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
        
        // Obtener el tipo de listado de la URL (1 = Mis Reservas, 2 = Gestionar Reservas)
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 1;
        
        if ($rol == 'administrador') {
            if ($tipo == 1) {
                // Si el admin está viendo "Mis Reservas", solo muestra sus propias reservas
                $reservas = $this->modelo->obtenerReservasPorUsuario($id_usuario);
            } else {
                // Si el admin está gestionando todas las reservas (tipo=2)
                $reservas = $this->modelo->obtenerReservas();
            }
        } else {
            // Usuarios normales solo pueden ver sus propias reservas
            $reservas = $this->modelo->obtenerReservasPorUsuario($id_usuario);
            // Forzar tipo 1 para usuarios normales
            $tipo = 1;
        }
        
        include_once("vistas/reservas/listar.php");
    }
    
    public function crear() {
        // Cargar los documentos para descargar según su tipo
        include_once("modelos/modelo_configuracion.php");
        $modeloConfig = new ModeloConfiguracion();
        $documentos = $modeloConfig->obtenerDocumentos();
        
        // Separar documentos por tipo
        $formularios_condiciones = [];
        
        foreach ($documentos as $doc) {
            if ($doc['activo']) {
                if($doc['tipo'] === 'condiciones') {
                    $formularios_condiciones[] = $doc;
                }
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación anti-reenvío: verificar token de sesión
            if (!isset($_POST['form_token']) || !isset($_SESSION['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
                $_SESSION['error'] = "Solicitud inválida. Por favor, recarga la página e inténtalo de nuevo.";
                header('Location: index.php?controlador=reservas&accion=crear');
                exit();
            }
            
            // Limpiar el token para evitar reutilización
            unset($_SESSION['form_token']);
            
            $id_usuario = $_SESSION['id_usuario'];
            $fecha_evento = $_POST['fecha_evento'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];
            $tipo_uso = $_POST['tipo_uso'];
            $motivo_de_uso = $_POST['motivo_de_uso'];
            
            // Generar código único y fecha de vencimiento
            $codigo_unico = 'RES-' . strtoupper(bin2hex(random_bytes(4)));
            $fecha_vencimiento = date('Y-m-d H:i:s', strtotime('+48 hours'));
            
            // Guardar código en sesión para debug
            $_SESSION['ultimo_codigo_reserva'] = $codigo_unico;
            
            // Obtener el rol del usuario para aplicar validaciones específicas
            $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'ingeniero';
            
            // 1. Llamar al modelo y capturar el resultado
            $id_reserva = $this->modelo->crearReserva($id_usuario, $fecha_evento, $hora_inicio, $hora_fin, $tipo_uso, $motivo_de_uso, $codigo_unico, $fecha_vencimiento, $rol);
            // 2. Verificar si hay error primero
            if (isset($id_reserva['error'])) {
                $_SESSION['error'] = $id_reserva['error'];
                header('Location: index.php?controlador=reservas&accion=crear');
                exit;
            }
            // 3. Si no hay error, obtener el ID de la reserva    
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
                
                // Redireccionar a subir formulario, pasando solo el código único
                // Asegurar que la URL está bien formada
                $_SESSION['debug_info'] = "Redirigiendo a código: " . $codigo_unico;
                $redirect_url = "index.php?controlador=reservas&accion=subirFormulario&codigo=" . $codigo_unico;
                header("Location: " . $redirect_url);
                exit();
            } else {
                $_SESSION['error'] = "No se pudo crear la reserva. Ya existe una reserva para esa fecha o has alcanzado el límite de reservas.";
                header('Location: index.php?controlador=reservas&accion=crear');
                exit();
            }
        } else {
            // Generar token único para el formulario
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
            include_once("vistas/reservas/crear.php");
        }
    }
    
    public function subirFormulario() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?controlador=usuarios&accion=login');
            exit();
        }

        // --- INICIO CAMBIOS ---
        if (!isset($_GET['codigo'])) {
            $_SESSION['error'] = 'No se proporcionó el código de reserva.';
            header('Location: index.php?controlador=reservas&accion=listar');
            exit();
        }

        $codigo_unico = $_GET['codigo'];
        
        // Debug info para capturar el código recibido
        $_SESSION['debug_codigo_recibido'] = $codigo_unico;

        // Verificar y potencialmente cancelar la reserva si ha expirado ANTES de mostrar el formulario
        $this->modelo->verificarYCancelarReservaExpirada($codigo_unico);

        // Obtener la reserva por código único
        $reserva = $this->modelo->obtenerReservaPorCodigo($codigo_unico);

        if (!$reserva) {
            $_SESSION['error'] = 'Reserva no encontrada o código inválido.';
            header('Location: index.php?controlador=reservas&accion=listar');
            exit();
        }
        // --- FIN CAMBIOS ---

        // Verificar si la reserva pertenece al usuario actual o si es admin
        if ($reserva['id_usuario'] != $_SESSION['id_usuario'] && $_SESSION['rol'] != 'administrador') {
            $_SESSION['error'] = 'No tiene permiso para ver esta reserva.';
            header('Location: index.php?controlador=reservas&accion=listar');
            exit();
        }

        // Manejo de la subida de archivos si es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- INICIO CAMBIOS POST ---
            // Re-verificar expiración justo antes de procesar la subida
            $this->modelo->verificarYCancelarReservaExpirada($codigo_unico);
            $reservaActualizada = $this->modelo->obtenerReservaPorCodigo($codigo_unico); // Obtener estado más reciente

            // Si la reserva fue cancelada mientras el usuario tenía el formulario abierto
            // EXCEPCIÓN: Permitir a administradores subir comprobantes de devolución en estado 'baja'
            if ($reservaActualizada['estado'] === 'cancelada' || $reservaActualizada['estado'] === 'rechazada') {
                $_SESSION['error'] = 'No se pueden subir archivos. La reserva ha sido cancelada o rechazada.';
                header('Location: index.php?controlador=reservas&accion=subirFormulario&codigo=' . $codigo_unico);
                exit();
            }
            
            // Para estado 'baja': solo permitir a administradores subir comprobantes de devolución
            if ($reservaActualizada['estado'] === 'baja' && $_SESSION['rol'] !== 'administrador') {
                $_SESSION['error'] = 'No se pueden subir archivos. La reserva ha sido dada de baja.';
                header('Location: index.php?controlador=reservas&accion=subirFormulario&codigo=' . $codigo_unico);
                exit();
            }
            // Permitimos subir archivos incluso si la reserva está aprobada
            // Los usuarios pueden necesitar subir comprobantes de pago total o formularios adicionales
            // después de que la reserva haya sido aprobada
            // --- FIN CAMBIOS POST ---

            $target_dir = "assets/uploads/";
            
            // Asegurarse de que el directorio existe
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $archivo_formulario = null;
            if (isset($_FILES['formulario']) && $_FILES['formulario']['error'] == 0) {
                $archivo_temp = $_FILES['formulario']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['formulario']['name'];
                $ruta_destino = $target_dir . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_formulario = $nombre_archivo;
                }
            }
            
            $archivo_municipal = null;
            if (isset($_FILES['formulario_municipal']) && $_FILES['formulario_municipal']['error'] == 0) {
                $archivo_temp = $_FILES['formulario_municipal']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['formulario_municipal']['name'];
                $ruta_destino = $target_dir . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_municipal = $nombre_archivo;
                }
            }
            
            $archivo_comprobante = null;
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
                $archivo_temp = $_FILES['comprobante']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['comprobante']['name'];
                $ruta_destino = $target_dir . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_comprobante = $nombre_archivo;
                }
            }
            
            $archivo_comprobante_total = null;
            if (isset($_FILES['comprobante_total']) && $_FILES['comprobante_total']['error'] == 0) {
                $archivo_temp = $_FILES['comprobante_total']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['comprobante_total']['name'];
                $ruta_destino = $target_dir . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_comprobante_total = $nombre_archivo;
                }
            }
            
            // Manejo del comprobante de devolución (solo para administradores en estado 'baja')
            $archivo_comprobante_devolucion = null;
            $monto_devolucion = null;
            $observaciones_devolucion = null;
            
            if ($reservaActualizada['estado'] === 'baja' && $_SESSION['rol'] === 'administrador') {
                // Procesar comprobante de devolución
                if (isset($_FILES['comprobante_devolucion']) && $_FILES['comprobante_devolucion']['error'] == 0) {
                    $archivo_temp = $_FILES['comprobante_devolucion']['tmp_name'];
                    $nombre_archivo = time() . '_devolucion_' . $_FILES['comprobante_devolucion']['name'];
                    $ruta_destino = $target_dir . $nombre_archivo;
                    
                    if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                        $archivo_comprobante_devolucion = $nombre_archivo;
                    }
                }
                
                // Obtener datos de devolución
                if (isset($_POST['monto_devolucion']) && !empty($_POST['monto_devolucion'])) {
                    $monto_devolucion = floatval($_POST['monto_devolucion']);
                    // Validar que el monto no exceda el monto de la reserva
                    if ($monto_devolucion > $reservaActualizada['monto']) {
                        $_SESSION['error'] = 'El monto de devolución no puede ser mayor al monto de la reserva.';
                        header('Location: index.php?controlador=reservas&accion=subirFormulario&codigo=' . $codigo_unico);
                        exit();
                    }
                }
                
                if (isset($_POST['observaciones_devolucion'])) {
                    $observaciones_devolucion = trim($_POST['observaciones_devolucion']);
                }
            }
            
            if ($this->modelo->subirArchivos($reserva['id'], $archivo_formulario, $archivo_comprobante, $archivo_municipal, $archivo_comprobante_total, $archivo_comprobante_devolucion, $monto_devolucion, $observaciones_devolucion)) {
                // Registrar pago de anticipo si se cargó comprobante
                if ($archivo_comprobante) {
                    $this->modelo->registrarPago($reserva['id'], 'anticipo');
                    
                    // Registrar en historial
                    $this->modelo->registrarHistorial($reserva['id'], $_SESSION['id_usuario'], 'pago', null, 'anticipo', 'Pago de anticipo registrado');
                }
                
                if ($archivo_comprobante_total) {
                    $this->modelo->registrarPago($reserva['id'], 'saldo_pagado');
                    
                    // Registrar en historial
                    $this->modelo->registrarHistorial($reserva['id'], $_SESSION['id_usuario'], 'pago', null, 'comprobante_total', 'Pago de comprobante total registrado');
                }
                
                // Registrar devolución si se procesó
                if ($archivo_comprobante_devolucion || $monto_devolucion || $observaciones_devolucion) {
                    $detalles_devolucion = [];
                    if ($monto_devolucion) $detalles_devolucion[] = "Monto: $" . number_format($monto_devolucion, 2);
                    if ($archivo_comprobante_devolucion) $detalles_devolucion[] = "Comprobante: {$archivo_comprobante_devolucion}";
                    if ($observaciones_devolucion) $detalles_devolucion[] = "Observaciones: {$observaciones_devolucion}";
                    
                    $detalle_historial = 'Devolución registrada - ' . implode(', ', $detalles_devolucion);
                    
                    // Registrar en historial
                    $this->modelo->registrarHistorial(
                        $reserva['id'], 
                        $_SESSION['id_usuario'], 
                        'devolucion', 
                        null, 
                        'comprobante_devolucion', 
                        $detalle_historial
                    );
                }
                
                // Crear notificación para administradores cuando un usuario sube documentación
                include_once("modelos/modelo_notificaciones.php");
                $tipos_documentos = [];
                if ($archivo_formulario) $tipos_documentos[] = "formulario de solicitud";
                if ($archivo_municipal) $tipos_documentos[] = "formulario municipal";
                if ($archivo_comprobante) $tipos_documentos[] = "comprobante de anticipo";
                if ($archivo_comprobante_total) $tipos_documentos[] = "comprobante de pago total";
                if ($archivo_comprobante_devolucion) $tipos_documentos[] = "comprobante de devolución";
                
                if (!empty($tipos_documentos)) {
                    $documentos_texto = implode(", ", $tipos_documentos);
                    
                    // Mensaje diferente si es una devolución (administrador)
                    if ($archivo_comprobante_devolucion) {
                        $mensaje_notificacion = "El administrador {$_SESSION['nombre']} {$_SESSION['apellido']} ha registrado una devolución para la reserva #{$reserva['id']} ({$reserva['tipo_uso']}) del usuario {$reserva['nombre']} {$reserva['apellido']}";
                    } else {
                        $mensaje_notificacion = "El usuario {$reserva['nombre']} {$reserva['apellido']} ha subido documentación ({$documentos_texto}) para la reserva #{$reserva['id']} ({$reserva['tipo_uso']})";
                    }
                    
                    ModeloNotificaciones::crearNotificacion(
                        $reserva['id_usuario'], 
                        $mensaje_notificacion, 
                        $reserva['id'], 
                        $archivo_comprobante_devolucion ? 'devolucion' : 'documentacion'
                    );
                }
                
                // Mensaje diferente si se registró una devolución
                if ($archivo_comprobante_devolucion || $monto_devolucion || $observaciones_devolucion) {
                    $_SESSION['mensaje'] = "Devolución registrada correctamente. Los datos han sido guardados en el sistema.";
                } else {
                    $_SESSION['mensaje'] = "Documentos subidos correctamente. Su solicitud será revisada por un administrador.";
                }
                header("Location: index.php?controlador=reservas&accion=listar");
                exit();
            } else {
                $_SESSION['error'] = "Error al subir los archivos.";
            }
        }
        
        // Cargar los documentos para descargar según su tipo
        include_once("modelos/modelo_configuracion.php");
        $modeloConfig = new ModeloConfiguracion();
        $documentos = $modeloConfig->obtenerDocumentos();
        
        // Separar documentos por tipo
        $formularios = [];
        $formularios_municipales = [];
        
        foreach ($documentos as $doc) {
            if ($doc['activo']) {
                if ($doc['tipo'] === 'solicitud') {
                    $formularios[] = $doc;
                } elseif ($doc['tipo'] === 'municipal') {
                    $formularios_municipales[] = $doc;
                }
            }
        }
        
        // IMPORTANTE: Pasar explícitamente el código único a la vista como variable global
        // para que esté disponible en el formulario y el script JavaScript
        global $codigo_unico; // Intentar pasar como variable global
        
        // Incluir la vista con el código único
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
    
    // Endpoint para verificar el estado de la reserva vía AJAX
    public function verificarEstado() {
        header('Content-Type: application/json'); // Asegurar que la respuesta sea JSON

        if (!isset($_GET['codigo'])) {
            echo json_encode(['estado' => 'error', 'mensaje' => 'Código no proporcionado']);
            exit();
        }

        $codigo_unico = $_GET['codigo'];

        // Llamar a la función que verifica y cancela si es necesario
        // Ahora esta función devuelve información sobre el resultado
        $resultado_verificacion = $this->modelo->verificarYCancelarReservaExpirada($codigo_unico);

        // Obtener el estado actualizado de la reserva
        $reserva = $this->modelo->obtenerReservaPorCodigo($codigo_unico);

        if ($reserva) {
            // Determinar si tiene comprobantes de pago
            $tiene_pago = !empty($reserva['archivo_comprobante']) || !empty($reserva['archivo_comprobante_total']);
            
            echo json_encode([
                'estado' => $reserva['estado'],
                'fecha_vencimiento' => $reserva['fecha_vencimiento'],
                'motivo_rechazo' => $reserva['motivo_rechazo'], // Incluir motivo si existe
                'tiene_pago' => $tiene_pago, // Indicar si tiene algún comprobante de pago
                'comprobante_50' => !empty($reserva['archivo_comprobante']),
                'comprobante_100' => !empty($reserva['archivo_comprobante_total']),
                'mensaje_verificacion' => isset($resultado_verificacion['motivo']) ? $resultado_verificacion['motivo'] : null
            ]);
        } else {
            echo json_encode(['estado' => 'no_encontrada', 'mensaje' => 'Reserva no encontrada']);
        }
        exit(); // Terminar ejecución después de enviar JSON
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

    public function actualizarMotivoUso()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador') {
            if (isset($_POST['id_reserva']) && !empty($_POST['id_reserva']) && isset($_POST['motivo_de_uso'])) {
                $id_reserva = filter_var($_POST['id_reserva'], FILTER_SANITIZE_NUMBER_INT);
                $motivo_de_uso = trim(htmlspecialchars($_POST['motivo_de_uso']));
                $id_usuario_admin = $_SESSION['id_usuario'];

                // Obtener el motivo de uso anterior para el historial
                $reserva_anterior = $this->modelo->obtenerReserva($id_reserva);
                $motivo_anterior = $reserva_anterior ? $reserva_anterior['motivo_de_uso'] : 'N/A';

                $actualizado = $this->modelo->actualizarMotivoUso($id_reserva, $motivo_de_uso, $id_usuario_admin, $motivo_anterior);

                if ($actualizado) {
                    $_SESSION['mensaje'] = "El motivo de uso se ha actualizado correctamente.";
                } else {
                    $_SESSION['error'] = "Error al actualizar el motivo de uso.";
                }

                header('Location: index.php?controlador=reservas&accion=ver&id=' . $id_reserva);
                exit();
            } else {
                $_SESSION['error'] = "Datos incompletos para la actualización.";
                header('Location: index.php?controlador=reservas&accion=listar');
                exit();
            }
        } else {
            $_SESSION['error'] = "Acceso no autorizado.";
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit();
        }
    }
    
    public function obtenerEventos() {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->modelo->obtenerEventosCalendario());
        exit();
    }

    public function buscarFechaEvento() {
        ob_clean();
        if (isset($_GET['fecha_evento']) && $_GET['fecha_evento'] != '') {
            $fecha_evento = $_GET['fecha_evento'];
            $eventos = $this->modelo->buscarFechaEvento($fecha_evento);
            echo json_encode($eventos);
            exit();
        }
    }
    
    public function generarPDF() {
        $id_reserva = $_GET['id'];
        $reserva = $this->modelo->obtenerReserva($id_reserva);
        
        // Verificar si el usuario puede acceder a esta reserva
        if ($_SESSION['rol'] != 'administrador' && $_SESSION['id_usuario'] != $reserva['id_usuario']) {
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        // Verificar que la reserva esté aprobada o dada de baja (para mostrar devolución)
        if ($reserva['estado'] != 'aprobada' && $reserva['estado'] != 'baja') {
            $_SESSION['error'] = "Solo se pueden generar comprobantes para reservas aprobadas o dadas de baja.";
            header("Location: index.php?controlador=reservas&accion=ver&id=".$id_reserva);
            exit();
        }
        
        // Cargar la vista del comprobante para impresión
        include_once("vistas/reservas/comprobante_pdf.php");
        exit();
    }

    public function eliminar() {
        // Verificar permisos según el tipo de acción
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_accion'])) {
            $tipo_accion = $_POST['tipo_accion'];
            
            // Solo administradores pueden eliminar completamente
            if ($tipo_accion === 'eliminar' && $_SESSION['rol'] != 'administrador') {
                $_SESSION['error'] = "No tienes permisos para eliminar completamente reservas.";
                header("Location: index.php?controlador=reservas&accion=listar");
                exit();
            }
            
            // Para dar de baja: administradores pueden dar de baja cualquier reserva,
            // usuarios normales solo pueden dar de baja sus propias reservas
            if ($tipo_accion === 'baja' && $_SESSION['rol'] != 'administrador') {
                // Verificar que la reserva pertenezca al usuario
                $id_reserva = $_POST['id_reserva'];
                $reserva = $this->modelo->obtenerReserva($id_reserva);
                
                if (!$reserva || $reserva['id_usuario'] != $_SESSION['id_usuario']) {
                    $_SESSION['error'] = "No tienes permisos para dar de baja esta reserva.";
                    header("Location: index.php?controlador=reservas&accion=listar");
                    exit();
                }
                
                // Verificar que la reserva esté en un estado que permita baja por el usuario
                if (!in_array($reserva['estado'], ['pendiente', 'aprobada'])) {
                    $_SESSION['error'] = "Solo puedes dar de baja reservas en estado 'pendiente' o 'aprobada'.";
                    header("Location: index.php?controlador=reservas&accion=listar");
                    exit();
                }
            }
        } else {
            // Si no hay tipo de acción definido, solo administradores pueden acceder
            if ($_SESSION['rol'] != 'administrador') {
                $_SESSION['error'] = "No tienes permisos para eliminar reservas.";
                header("Location: index.php?controlador=reservas&accion=listar");
                exit();
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva']) && isset($_POST['tipo_accion'])) {
            $id_reserva = $_POST['id_reserva'];
            $tipo_accion = $_POST['tipo_accion']; // 'baja' o 'eliminar'
            
            // Obtener información de la reserva antes de procesarla
            $reserva = $this->modelo->obtenerReserva($id_reserva);
            
            if ($reserva) {
                $estado_anterior = $reserva['estado'];
                
                if ($tipo_accion === 'baja') {
                    // Dar de baja (cambiar estado a 'baja')
                    if ($this->modelo->eliminarReserva($id_reserva, false)) {
                    // Registrar en historial con mensaje detallado según quién lo haga
                $usuario_actual = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
                $comentario = ($_SESSION['rol'] == 'administrador') 
                    ? "Reserva dada de baja por administrador {$usuario_actual} (ID: {$_SESSION['id_usuario']})"
                    : "Reserva dada de baja por el usuario propietario {$usuario_actual} (ID: {$_SESSION['id_usuario']})";
                
                $this->modelo->registrarHistorial(
                    $id_reserva, 
                    $_SESSION['id_usuario'], 
                    'baja', 
                    $estado_anterior, 
                    'baja', 
                    $comentario
                );
                    
                    // Crear notificación para administradores cuando un usuario da de baja su reserva
                    if ($_SESSION['rol'] != 'administrador') {
                        include_once("modelos/modelo_notificaciones.php");
                        $mensaje_notificacion = "El usuario {$reserva['nombre']} {$reserva['apellido']} ha dado de baja su reserva #{$id_reserva} ({$reserva['tipo_uso']})";
                        ModeloNotificaciones::crearNotificacion(
                            $_SESSION['id_usuario'], 
                            $mensaje_notificacion, 
                            $id_reserva, 
                            'baja_usuario'
                        );
                    }
                    
                    $mensaje_exito = ($_SESSION['rol'] == 'administrador')
                        ? "La reserva ha sido dada de baja correctamente."
                        : "Tu reserva ha sido dada de baja correctamente. Esta acción es irreversible.";
                    
                    $_SESSION['mensaje'] = $mensaje_exito;
                    } else {
                        $_SESSION['error'] = "Error al dar de baja la reserva.";
                    }
                } elseif ($tipo_accion === 'eliminar') {
                    // Registrar en historial ANTES de la eliminación completa
                    $admin_actual = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
                    $comentario_eliminacion = "Reserva eliminada completamente por administrador {$admin_actual} (ID: {$_SESSION['id_usuario']}). Datos respaldados en tabla reservas_eliminadas.";
                    
                    $this->modelo->registrarHistorial(
                        $id_reserva, 
                        $_SESSION['id_usuario'], 
                        'eliminacion_completa', 
                        $estado_anterior, 
                        'eliminada', 
                        $comentario_eliminacion
                    );
                    
                    // Eliminar completamente de la base de datos
                    if ($this->modelo->eliminarReserva($id_reserva, true)) {
                        $_SESSION['mensaje'] = "La reserva ha sido eliminada completamente de la base de datos.";
                    } else {
                        $_SESSION['error'] = "Error al eliminar completamente la reserva.";
                    }
                } else {
                    $_SESSION['error'] = "Tipo de acción no válida.";
                }
            } else {
                $_SESSION['error'] = "Reserva no encontrada.";
            }
        } else {
            $_SESSION['error'] = "Solicitud inválida. Faltan parámetros requeridos.";
        }
        
        header("Location: index.php?controlador=reservas&accion=listar");
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

    public function actualizarMontosPago() {
        // Verificar que solo los administradores puedan actualizar montos
        if ($_SESSION['rol'] != 'administrador') {
            $_SESSION['error'] = "No tienes permisos para modificar montos de pago.";
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
            $id_reserva = $_POST['id_reserva'];
            $monto_anticipo = isset($_POST['monto_anticipo']) && $_POST['monto_anticipo'] !== '' ? floatval($_POST['monto_anticipo']) : null;
            $monto_saldo = isset($_POST['monto_saldo']) && $_POST['monto_saldo'] !== '' ? floatval($_POST['monto_saldo']) : null;
            
            // Obtener información de la reserva antes de la actualización
            $reserva = $this->modelo->obtenerReserva($id_reserva);
            
            if ($reserva) {
                // Actualizar los montos
                if ($this->modelo->actualizarMontosPago($id_reserva, $monto_anticipo, $monto_saldo)) {
                    // Registrar en historial
                    $comentario = "Montos actualizados por administrador. ";
                    if ($monto_anticipo !== null) {
                        $comentario .= "Anticipo: $" . number_format($monto_anticipo, 2) . ". ";
                    }
                    if ($monto_saldo !== null) {
                        $comentario .= "Saldo: $" . number_format($monto_saldo, 2) . ".";
                    }
                    
                    $this->modelo->registrarHistorial(
                        $id_reserva, 
                        $_SESSION['id_usuario'], 
                        'actualizacion_montos', 
                        null, 
                        null, 
                        $comentario
                    );
                    
                    $_SESSION['mensaje'] = "Los montos de pago han sido actualizados correctamente.";
                } else {
                    $_SESSION['error'] = "Error al actualizar los montos de pago.";
                }
            } else {
                $_SESSION['error'] = "Reserva no encontrada.";
            }
        } else {
            $_SESSION['error'] = "Solicitud inválida. Faltan parámetros requeridos.";
        }
        
        // Redireccionar de vuelta a la vista de detalles
        $redirect_id = isset($_POST['id_reserva']) ? $_POST['id_reserva'] : '';
        header("Location: index.php?controlador=reservas&accion=ver&id=" . $redirect_id);
        exit();
    }
}
