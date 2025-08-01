<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception(".env file not found at: $path");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if they exist
        if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
            $value = substr($value, 1, -1);
        }
        
        // Set as global variable
        $GLOBALS[$name] = $value;
    }
}

// Load .env file from project root (two directories up)
try {
    loadEnv(__DIR__ . '/../../.env');
} catch (Exception $e) {
    die("Error loading .env file: " . $e->getMessage());
}

// Fix the paths to the PHPMailer files
// 
$base_path = realpath(__DIR__ . '/../../');
require_once($base_path . "/utils/lib/phpMailer/PHPMailer.php");
require_once($base_path . "/utils/lib/phpMailer/SMTP.php"); 
require_once($base_path . "/utils/lib/phpMailer/Exception.php");

//verificar que los datos de la reserva esten disponibles
if (!isset($reserva) || !is_array($reserva)) {
    die("Error: No se recibieron los datos de la reserva.");
}

// Create a formatted date
$fecha_formateada = date('d/m/Y', strtotime($reserva['fecha_evento']));

// Definir colores y títulos según el estado
$header_color = '#3498db'; // Azul por defecto
$titulo_estado = 'Confirmación de Reserva';
$mensaje_principal = "Le confirmamos que su reserva ha sido <strong>{$reserva['estado']}</strong>.";

switch($reserva['estado']) {
    case 'aprobada':
        $header_color = '#27ae60'; // Verde
        $titulo_estado = 'Reserva Aprobada';
        $mensaje_principal = "¡Excelente! Su reserva ha sido <strong>aprobada</strong>.";
        break;
    case 'rechazada':
        $header_color = '#e74c3c'; // Rojo
        $titulo_estado = 'Reserva Rechazada';
        $mensaje_principal = "Lamentamos informarle que su reserva ha sido <strong>rechazada</strong>.";
        break;
    case 'cancelada':
        $header_color = '#f39c12'; // Naranja
        $titulo_estado = 'Reserva Cancelada';
        $mensaje_principal = "Su reserva ha sido <strong>cancelada</strong> por no presentar los comprobantes de pago dentro de las 48 horas establecidas.";
        break;
    case 'baja':
        $header_color = '#95a5a6'; // Gris
        $titulo_estado = 'Reserva Dada de Baja';
        $mensaje_principal = "Su reserva ha sido <strong>dada de baja</strong> por la administración.";
        break;
}

