<?php
	// Primero cogemos la info que viene del formulario
	$nombrecompleto = $_POST['nombrecompleto'];
	$usuario = $_POST['usuario'];
	$contrasena = ($_POST['contrasena']);
	$email = $_POST['email'];
	// Y luego metemos esa información en la base de datos

	$host = "localhost";
	$user = "CRM";
	$pass = "Crm2526$";
	$db   = "CRM";

	$conexion = new mysqli($host, $user, $pass, $db);

	// Metemos los datos en la base de datos
	$sql = "
		INSERT INTO usuarios (usuario, contrasena, nombrecompleto, email) VALUES (
		'".$usuario."',
		'".$contrasena."',
		'".$nombrecompleto."',
		'".$email."'
		);
	";
	$conexion->query($sql);

	$conexion->close();

	// Y redirigimos al usuario a la página de inicio
	header("Location: login.php");
?>
