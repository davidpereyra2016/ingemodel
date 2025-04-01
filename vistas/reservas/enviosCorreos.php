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
$base_path = realpath(__DIR__ . '/../../');
require_once($base_path . "/utils/lib/phpMailer/PHPMailer.php");
require_once($base_path . "/utils/lib/phpMailer/SMTP.php"); 
require_once($base_path . "/utils/lib/phpMailer/Exception.php");

// Verify that we have the reservation data
if (!isset($reserva) || !is_array($reserva)) {
    die("Error: No se recibieron los datos de la reserva.");
}

// Create a formatted date
$fecha_formateada = date('d/m/Y', strtotime($reserva['fecha_evento']));

// Create an HTML email body
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #3498db; color: white; padding: 10px 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Confirmación de Reserva</h2>
        </div>
        <div class='content'>
            <p>Estimado/a <strong>{$reserva['nombre']} {$reserva['apellido']}</strong>,</p>
            
            <p>Le confirmamos que su reserva ha sido <strong>{$reserva['estado']}</strong>.</p>
            
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

// Close the email body HTML
$email_body .= "
            <p>Si tiene alguna consulta, por favor no dude en contactarnos.</p>
            
            <p>Saludos cordiales,<br>
            Equipo de IngeModel</p>
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

$email_alt_body .= "Si tiene alguna consulta, por favor no dude en contactarnos.\n\n" .
                  "Saludos cordiales,\n" .
                  "Equipo de IngeModel";

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
    $mail->setFrom($GLOBALS['correoHostingerFrom'], 'IngeModel - Reservas');
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