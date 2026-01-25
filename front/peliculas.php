<?php
	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: SESIÓN Y CONFIGURACIÓN
	 * -------------------------------------------------------------------------
	 */
	session_start();

	// Seguridad: Si no hay usuario, fuera.
	if (!isset($_SESSION['usuario'])) {
		header("Location: intruso.php");
		exit;
	}

	require_once 'inc/db.php'; 

	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: LÓGICA DE BACKEND (POST)
	 * -------------------------------------------------------------------------
	 */
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$accion = $_POST['accion'] ?? ''; // Leer acción del formulario
		$id = (int)($_POST['id'] ?? 0);
		$usuario_id = $_SESSION['id_usuario'] ?? 1; 

		// 1. MOVER A VISTAS
		if ($accion === 'mover') {
			$fecha = $_POST['fecha'];
			$rating = $_POST['rating']; 
			$stmt = $conexion->prepare("UPDATE contenido SET estado='Vistas', fecha_visualizacion=?, puntuacion=? WHERE id=?");
			$stmt->bind_param("ssi", $fecha, $rating, $id);
			$stmt->execute(); $stmt->close();

		// 2. BORRAR
		} elseif ($accion === 'borrar') {
			$stmt = $conexion->prepare("DELETE FROM contenido WHERE id=?");
			$stmt->bind_param("i", $id);
			$stmt->execute(); $stmt->close();

		// 3. AGREGAR PELÍCULA
		} elseif ($accion === 'agregar') {
			$titulo = $_POST['nombre'];
			$comentario = $_POST['comentario'] ?? '';
			$prioridad = $_POST['prioridad'] ?? 'Media';
			$estado = $_POST['estado'] ?? 'Por_ver';
			$img_url = $_POST['imagen_url'] ?? ''; 
			$tipo = 'pelicula'; // CLAVE: Diferenciamos de 'serie'

			$sql = "INSERT INTO contenido (usuario_id, titulo, comentario, estado, tipo, nivel_prioridad, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$stmt = $conexion->prepare($sql);
			$stmt->bind_param("issssss", $usuario_id, $titulo, $comentario, $estado, $tipo, $prioridad, $img_url);
			$stmt->execute(); $stmt->close();
		}
		
		// Evitar reenvío de formulario
		header("Location: peliculas.php");
		exit;
	}

	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: LÓGICA DE ORDENAMIENTO (GET)
	 * -------------------------------------------------------------------------
	 */
	// Capturamos la opción de orden del desplegable (por defecto fecha)
	$orden = $_GET['orden'] ?? 'fecha';
	$sql_order = "ORDER BY id DESC"; // Default: Más recientes primero

	if ($orden === 'alfa') {
		$sql_order = "ORDER BY titulo ASC";
	} elseif ($orden === 'prioridad') {
		// Orden personalizado: Alta -> Media -> Baja
		$sql_order = "ORDER BY FIELD(nivel_prioridad, 'Alta', 'Media', 'Baja')";
	}

	// Consulta final con el orden aplicado Y FILTRO DE USUARIO
	$usuario_id = $_SESSION['id_usuario'];
	$sql = "SELECT * FROM contenido WHERE tipo = 'pelicula' AND usuario_id = ? $sql_order";
	
	$stmt = $conexion->prepare($sql);
	$stmt->bind_param("i", $usuario_id);
	$stmt->execute();
	$result = $stmt->get_result();

	$peliculas = [];
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$peliculas[] = $row;
		}
	}
