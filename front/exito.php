<?php
/**
 * -----------------------------------------------------------------------------
 * DASHBOARD PRINCIPAL (HOME)
 * -----------------------------------------------------------------------------
 * Página de bienvenida tras loguearse.
 */
session_start();
 
// --- SEGURIDAD DE SESIÓN ---
// Verificamos si la variable de sesión existe.
// Si NO existe (!isset), significa que el usuario no se ha logueado.
// Lo expulsamos inmediatamente a "intruso.php".
if (!isset($_SESSION['usuario'])) {
    header("Location: intruso.php");
    exit;
}
?>
 
<!doctype html>
<html lang="es">
	<head>
		<title>AdminViews</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/png" href="img/adminviews_favicon.png">
		<link rel="stylesheet" href="css/estilo.css">
	</head>
	<body>
		<header>
			<img src="img/adminviews.png" class="logo-admin" alt="Logo AdminViews">
			<a href="index.php" class="btn-inicio"><img src="img/iconologout.png" alt="Volver al Inicio" title='Salir'></a> 
		</header>
		<main>
			<section class="cards-container">
				<article class="card">
					<div class="card-header">
						<img src="img/iconoseries.png" alt="Icono Series" class="card-icon">
						<h3>Lista de Series</h3>
					</div>
					<div class="card-body">
						<p>Accede a todas las series que tienes añadidas a tu lista de series pendientes, viendo o vistas.</p>
						<a href="series.php" class="btn-flecha">
						<img src="img/flechaderecha.png" alt="Ir">
						</a>
					</div>
				</article>
				<article class="card">
					<div class="card-header">
						<img src="img/iconopelicula.png" alt="Icono Películas" class="card-icon">
						<h3>Lista de Películas</h3>
					</div>
					<div class="card-body">
						<p>Accede a todas las películas que tienes añadidas a tu lista de películas pendientes o vistas.</p>
						<a href="peliculas.php" class="btn-flecha">
						<img src="img/flechaderecha.png" alt="Ir">
					</a>
					</div>
				</article>
			</section>
<?php include "inc/piedepagina.php" ?>
