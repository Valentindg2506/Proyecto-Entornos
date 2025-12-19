<?php
// Capturamos la dirección IP del cliente
$ip_intruso = $_SERVER['REMOTE_ADDR'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCESO DENEGADO</title>
    <style>
        body {
            background-color: #000;
            color: #ff0000;
            font-family: 'Courier New', Courier, monospace;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        .alerta {
            font-size: 5rem;
            font-weight: bold;
            text-transform: uppercase;
            animation: parpadeo 0.2s infinite alternate; /* Más rápido para más estrés */
        }

        .mensaje {
            font-size: 1.5rem;
            margin-top: 20px;
        }

        .ip-display {
            font-size: 2.5rem;
            color: #fff;
            background-color: #ff0000;
            padding: 10px 20px;
            margin-top: 20px;
            border: 2px solid white;
        }

        .icono {
            font-size: 8rem;
            margin-bottom: 20px;
        }

        @keyframes parpadeo {
            from { opacity: 1; text-shadow: 0 0 10px red; }
            to { opacity: 0.5; text-shadow: 0 0 30px darkred; transform: scale(1.02); }
        }
    </style>
</head>
<body>

    <div class="icono">🚫</div>
    <div class="alerta">¡SOS UN INTRUSO!</div>
    
    <div class="mensaje">HEMOS RASTREADO TU UBICACIÓN</div>
    
    <div class="ip-display">IP: <?php echo $ip_intruso; ?></div>

</body>
</html>
