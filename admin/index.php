<?php
/**
 * PÁGINA PRINCIPAL DEL ADMIN (DASHBOARD)
 */
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../front/intruso.php");
    exit;
}
require_once "inc/db.php";

// 1. OBTENER DATOS REALES (KPIs)
// -------------------------------------------------------------------------

// Usuarios Totales
$sqlUsers = "SELECT COUNT(*) as total FROM usuario";
$resUsers = $conexion->query($sqlUsers);
$totalUsuarios = $resUsers->fetch_assoc()['total'];

// Series Totales
$sqlSeries = "SELECT COUNT(*) as total FROM contenido WHERE tipo = 'serie'";
$resSeries = $conexion->query($sqlSeries);
$totalSeries = $resSeries->fetch_assoc()['total'];

// Películas Totales
$sqlPelis = "SELECT COUNT(*) as total FROM contenido WHERE tipo = 'pelicula'";
$resPelis = $conexion->query($sqlPelis);
$totalPelis = $resPelis->fetch_assoc()['total'];

// Nuevos Hoy (Como no hay fecha_registro, mostramos 0 o simulamos para no romper el diseño)

$nuevosHoy = 0; 


// 2. DATOS PARA EL GRÁFICO (Distribución de Estados)
// -------------------------------------------------------------------------
// Queremos saber cuántos items hay 'Por_ver', 'Viendo', 'Vistas'
$sqlChart = "SELECT estado, COUNT(*) as cantidad FROM contenido GROUP BY estado";
$resChart = $conexion->query($sqlChart);

$dataChart = ['Por_ver' => 0, 'Viendo' => 0, 'Vistas' => 0];																																																																																										
while($row = $resChart->fetch_assoc()) {
    $dataChart[$row['estado']] = $row['cantidad'];
}

// 3. TOP 10 SERIES MÁS VISTAS (Ranking)
// -------------------------------------------------------------------------
$sqlTopSeries = "SELECT titulo, COUNT(*) as total FROM contenido WHERE tipo = 'serie' AND estado = 'Vistas' GROUP BY titulo ORDER BY total DESC LIMIT 10";
$resTopSeries = $conexion->query($sqlTopSeries);

// 4. TOP 10 PELÍCULAS MÁS VISTAS (Ranking)
// -------------------------------------------------------------------------
$sqlTopPelis = "SELECT titulo, COUNT(*) as total FROM contenido WHERE tipo = 'pelicula' AND estado = 'Vistas' GROUP BY titulo ORDER BY total DESC LIMIT 10";
$resTopPelis = $conexion->query($sqlTopPelis);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AdminViews</title>
    <!-- Estilos -->
    <link rel="stylesheet" href="css/estilo.css">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>										

    <div class="admin-wrapper">
        
        <!-- SIDEBAR -->
        <?php include "inc/sidebar.php"; ?>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">

            <header class="page-header">
                <h1>Panel de Control</h1>
                <p>Bienvenido al sistema de administración de AdminViews.</p>
            </header>

            <!-- TARJETAS DE ESTADÍSTICAS -->
            <section class="stats-grid">
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $totalUsuarios; ?></h3>
                        <p>Usuarios</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $totalSeries; ?></h3>
                        <p>Series</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $totalPelis; ?></h3>
                        <p>Películas</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-film"></i>
                    </div>
                </div>


            </section>

            <!-- SECCIÓN DE ACTIVIDAD (GRÁFICO) -->
            <section class="content-section">
                <div class="section-header">
                    <h2>Estado del Contenido Global</h2>
                    <!-- Botón decorativo -->
                    <button style="background:none; border:none; cursor:pointer; color:#777;"><i class="fas fa-filter"></i></button>
                </div>
                
                <!-- Contenedor del gráfico -->
                <div style="height: 350px; width: 100%; position: relative;">
                    <canvas id="myChart"></canvas>
                </div>
            </section>

            <!-- NUEVA FILA DE RANKINGS (TOP SERIES Y PELIS) -->
            <div class="charts-row">
                
                <!-- TOP SERIES -->
                <section class="content-section" style="flex:1;">
                    <div class="section-header">
                        <h2>Top 10 Series Más Vistas</h2>
                    </div>
                    
                    <ol class="ranking-list">
                        <?php 
                        $pos = 1;
                        if ($resTopSeries->num_rows > 0) {
                            while($row = $resTopSeries->fetch_assoc()) {
                                $badgeClass = 'default';
                                if($pos == 1) $badgeClass = 'gold';
                                elseif($pos == 2) $badgeClass = 'silver';
                                elseif($pos == 3) $badgeClass = 'bronze';
                                
                                echo "<li class='ranking-item'>";
                                echo "<span class='ranking-pos $badgeClass'>$pos</span>";
                                echo "<div class='ranking-info'>";
                                echo "<strong>" . htmlspecialchars($row['titulo']) . "</strong>";
                                echo "<small>" . $row['total'] . " visualizaciones</small>";
                                echo "</div>";
                                echo "</li>";
                                $pos++;
                            }
                        } else {
                            echo "<p style='color:#777; padding:10px;'>No hay datos suficientes.</p>";
                        }
                        ?>
                    </ol>
                </section>

                <!-- TOP PELÍCULAS -->
                <section class="content-section" style="flex:1;">
                    <div class="section-header">
                        <h2>Top 10 Películas Más Vistas</h2>
                    </div>
                    
                    <ol class="ranking-list">
                        <?php 
                        $pos = 1;
                        if ($resTopPelis->num_rows > 0) {
                            while($row = $resTopPelis->fetch_assoc()) {
                                $badgeClass = 'default';
                                if($pos == 1) $badgeClass = 'gold';
                                elseif($pos == 2) $badgeClass = 'silver';
                                elseif($pos == 3) $badgeClass = 'bronze';
                                
                                echo "<li class='ranking-item'>";
                                echo "<span class='ranking-pos $badgeClass'>$pos</span>";
                                echo "<div class='ranking-info'>";
                                echo "<strong>" . htmlspecialchars($row['titulo']) . "</strong>";
                                echo "<small>" . $row['total'] . " visualizaciones</small>";
                                echo "</div>";
                                echo "</li>";
                                $pos++;
                            }
                        } else {
                            echo "<p style='color:#777; padding:10px;'>No hay datos suficientes.</p>";
                        }
                        ?>
                    </ol>
                </section>

            </div>

        </main>

    </div>

    <!-- Script del Gráfico -->
    <script>
        // --- GRÁFICO PRINICPAL (ESTADOS) ---
        const ctx = document.getElementById('myChart');
        const datos = {
            porVer: <?php echo $dataChart['Por_ver']; ?>,
            viendo: <?php echo $dataChart['Viendo']; ?>,
            vistas: <?php echo $dataChart['Vistas']; ?>
        };

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Por Ver', 'Viendo', 'Vistas'],
                datasets: [{
                    label: 'Contenidos',
                    data: [datos.porVer, datos.viendo, datos.vistas],
                    backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 192, 0.6)'],
                    borderColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(75, 192, 192)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    </script>

        </main>

    </div>

</body>
</html>
