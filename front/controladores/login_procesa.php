<?php
/**
 * -----------------------------------------------------------------------------
 * CONTROLADOR: PROCESAR LOGIN
 * -----------------------------------------------------------------------------
 * Recibe los datos del formulario de inicio de sesión y verifica credenciales.
 * En el caso de que el usuario sea admin lo lleva al admin panel.
 * En el caso contrario lo lleva por la ruta normal.
 */

session_start();
require_once '../inc/db.php'; // Conexión a BD

// ¿El usuario envió el formulario?
if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
    
    $usuario = $_POST['usuario'];
    $pass_ingresada = $_POST['contrasena'];

    // 1. Buscamos SOLO por usuario (evitando inyección SQL con prepare)
    $sql = "SELECT id, usuario, contrasena FROM usuario WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario); // "s" = string
    $stmt->execute();
    
    $resultado = $stmt->get_result();

    // 2. Si el usuario existe, extraemos sus datos
    if ($fila = $resultado->fetch_assoc()) {
        
        $hash_guardado = $fila['contrasena']; // El hash encriptado de la BD

        // 3. Comparamos la pass escrita (texto plano) con el hash (encriptado)
        if (password_verify($pass_ingresada, $hash_guardado)) {
        
            // LOGIN ÉXITOSO: Guardamos datos en sesión para recordarlo
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['id_usuario'] = $fila['id'];
            
			## LOGICA DE REDIRECCION MANUAL ##
            if ($fila['usuario'] === 'Admin1') {  // SI EL USUARIO ES ADMIN
                header("Location: ../../admin/index.php"); // LO LLEVA A AL PANEL DE ADMIN
            } else {
                header("Location: ../exito.php"); // Ruta normal
            }
            
            exit;
        } else {
            // Contraseña mal
            header("Location: ../index.php?error=1");
            exit;
        }

    } else {
        // Usuario no existe
        header("Location: ../index.php?error=1");
        exit;
    }

    $stmt->close();
    $conexion->close();

} else {
    // Intento de acceso directo sin formulario
    header("Location: ../index.php");
}
?>
