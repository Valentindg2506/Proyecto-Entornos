<?php
session_start();
require_once 'db.php'; // Usamos tu archivo de conexión nuevo

// Verificar que llegan datos
if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
    
    $usuario = $_POST['usuario'];
    $pass_ingresada = $_POST['contrasena'];

    // 1. Buscamos SOLO por usuario (evitando inyección SQL)
    $sql = "SELECT id, usuario, contrasena FROM usuario WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    
    $resultado = $stmt->get_result();

    // 2. Si el usuario existe, verificamos la contraseña
    if ($fila = $resultado->fetch_assoc()) {
        
        $hash_guardado = $fila['contrasena'];

        // 3. Comparamos la pass del form con el hash de la BD
        if (password_verify($pass_ingresada, $hash_guardado)) {
            // Login CORRECTO
            $_SESSION['usuario'] = $fila['usuario']; // Guardamos datos útiles
            $_SESSION['id_usuario'] = $fila['id'];
            
            header("Location: exito.php");
            exit; // Siempre pon exit después de un header
        } else {
            // Contraseña INCORRECTA
            header("Location: index.php?error=1");
            exit;
        }

    } else {
        // Usuario NO encontrado
        header("Location: index.php?error=1");
        exit;
    }

    $stmt->close();
    $conexion->close();

} else {
    // Si intentan entrar directo al archivo sin enviar datos
    header("Location: index.php");
}
?>
