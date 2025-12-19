<?php
session_start();

// Verificamos si la variable de sesión existe
// Si NO existe (!isset), redirigimos al login
if (!isset($_SESSION['usuario'])) {
    header("Location: intruso.php");
    exit;
}
?>

<!doctype html>
<html lang="es">
    <head>
        <title>AdminViews</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/estilo.css">
    </head>
    <body>
        <header>
        	<img src="../img/adminviews.png" class="logo-admin" alt="Logo AdminViews">
	        <a href="../Login/exito" class="btn-inicio"><img src="../img/iconologout.png" alt="Volver al Inicio"></a> 
        </header>
        <main>
        </main>
        <footer>
        </footer>
	</body>
</html>
