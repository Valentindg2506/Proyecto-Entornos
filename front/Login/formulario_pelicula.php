<?php
	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: INICIALIZACIÓN
	 * -------------------------------------------------------------------------
	 */
	session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="stylesheet" href="style/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<title>Añadir Película</title>
	
	<style>
		.container {
			min-height: auto;
			padding: 40px 0;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.form-container {
			position: relative; 
			top: auto; left: auto;
			width: 100%; height: auto;
			transform: none; opacity: 1; z-index: 1;
		}
		/* Estilo compartido para selects */
		.input-group select {
			background-color: #eee;
			border: none;
			padding: 12px 15px;
			padding-left: 40px;
			width: 100%;
			font-family: 'Inter', sans-serif;
			outline: none;
			color: #333;
			cursor: pointer;
		}
	</style>
</head>

<body class="login-page">
	
	<div class="container">
		<div class="form-container">
			<form action="guardar_pelicula.php" method="POST">
				
				<h1>Nueva Película</h1>
				<p>Completa los detalles para añadir una película.</p>
				
				<div class="input-group">
					<input type="text" name="nombre" placeholder="Título de la película" required />
					<i class="fa-solid fa-film"></i>
				</div>

				<div class="input-group">
					<input type="text" name="comentario" placeholder="Comentario o reseña breve" />
					<i class="fa-solid fa-comment"></i>
				</div>

				<div class="input-group">
					<select name="prioridad" required>
						<option value="" disabled>Selecciona Prioridad</option>
						<option value="Alta">Alta</option>
						<option value="Media" selected>Media</option>
						<option value="Baja">Baja</option>
					</select>
					<i class="fa-solid fa-layer-group"></i>
				</div>

				<div class="input-group">
					<select name="estado" required>
						<option value="Por ver" selected>Por ver</option>
						<option value="Vista">Vista</option>
					</select>
					<i class="fa-solid fa-eye"></i>
				</div>

				<button type="submit">Guardar Película</button>
				
				<a href="index.php" style="margin-top: 15px; font-size: 12px;">
					<i class="fa-solid fa-arrow-left"></i> Volver al inicio
				</a>

			</form>
		</div>
	</div>

</body>
</html>
