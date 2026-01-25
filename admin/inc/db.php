<?php
/**
 * 
 * ADMIN - CONEXIÓN A BASE DE DATOS
 * Reutilizamos la configuración del front para consistencia.
 * 
 */

$host = "localhost";
$user = "AdminViews";
$pass = "AdminViews123$";
$db   = "AdminViews";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error Admin - Conexión fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
