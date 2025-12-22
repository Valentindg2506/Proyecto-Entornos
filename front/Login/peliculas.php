<?php
	session_start();
 
	// Verificamos si la variable de sesión existe
	// Si NO existe (!isset), redirigimos al login
	if (!isset($_SESSION['usuario'])) {
		header("Location: intruso.php");
		exit;
	}
?>

<?php include "../inc/cabecera.php" ?>


			
<?php include "../inc/piedepagina.php" ?>





