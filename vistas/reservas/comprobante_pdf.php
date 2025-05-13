<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Reserva #<?php echo $reserva['id']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header img {
            max-width: 100px;
            height: auto;
        }
        h1 {
            font-size: 18px;
            margin: 5px 0;
            color: #006633;
        }
        h2 {
            font-size: 16px;
            margin: 10px 0;
            color: #006633;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        p {
            margin: 5px 0;
        }
        .info-section {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .info-section h3 {
            margin-top: 0;
            font-size: 14px;
            color: #006633;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 100px;
            height: auto;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 5px;
        }
        .disclaimer {
            margin-top: 20px;
            font-size: 10px;
            font-style: italic;
            text-align: center;
        }
        .documento-oficial {
            position: relative;
        }
        .sello {
            position: absolute;
            top: 150px;
            right: 50px;
            opacity: 0.2;
            transform: rotate(-20deg);
            font-size: 18px;
            color: #006633;
            border: 2px solid #006633;
            border-radius: 5px;
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body class="documento-oficial">
    <div class="sello">COMPROBANTE OFICIAL</div>

    <div class="header">
        <!-- <img src="../../assets/img/logo-2.png" alt="Logo"> -->
        <h1>Colegio Público de Ingenieros de Formosa</h1>
        <p>COMPROBANTE DE RESERVA</p>
        <p><strong>Código de Reserva:</strong> <?php echo $reserva['codigo_unico']; ?></p>
        <p><strong>Fecha de emisión:</strong> <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <div class="info-section">
        <h3><i class="bi bi-file-earmark-text"></i> Información de la Reserva</h3>
        <div class="info-row">
            <span class="info-label">Número de Reserva:</span>
            <span class="info-value">#<?php echo $reserva['id']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <?php
                $badgeClass = '';
                $estadoText = '';
                switch ($reserva['estado']) {
                    case 'pendiente':
                        $badgeClass = 'badge-warning';
                        $estadoText = 'Pendiente';
                        break;
                    case 'aprobada':
                        $badgeClass = 'badge-success';
                        $estadoText = 'Aprobada';
                        break;
                    case 'rechazada':
                        $badgeClass = 'badge-danger';
                        $estadoText = 'Rechazada';
                        break;
                    case 'cancelada':
                        $badgeClass = 'badge-secondary';
                        $estadoText = 'Cancelada';
                        break;
                }
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo $estadoText; ?></span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha del Evento:</span>
            <span class="info-value"><?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Horario:</span>
            <span class="info-value"><?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipo de Uso:</span>
            <span class="info-value"><?php echo $reserva['tipo_uso']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Motivo de Uso:</span>
            <span class="info-value"><?php echo $reserva['motivo_de_uso']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Solicitud:</span>
            <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($reserva['fecha_solicitud'])); ?></span>
        </div>
    </div>

    <div class="info-section">
        <h3><i class="bi bi-person"></i> Datos del Solicitante</h3>
        <div class="info-row">
            <span class="info-label">Nombre:</span>
            <span class="info-value"><?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Matrícula:</span>
            <span class="info-value"><?php echo $reserva['matricula']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value"><?php echo $reserva['email']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Teléfono:</span>
            <span class="info-value"><?php echo $reserva['telefono']; ?></span>
        </div>
    </div>

    <div class="info-section">
        <h3><i class="bi bi-cash"></i> Información de Pago</h3>
        <div class="info-row">
            <span class="info-label">Monto Total:</span>
            <span class="info-value">$<?php echo number_format($reserva['monto'], 2, ',', '.'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Anticipo:</span>
            <span class="info-value">
                <?php if ($reserva['anticipo_pagado']): ?>
                    <span style="color: #28a745;">Pagado ✓</span>
                <?php else: ?>
                    <span style="color: #dc3545;">Pendiente ✗</span>
                <?php endif; ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Saldo:</span>
            <span class="info-value">
                <?php if ($reserva['saldo_pagado']): ?>
                    <span style="color: #28a745;">Pagado ✓</span>
                <?php else: ?>
                    <span style="color: #dc3545;">Pendiente ✗</span>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <!-- 
    <div class="qr-code">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode('Reserva#' . $reserva['id'] . '-' . $reserva['codigo_unico']); ?>" alt="QR Code">
        <p>Código de verificación</p>
    </div>
    -->

    <div class="signature">
        <div class="signature-line">
            <p>Firma del Solicitante</p>
        </div>
        <div class="signature-line">
            <p>Sello y Firma Autoridad</p>
        </div>
    </div>

    <div class="disclaimer">
        <p>Este comprobante es válido con firma y sello de la autoridad del Colegio Público de Ingenieros de Formosa.</p>
        <p>Para consultas o modificaciones comuníquese al teléfono: (0370) 4436677 o por email a info@cpiformosa.org.ar</p>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente por el sistema de gestión de reservas del Colegio Público de Ingenieros de Formosa</p>
        <p>Este comprobante tiene carácter oficial y sirve como constancia de la reserva realizada.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
