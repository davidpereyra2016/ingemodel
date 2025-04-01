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

require_once("../../utils/lib/phpMailer/PHPMailer.php");
require_once("../../utils/lib/phpMailer/SMTP.php");
require_once("../../utils/lib/phpMailer/Exception.php");

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
    $mail->setFrom($GLOBALS['correoHostingerFrom'], 'Correo de prueba1');
    $mail->addAddress('davidpereyra2013.dp@gmail.com', 'David');     //Add a recipient
   
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Prueba de correo subject1';
    $mail->Body    = 'Prueba de correo body 1';
    $mail->AltBody = 'Prueba de correo alt body 1';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}