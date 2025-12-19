<?php
session_start();

$nombre = $_POST['nombrecompleto'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
$correo = $_POST['email'];

$errores = [];

// VALIDACIONES
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = "El correo no es válido";
}

if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,16}$/', $contrasena)) {
    $errores['pass'] = "La contraseña debe tener 8-16 chars, 1 Mayúscula y 1 Símbolo";
}

// SI HAY ERRORES
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_viejos'] = $_POST;
    
    // CORRECCIÓN: Quitamos el ?error=1 para no activar la alerta del Login
    header("Location: index.php"); 
    exit;
}

// SI TODO ESTÁ BIEN
require_once 'db.php'; 

$passHash = password_hash($contrasena, PASSWORD_DEFAULT);
$sql = "INSERT INTO usuario (usuario, contrasena, nombre, correo) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $usuario, $passHash, $nombre, $correo);

if($stmt->execute()){
    // Opcional: añade ?registro=ok para mostrar un mensaje de éxito si quieres
    header("Location: index.php?registro=ok"); 
} else {
    echo "Error BD: " . $conexion->error;
}
?>
