<?php
session_start();

// 1. IMPORTANTE: La conexión debe ir al principio para poder usarla en las validaciones
require_once 'db.php'; 

$nombre = $_POST['nombrecompleto'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
$correo = $_POST['email'];

$errores = [];

// --- VALIDAR SI EL USUARIO YA EXISTE ---
// Escapamos el dato para evitar errores si contiene comillas o ataques SQL
$usuario_seguro = mysqli_real_escape_string($conexion, $usuario);

$sql_check = "SELECT * FROM usuario WHERE usuario = '$usuario_seguro'";
$res_check = mysqli_query($conexion, $sql_check);

if (mysqli_num_rows($res_check) > 0) {
    $errores['usuario'] = "Este usuario ya está ocupado. Elige otro.";
}
    
// --- VALIDAR QUE EL EMAIL SEA VALIDO ---
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = "El correo no es válido";
}

// --- VALIDAR QUE LA CONTRASEÑA CUMPLA CON LOS REQUISITOS ---
if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,16}$/', $contrasena)) {
    $errores['pass'] = "La contraseña debe tener 8-16 chars, 1 Mayúscula y 1 Símbolo";
}

// SI HAY ERRORES
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_viejos'] = $_POST;
    
    header("Location: index.php"); 
    exit;
}

// SI TODO ESTÁ BIEN (Insertar en BD)
$passHash = password_hash($contrasena, PASSWORD_DEFAULT);
$sql = "INSERT INTO usuario (usuario, contrasena, nombre, correo) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $usuario, $passHash, $nombre, $correo);

if($stmt->execute()){
    header("Location: index.php?registro=ok"); 
} else {
    echo "Error BD: " . $conexion->error;
}
?>
