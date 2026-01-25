<?php
/**
 * PÁGINA DE GESTIÓN DE SERIES
 */
session_start();
require_once "inc/db.php";

// Consulta de series
$sql = "SELECT * FROM contenido WHERE tipo = 'serie'";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series - AdminViews</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include "inc/sidebar.php"; ?>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Catálogo de Series</h1>
                <p>Lista completa de series en la plataforma</p>
                <button class="btn-primary-admin"><i class="fas fa-plus"></i> Nueva Serie</button>
            </header>

            <section class="content-section">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Carátula</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Puntuación</th>
                                <th>Prioridad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado->num_rows > 0): ?>
                                <?php while($row = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if($row['imagen_url']): ?>
                                                <img src="<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="Cover" class="img-tabla-mini">
                                            <?php else: ?>
                                                <div class="no-img-mini"><i class="fas fa-image"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($row['titulo']); ?></strong></td>
                                        <td><span class="badge status-<?php echo strtolower($row['estado']); ?>"><?php echo $row['estado']; ?></span></td>
                                        <td><?php echo $row['puntuacion']; ?>/5</td>
                                        <td><?php echo $row['nivel_prioridad']; ?></td>
                                        <td>
                                            <a href="#" class="btn-action edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn-action delete"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center;">No hay series registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
