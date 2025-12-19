<?php
session_start();

// Verificamos si la variable de sesión existe
// Si NO existe (!isset), redirigimos al login
if (!isset($_SESSION['usuario'])) {
    header("Location: intruso.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área Privada</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
    <p>Has entrado con éxito al área segura.</p>

    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
