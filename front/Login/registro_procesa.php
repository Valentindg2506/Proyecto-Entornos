<?php
	// Primero cogemos la info que viene del formulario
	$nombre = $_POST['nombrecompleto'];
	$usuario = $_POST['usuario'];
	$contrasena = ($_POST['contrasena']);
	$correo = $_POST['email'];
	// Y luego metemos esa información en la base de datos

	$host = "localhost";
	$user = "AdminViews";
	$pass = "AdminViews123$";
	$db   = "AdminViews";

	$conexion = new mysqli($host, $user, $pass, $db);

	// Metemos los datos en la base de datos
	$sql = "
		INSERT INTO usuario (usuario, contrasena, nombre, correo) VALUES (
		'".$usuario."',
		'".$contrasena."',
		'".$nombre."',
		'".$correo."'
		);
	";
	$conexion->query($sql);

	$conexion->close();

	// Y redirigimos al usuario a la página de inicio
	header("Location: index.php");
?>
