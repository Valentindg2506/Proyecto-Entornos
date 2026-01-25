<?php
/**
 * -----------------------------------------------------------------------------
 * CONTROLADOR: GUARDAR CONTENIDO
 * -----------------------------------------------------------------------------
 * Recibe los datos de los formularios (película o serie) y los guarda en la BD.
 */
session_start();
require_once '../inc/db.php';

// 1. Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../intruso.php");
    exit;
}

// 2. Verificar datos mínimos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $usuario_id = $_SESSION['id_usuario'];
    $tipo = $_POST['tipo'] ?? 'pelicula'; // 'pelicula' o 'serie'
    
    $titulo = $_POST['nombre'];
    $comentario = $_POST['comentario'] ?? '';
    // Mapeo simple: Si viene del formulario 'Por ver' se guarda como 'Por_ver' en BD
    $estado_raw = $_POST['estado'] ?? 'Por_ver';
    $estado = ($estado_raw == 'Por ver') ? 'Por_ver' : $estado_raw;
    
    $prioridad = $_POST['prioridad'] ?? 'Media';
    $img_url = $_POST['imagen_url'] ?? '';

    // 3. Insertar en BD
    $sql = "INSERT INTO contenido (usuario_id, titulo, comentario, estado, tipo, nivel_prioridad, imagen_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issssss", $usuario_id, $titulo, $comentario, $estado, $tipo, $prioridad, $img_url);
    
    if ($stmt->execute()) {
        // Redirección según el tipo
        if ($tipo === 'serie') {
            header("Location: ../series.php");
        } else {
            header("Location: ../peliculas.php");
        }
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
    
    $stmt->close();
    $conexion->close();
    
} else {
    // Si intentan entrar directo
    header("Location: ../exito.php");
}
?>