// Create an HTML email body
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: {$header_color}; color: white; padding: 10px 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
        .contact-info { background-color: #f8f9fa; padding: 15px; border-left: 4px solid {$header_color}; margin: 15px 0; }
        .important-notice { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>{$titulo_estado}</h2>
        </div>
        <div class='content'>
            <p>Estimado/a <strong>{$reserva['nombre']} {$reserva['apellido']}</strong>,</p>
            
            <p>{$mensaje_principal}</p>
            
            <h3>Detalles de la Reserva:</h3>
            <table>
                <tr>
                    <th>Fecha del Evento</th>
                    <td>{$fecha_formateada}</td>
                </tr>
                <tr>
                    <th>Horario</th>
                    <td>{$reserva['hora_inicio']} - {$reserva['hora_fin']}</td>
                </tr>
                <tr>
                    <th>Tipo de Uso</th>
                    <td>{$reserva['tipo_uso']}</td>
                </tr>
                <tr>
                    <th>Monto</th>
                    <td>$ {$reserva['monto']}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{$reserva['estado']}</td>
                </tr>
            </table>";

// Add the additional matriculated members if they exist
if (isset($matriculados) && !empty($matriculados)) {
    $email_body .= "
            <h3>Integrantes del Grupo:</h3>
            <table>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                </tr>";
    
    foreach ($matriculados as $matriculado) {
        $email_body .= "
                <tr>
                    <td>{$matriculado['matricula']}</td>
                    <td>{$matriculado['nombre_completo']}</td>
                </tr>";
    }
    
    $email_body .= "
            </table>";
}

// Add rejection reason if applicable
if ($reserva['estado'] == 'rechazada' && !empty($reserva['motivo_rechazo'])) {
    $email_body .= "
            <h3>Motivo del Rechazo:</h3>
            <p>{$reserva['motivo_rechazo']}</p>";
}

// Add specific information for cancelled reservations
if ($reserva['estado'] == 'cancelada') {
    $email_body .= "
            <div class='important-notice'>
                <h3><i class='fas fa-exclamation-triangle'></i> Información Importante:</h3>
                <p><strong>Motivo de la cancelación:</strong> No se presentaron los comprobantes de pago dentro del plazo establecido de 48 horas desde la creación de la reserva.</p>
                <p>Para futuras reservas, recuerde que es <strong>obligatorio</strong> presentar los comprobantes de pago dentro de las primeras 48 horas para confirmar su reserva.</p>
            </div>
            <div class='contact-info'>
                <h3><i class='fas fa-phone'></i> Información de Contacto:</h3>
                <p>Para cualquier consulta o duda, puede comunicarse con el Colegio de Ingenieros:</p>
                <ul>
                    <li><strong>Teléfono:</strong> 3704043114</li>
                    <li><strong>Correo electrónico:</strong> ingenierosformosa@gmail.com</li>
                </ul>
            </div>";
}

// Add specific information for reservations given "baja"
if ($reserva['estado'] == 'baja') {
    $email_body .= "
            <div class='important-notice'>
                <h3><i class='fas fa-info-circle'></i> Información sobre la Baja:</h3>
                <p>Su reserva ha sido dada de baja por la administración del Colegio de Ingenieros.</p>
                <p>Esta decisión puede deberse a diversos motivos administrativos o cambios en la disponibilidad del salón.</p>
            </div>
            <div class='contact-info'>
                <h3><i class='fas fa-phone'></i> Información de Contacto:</h3>
                <p>Para obtener más información sobre los motivos de la baja o para realizar una nueva reserva, puede comunicarse con nosotros:</p>
                <ul>
                    <li><strong>Teléfono:</strong> 3704043114</li>
                    <li><strong>Correo electrónico:</strong> ingenierosformosa@gmail.com</li>
                </ul>
                <p><em>Lamentamos cualquier inconveniente que esto pueda ocasionar.</em></p>
            </div>";
}

// Close the email body HTML
$email_body .= "
            <p>Si tiene alguna consulta, por favor no dude en contactarnos.</p>
            
            <p>Saludos cordiales,<br>
            Colegio de Ingenieros</p>
        </div>
        <div class='footer'>
            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
        </div>
    </div>
</body>
</html>
";

// Create plain text alternative
$email_alt_body = "Confirmación de Reserva\n\n" .
                 "Estimado/a {$reserva['nombre']} {$reserva['apellido']},\n\n" .
                 "Le confirmamos que su reserva ha sido {$reserva['estado']}.\n\n" .
                 "Detalles de la Reserva:\n" .
                 "Fecha del Evento: {$fecha_formateada}\n" .
                 "Horario: {$reserva['hora_inicio']} - {$reserva['hora_fin']}\n" .
                 "Tipo de Uso: {$reserva['tipo_uso']}\n" .
                 "Monto: $ {$reserva['monto']}\n" .
                 "Estado: {$reserva['estado']}\n\n";

// Add the rejection reason to the text version if applicable
if ($reserva['estado'] == 'rechazada' && !empty($reserva['motivo_rechazo'])) {
    $email_alt_body .= "Motivo del Rechazo: {$reserva['motivo_rechazo']}\n\n";
}

// Add specific information for cancelled reservations (text version)
if ($reserva['estado'] == 'cancelada') {
    $email_alt_body .= "INFORMACIÓN IMPORTANTE:\n" .
                      "Motivo de la cancelación: No se presentaron los comprobantes de pago dentro del plazo establecido de 48 horas desde la creación de la reserva.\n" .
                      "Para futuras reservas, recuerde que es obligatorio presentar los comprobantes de pago dentro de las primeras 48 horas para confirmar su reserva.\n\n" .
                      "INFORMACIÓN DE CONTACTO:\n" .
                      "Para cualquier consulta o duda, puede comunicarse con el Colegio de Ingenieros:\n" .
                      "- Teléfono: 3704043114\n" .
                      "- Correo electrónico: ingenierosformosa@gmail.com\n";
}

// Add specific information for reservations given "baja" (text version)
if ($reserva['estado'] == 'baja') {
    $email_alt_body .= "INFORMACIÓN SOBRE LA BAJA:\n" .
                      "Su reserva ha sido dada de baja por la administración del Colegio de Ingenieros.\n" .
                      "Esta decisión puede deberse a diversos motivos administrativos o cambios en la disponibilidad del salón.\n\n" .
                      "INFORMACIÓN DE CONTACTO:\n" .
                      "Para obtener más información sobre los motivos de la baja o para realizar una nueva reserva, puede comunicarse con nosotros:\n" .
                      "- Teléfono: 3704043114\n" .
                      "- Correo electrónico: ingenierosformosa@gmail.com\n";
}

$email_alt_body .= "Si tiene alguna consulta, por favor no dude en contactarnos.\n\n" .
                  "Saludos cordiales,\n" .
                  "Equipo de Ingenieros";

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 2;                      //Enable verbose debug output (2 for detailed debug)
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = $GLOBALS['correoHostingerHost'];        //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $GLOBALS['correoHostingerUser'];        //SMTP username
    $mail->Password   = $GLOBALS['correoHostingerPass'];        //SMTP password
    
    // For SMTPSecure, we need to handle the constant separately
    if ($GLOBALS['correoHostingerSecure'] === 'PHPMailer::ENCRYPTION_SMTPS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else if ($GLOBALS['correoHostingerSecure'] === 'PHPMailer::ENCRYPTION_STARTTLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = $GLOBALS['correoHostingerSecure'];
    }
    
    $mail->Port       = $GLOBALS['correoHostingerPort'];        //TCP port to connect to

    //Recipients
    $mail->setFrom($GLOBALS['correoHostingerFrom'], 'Colegio de Ingenieros - Reservas');
    $mail->addAddress($reserva['email'], $reserva['nombre'] . ' ' . $reserva['apellido']);     //Add the user as recipient
    
    // Add a copy to the admin (optional)
    // $mail->addCC('admin@example.com', 'Administración');

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Reserva #' . $reserva['id'] . ' - ' . ucfirst($reserva['estado']);
    $mail->Body    = $email_body;
    $mail->AltBody = $email_alt_body;

    $mail->send();
    echo 'El correo ha sido enviado correctamente.';
} catch (Exception $e) {
    echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
}