<aside class="sidebar">
    <div class="sidebar-header">
        <img src="img/adminviews.png" alt="Logo Admin" class="sidebar-logo">
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="usuarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            <li>
                <a href="series.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'series.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tv"></i> Series
                </a>
            </li>
            <li>
                <a href="peliculas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'peliculas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-film"></i> Películas
                </a>
            </li>
            <!-- Separador -->
            <li class="nav-separator"></li>
            <li>
                <a href="../front/index.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <p>&copy; 2025 AdminViews</p>
    </div>
</aside>