?>
<?php include "inc/cabecera.php" ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<style>
		/* --- LAYOUT GENERAL --- */
		.dashboard-container {
			display: grid; 
			grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
			gap: 30px; 
			padding: 20px; 
			max-width: 1600px; 
			margin: 0 auto;
		}


		/* --- ENCABEZADOS DE COLUMNA (Ahora ambos naranjas) --- */
		.columna-header {
			padding: 15px; 
			border-radius: 10px 10px 0 0; 
			color: white;
			font-weight: 800; 
			text-align: center; 
			text-transform: uppercase;
			letter-spacing: 1px;
			box-shadow: 0 4px 6px rgba(0,0,0,0.1);
			/* Mismo degradado naranja para ambos */
			background: linear-gradient(to right, #FF7F50, #FF6347); 
		}

		/* Cuerpo blanco de las columnas */
		.columna-body {
			background: #fff; 
			border: 1px solid #eee; 
			border-top: none;
			border-radius: 0 0 10px 10px; 
			padding: 20px; 
			min-height: 450px; /* Altura mínima para que se vea bien */
			box-shadow: 0 10px 30px rgba(0,0,0,0.05);
			
						
			/* SCROLL: */
			height: 50vh;       /* Ocupa el 70% del alto de la pantalla */
			overflow-y: auto;   /* Activa el scroll vertical si hay muchas series */
			
		}


		/* --- TARJETA DE PELÍCULA --- */
		.card-peli {
			display: flex; 
			align-items: center;
			background: #fff; 
			border-radius: 12px; 
			padding: 10px;
			margin-bottom: 15px; 
			box-shadow: 0 4px 15px rgba(0,0,0,0.05);
			border: 1px solid #f0f0f0;
			border-left: 5px solid var(--primary); /* Borde lateral naranja */
			transition: transform 0.2s, box-shadow 0.2s;
		}
		.card-peli:hover { 
			transform: translateY(-3px); 
			box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
		}

		.card-img {
			width: 60px; height: 90px; 
			object-fit: cover; 
			border-radius: 6px;
			background-color: #eee; 
			flex-shrink: 0;
			box-shadow: 0 2px 5px rgba(0,0,0,0.2);
		}

		.card-info { 
			flex-grow: 1; 
			padding-left: 20px; 
			display: flex; 
			flex-direction: column; 
			justify-content: center; 
		}
		
		.card-info h4 { margin: 0 0 5px; font-size: 1.1rem; color: #333; font-weight: 700; }
		.card-info small { color: #888; font-size: 0.85rem; margin-bottom: 5px; display: block; }
		
		.badge-prioridad {
			font-size: 0.75rem; 
			padding: 4px 10px; 
			border-radius: 20px;
			background: #f3f3f3; 
			color: #666; 
			font-weight: 600;
			width: fit-content;
		}

		/* --- BOTONES DE ACCIÓN (Estilo imagen) --- */
		.card-actions { 
			display: flex; 
			gap: 10px; 
			padding-left: 15px; 
		}

		/* Clase base para botones cuadrados */
		.btn-cuadrado {
			width: 40px; 
			height: 40px; 
			border-radius: 8px;
			border: none;
			display: flex; 
			align-items: center; 
			justify-content: center;
			color: white; 
			font-size: 1.1rem;
			cursor: pointer;
			transition: filter 0.2s, transform 0.1s;
			margin: 0; /* Override del global button */
			box-shadow: 0 4px 6px rgba(0,0,0,0.1);
		}
		.btn-cuadrado:hover { filter: brightness(1.1); }
		.btn-cuadrado:active { transform: scale(0.95); }

		/* Colores específicos */
		.btn-check { 
			background: #2ecc71; /* Verde */
			background: linear-gradient(to bottom right, #2ecc71, #27ae60);
		}
		.btn-trash { 
			background: #e74c3c; /* Rojo */
			background: linear-gradient(to bottom right, #e74c3c, #c0392b);
		}

		/* Estrellas amarillas */
		.estrellas { color: #FFD700; font-size: 0.9rem; margin-top: 5px;}

		/* --- BARRA SUPERIOR Y ORDENAR --- */
		.top-bar {
			display: flex; 
			justify-content: space-between; 
			align-items: center;
			padding: 20px 40px; 
			background: #fff; 
			box-shadow: 0 2px 10px rgba(0,0,0,0.05);
			margin-bottom: 20px;
		}

		.controles-derecha {
			display: flex;
			gap: 15px;
			align-items: center;
		}

		/* Select de ordenar personalizado */
		.select-orden {
			padding: 10px 15px;
			border-radius: 20px;
			border: 1px solid #ddd;
			background: #fff;
			font-family: 'Inter', sans-serif;
			color: #555;
			cursor: pointer;
			outline: none;
		}

		/* --- SUGERENCIAS API y MODALES (Igual que antes) --- */
		.suggestions-box {
			position: absolute; top: 100%; left: 0; width: 100%;
			background: #fff; border: 1px solid #ddd; border-top: none;
			z-index: 1001; max-height: 200px; overflow-y: auto;
			box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px; display: none;
		}
		.sugg-item { padding: 10px; display: flex; align-items: center; cursor: pointer; border-bottom: 1px solid #eee; }
		.sugg-item:hover { background-color: #f9f9f9; }
		.sugg-item img { width: 40px; margin-right: 10px; border-radius: 4px; }
		
		.modal-fondo {
			display: none; position: fixed; z-index: 2000; left: 0; top: 0;
			width: 100%; height: 100%; overflow: auto;
			background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px);
			align-items: center; justify-content: center;
		}
		.modal-contenido {
			background-color: #fff; border-radius: 10px; padding: 30px;
			width: 90%; max-width: 500px; position: relative;
			box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: bajar 0.3s ease;
		}
		@keyframes bajar { from {transform: translateY(-20px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
		
		.rating-group { display: flex; flex-direction: row-reverse; justify-content: center; margin: 15px 0; }
		.rating-group input { display: none; }
		.rating-group label { font-size: 30px; color: #ddd; cursor: pointer; transition: 0.2s; }
		.rating-group input:checked ~ label, .rating-group label:hover, .rating-group label:hover ~ label { color: #FFD700; }
	</style>

	<div class="top-bar">
		<div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
			<i class="fa-solid fa-clapperboard"></i> Mis Películas
		</div>
		<!-- ORDEN DE PELICULAS -->
		<div class="controles-derecha">
			<form method="GET" style="background:none; padding:0; height:auto; display:block;">
				<select name="orden" class="select-orden" onchange="this.form.submit()">
					<option value="fecha" <?= $orden == 'fecha' ? 'selected' : '' ?>>📅 Fecha (Nuevo)</option>
					<option value="alfa" <?= $orden == 'alfa' ? 'selected' : '' ?>>🔤 Alfabético (A-Z)</option>
					<option value="prioridad" <?= $orden == 'prioridad' ? 'selected' : '' ?>>🔥 Prioridad</option>
				</select>
			</form>
		<!-- AÑADIR PELICULAS -->
			<a href="formulario_pelicula.php" class="btn-agregar-diseno" style="margin: 0; text-decoration:none;">
				<span class="plus-icon"><i class="fa-solid fa-plus"></i></span> Nueva Película
			</a>
		</div>
	</div>

	<div class="dashboard-container">
		<!-- TARJETA POR VER -->
		<div>
			<div class="columna-header">Por Ver</div>
			<div class="columna-body">
				<?php foreach ($peliculas as $p): ?>
					<?php if ($p['estado'] == 'Por_ver'): ?>
					
					<div class="card-peli">
						<img src="<?= !empty($p['imagen_url']) ? htmlspecialchars($p['imagen_url']) : 'https://via.placeholder.com/60x90?text=Cine' ?>" 
							 class="card-img" alt="cover">
						
						<div class="card-info">
							<h4><?= htmlspecialchars($p['titulo']) ?></h4>
							<small><?= htmlspecialchars($p['comentario']) ?></small>
							<span class="badge-prioridad">Prioridad: <?= htmlspecialchars($p['nivel_prioridad'] ?? 'Media') ?></span>
						</div>
						
						<div class="card-actions">
							<button class="btn-cuadrado btn-check" onclick="abrirModalCalificar(<?= $p['id'] ?>)">
								<i class="fa-solid fa-check"></i>
							</button>
							
							<form method="POST" onsubmit="return confirm('¿Seguro que quieres borrarla?');" style="background:none; padding:0; height:auto;">
								<input type="hidden" name="accion" value="borrar">
								<input type="hidden" name="id" value="<?= $p['id'] ?>">
								<button type="submit" class="btn-cuadrado btn-trash">
									<i class="fa-solid fa-trash"></i>
								</button>
							</form>
						</div>
					</div>

					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<!-- TARJETA VISTAS -->
		<div>
			<div class="columna-header">Vistas</div>
			<div class="columna-body">
				<?php foreach ($peliculas as $p): ?>
					<?php if ($p['estado'] == 'Vistas'): ?>
					
					<div class="card-peli" style="opacity: 0.9;">
						<img src="<?= !empty($p['imagen_url']) ? htmlspecialchars($p['imagen_url']) : 'https://via.placeholder.com/60x90?text=Cine' ?>" 
							 class="card-img" alt="cover">
						
						<div class="card-info">
							<h4><?= htmlspecialchars($p['titulo']) ?></h4>
							<small>Visto: <?= htmlspecialchars($p['fecha_visualizacion']) ?></small>
							
							<?php $pts = (int)($p['puntuacion'] ?? 0); ?>
							<div class="estrellas">
								<?= str_repeat('<i class="fa-solid fa-star"></i>', $pts) ?>
								<?= str_repeat('<i class="fa-regular fa-star"></i>', 5 - $pts) ?>
							</div>
						</div>
						
						<div class="card-actions">
							<form method="POST" onsubmit="return confirm('¿Eliminar del historial?');" style="background:none; padding:0; height:auto;">
								<input type="hidden" name="accion" value="borrar">
								<input type="hidden" name="id" value="<?= $p['id'] ?>">
								<button type="submit" class="btn-cuadrado btn-trash">
									<i class="fa-solid fa-trash"></i>
								</button>
							</form>
						</div>
					</div>

					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

	</div>

	<div id="modalAgregar" class="modal-fondo">
		<div class="modal-contenido">
			<span class="cerrar-modal" onclick="document.getElementById('modalAgregar').style.display='none'" 
				  style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:1.5rem;">&times;</span>
			
			<form action="" method="POST">
				<input type="hidden" name="accion" value="agregar">
				<h2 style="text-align:center; color:var(--primary); margin-bottom:20px;">Nueva Película</h2>

				<div id="preview_container" style="text-align:center; display:none; margin-bottom:15px;">
					<img id="preview_img" src="" style="width:100px; border-radius:5px; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
				</div>
				<input type="hidden" name="imagen_url" id="imagen_input">

				<div class="input-group">
					<input type="text" name="nombre" id="titulo_input" placeholder="Buscar título..." autocomplete="off" required />
					<i class="fa-solid fa-magnifying-glass"></i>
					<div id="suggestions" class="suggestions-box"></div>
				</div>

				<div class="input-group">
					<input type="text" name="comentario" placeholder="Comentario breve" />
					<i class="fa-solid fa-comment"></i>
				</div>

				<div class="input-group">
					<select name="prioridad">
						<option value="Alta">Prioridad Alta</option>
						<option value="Media" selected>Prioridad Media</option>
						<option value="Baja">Prioridad Baja</option>
					</select>
					<i class="fa-solid fa-layer-group"></i>
				</div>

				<button type="submit" style="width:100%; margin-top:10px;">Guardar</button>
			</form>
		</div>
	</div>

	<div id="modalCalificar" class="modal-fondo">
		<div class="modal-contenido" style="text-align: center; border-radius: 15px; padding: 40px; border-top: 6px solid #FF4500;">
			<h2 style="color: #FF4500; font-weight: 800; margin-top: 0; margin-bottom: 5px; font-size: 1.8rem;">
				<i class="fa-solid fa-award"></i> ¿Ya la viste?
			</h2>
			<p style="color: #666; margin-bottom: 25px; font-size: 0.95rem;">
				¡Genial! Regístralo en tu historial.
			</p>
			
			<form method="POST">
				<input type="hidden" name="accion" value="mover">
				<input type="hidden" name="id" id="idPeliCalificar">

				<div class="input-group" style="text-align: left;">
					<label style="display:block; font-size:0.8rem; font-weight:bold; color:#333; margin-bottom:5px;">Fecha de visualización:</label>
					<input type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>" 
						   style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-family: inherit;">
				</div>

				<div style="margin: 20px 0;">
					<label style="display:block; font-size:0.8rem; font-weight:bold; color:#333; margin-bottom:5px;">Tu Valoración:</label>
					<div class="rating-group" style="margin: 0; justify-content: center; gap: 5px;">
						<input type="radio" name="rating" value="5" id="r5"><label for="r5" title="¡Obra maestra!">★</label>
						<input type="radio" name="rating" value="4" id="r4"><label for="r4" title="Muy buena">★</label>
						<input type="radio" name="rating" value="3" id="r3"><label for="r3" title="Buena">★</label>
						<input type="radio" name="rating" value="2" id="r2"><label for="r2" title="Regular">★</label>
						<input type="radio" name="rating" value="1" id="r1"><label for="r1" title="Mala">★</label>
					</div>
				</div>

				<div style="display:flex; gap:15px; justify-content:center; margin-top: 10px;">
					<button type="submit" style="flex: 1; border-radius: 50px; background: linear-gradient(to right, #ff4e00, #ff7f50); box-shadow: 0 4px 6px rgba(255, 69, 0, 0.2);">
						Guardar
					</button>
					<button type="button" onclick="document.getElementById('modalCalificar').style.display='none'" 
							style="flex: 1; background: #f0f0f0; color: #555; border: 1px solid #ccc; border-radius: 50px; padding: 12px; font-weight: bold; cursor: pointer;">
						Cancelar
					</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		// MODAL CALIFICAR
		const modalCalificar = document.getElementById('modalCalificar');
		
		function abrirModalCalificar(id) {
			document.getElementById('idPeliCalificar').value = id;
			modalCalificar.style.display = 'flex';
		}

		window.onclick = function(event) {
			if (event.target == modalCalificar) modalCalificar.style.display = "none";
		}
	</script>

<?php include "inc/piedepagina.php" ?>



