<?php
/**
 * -----------------------------------------------------------------------------
 * ARCHIVO DE CONEXIÓN A BASE DE DATOS
 * -----------------------------------------------------------------------------
 * Este archivo establece la comunicación entre PHP y MySQL.
 * Se incluye en todos los archivos que necesiten leer o guardar datos.
 */

$host = "localhost";        // Servidor (suele ser localhost en XAMPP)
$user = "AdminViews";       // Usuario de la BD
$pass = "AdminViews123$";   // Contraseña del usuario
$db   = "AdminViews";       // Nombre exacto de la base de datos

// Paso 1: Intentar conectar
$conexion = new mysqli($host, $user, $pass, $db);

// Paso 2: Verificar si hubo error
if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

// (Opcional) Forzar codificación UTF-8 para evitar problemas con tildes y ñ
$conexion->set_charset("utf8");
?>
