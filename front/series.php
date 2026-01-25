
<?php
	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: SESIÓN Y CONFIGURACIÓN
	 * -------------------------------------------------------------------------
	 */
	session_start();

	// --- 1. SEGURIDAD: CONTROL DE SESIÓN ---
	if (!isset($_SESSION['usuario'])) {
		header("Location: intruso.php"); // Si no es usuario, fuera.
		exit;
	}

	require_once 'inc/db.php'; 

	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: BACKEND (ACCIONES)
	 * -------------------------------------------------------------------------
	 */
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$accion = $_POST['accion'] ?? ''; // Qué botón pulsó el usuario
		$id = (int)($_POST['id'] ?? 0);   // ID de la serie afectada
		$usuario_id = $_SESSION['id_usuario'] ?? 1; 

		// 1. EMPEZAR A VER (Mover de 'Por_ver' -> 'Viendo')
		if ($accion === 'empezar') {
			// Simplemente cambiamos el estado, sin pedir nota ni fecha aún
			$stmt = $conexion->prepare("UPDATE contenido SET estado='Viendo' WHERE id=?");
			$stmt->bind_param("i", $id);
			$stmt->execute(); $stmt->close();

		// 2. TERMINAR SERIE (Mover de 'Viendo' -> 'Vistas')
		} elseif ($accion === 'terminar') {
			$fecha = $_POST['fecha'];
			$rating = $_POST['rating']; 
			// Aquí guardamos la fecha en que la terminaste y tu nota final
			$stmt = $conexion->prepare("UPDATE contenido SET estado='Vistas', fecha_visualizacion=?, puntuacion=? WHERE id=?");
			$stmt->bind_param("ssi", $fecha, $rating, $id);
			$stmt->execute(); $stmt->close();

		// 3. BORRAR
		} elseif ($accion === 'borrar') {
			$stmt = $conexion->prepare("DELETE FROM contenido WHERE id=?");
			$stmt->bind_param("i", $id);
			$stmt->execute(); $stmt->close();

		// 4. AGREGAR NUEVA SERIE
		} elseif ($accion === 'agregar') {
			$titulo = $_POST['nombre'];
			$comentario = $_POST['comentario'] ?? '';
			$prioridad = $_POST['prioridad'] ?? 'Media';
			$estado = $_POST['estado'] ?? 'Por_ver';
			$img_url = $_POST['imagen_url'] ?? ''; 
			
			// Forzamos "tipo = serie" para diferenciarlo en la tabla única
			$tipo = 'serie';

			$sql = "INSERT INTO contenido (usuario_id, titulo, comentario, estado, tipo, nivel_prioridad, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$stmt = $conexion->prepare($sql);
			$stmt->bind_param("issssss", $usuario_id, $titulo, $comentario, $estado, $tipo, $prioridad, $img_url);
			$stmt->execute(); $stmt->close();
		}
		
		header("Location: series.php");
		exit;
	}

	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: ORDENAMIENTO Y CONSULTA
	 * -------------------------------------------------------------------------
	 */
	$orden = $_GET['orden'] ?? 'fecha';
	$sql_order = "ORDER BY id DESC"; // Default

	if ($orden === 'alfa') {
		$sql_order = "ORDER BY titulo ASC";
	} elseif ($orden === 'prioridad') {
		$sql_order = "ORDER BY FIELD(nivel_prioridad, 'Alta', 'Media', 'Baja')";
	}

	// FILTRAR SOLO SERIES (tipo = 'serie') DEL USUARIO ACTUAL
	$usuario_id = $_SESSION['id_usuario'];
	$sql = "SELECT * FROM contenido WHERE tipo = 'serie' AND usuario_id = ? $sql_order";
	
	$stmt = $conexion->prepare($sql);
	$stmt->bind_param("i", $usuario_id);
	$stmt->execute();
	$result = $stmt->get_result();

	$series = [];
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$series[] = $row;
		}
	}
?>

<?php include "inc/cabecera.php" ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<style>
		/* Estilos base (reutilizados de peliculas) */
		.dashboard-container {
			display: grid; 
			/* Ajuste para 3 columnas responsivas */
			grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
			gap: 20px; 
			padding: 20px; 
			max-width: 1600px; 
			margin: 0 auto;
		}

		.columna-header {
			padding: 15px; border-radius: 10px 10px 0 0; color: white;
			font-weight: 800; text-align: center; text-transform: uppercase;
			letter-spacing: 1px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
		}

		/* COLORES DE COLUMNAS (Gradiente progresivo) */
		.header-por-ver { background: linear-gradient(to right, #FF7F50, #FF6347); }
		.header-viendo { background: linear-gradient(to right, #FF7F50, #FF6347); }
		.header-vistas { background: linear-gradient(to right, #FF7F50, #FF6347); } 

		.columna-body {
			background: #fff; 
			border: 1px solid #eee; 
			border-top: none;
			border-radius: 0 0 10px 10px; 
			padding: 15px; 
			
			/* SCROLL: */
			height: 50vh;       /* Ocupa el 70% del alto de la pantalla */
			overflow-y: auto;   /* Activa el scroll vertical si hay muchas series */
			
			box-shadow: 0 10px 30px rgba(0,0,0,0.05);
		}

		.card-peli {
			display: flex; align-items: center; background: #fff; border-radius: 12px; 
			padding: 10px; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
			border: 1px solid #f0f0f0; border-left: 5px solid var(--primary);
			transition: transform 0.2s, box-shadow 0.2s;
		}
		.card-peli:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }

		.card-img { width: 50px; height: 75px; object-fit: cover; border-radius: 6px; background-color: #eee; flex-shrink: 0; }
		.card-info { flex-grow: 1; padding-left: 15px; display: flex; flex-direction: column; justify-content: center; }
		.card-info h4 { margin: 0 0 3px; font-size: 1rem; color: #333; font-weight: 700; }
		.card-info small { color: #888; font-size: 0.8rem; margin-bottom: 3px; display: block; }
		
		.badge-prioridad { font-size: 0.7rem; padding: 2px 8px; border-radius: 20px; background: #f3f3f3; color: #666; font-weight: 600; width: fit-content; }

		.card-actions { display: flex; gap: 8px; padding-left: 10px; }
		
		.btn-cuadrado {
			width: 35px; height: 35px; border-radius: 8px; border: none;
			display: flex; align-items: center; justify-content: center;
			color: white; font-size: 1rem; cursor: pointer;
			transition: filter 0.2s, transform 0.1s; margin: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
		}
		.btn-cuadrado:hover { filter: brightness(1.1); }
		.btn-cuadrado:active { transform: scale(0.95); }
		
		.btn-check { background: linear-gradient(to bottom right, #2ecc71, #27ae60); }
		.btn-play { background: linear-gradient(to bottom right, #f39c12, #d35400); } /* Icono Play para empezar */
		.btn-trash { background: linear-gradient(to bottom right, #e74c3c, #c0392b); }

		.estrellas { color: #FFD700; font-size: 0.8rem; margin-top: 3px;}

		.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
		.controles-derecha { display: flex; gap: 15px; align-items: center; }
		.select-orden { padding: 10px 15px; border-radius: 20px; border: 1px solid #ddd; background: #fff; font-family: 'Inter', sans-serif; color: #555; cursor: pointer; outline: none; }

		/* Sugerencias y Modales */
		.suggestions-box { position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #ddd; border-top: none; z-index: 1001; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px; display: none; }
		.sugg-item { padding: 10px; display: flex; align-items: center; cursor: pointer; border-bottom: 1px solid #eee; }
		.sugg-item:hover { background-color: #f9f9f9; }
		.sugg-item img { width: 40px; margin-right: 10px; border-radius: 4px; }
		
		.modal-fondo { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px); align-items: center; justify-content: center; }
		.modal-contenido { background-color: #fff; border-radius: 10px; padding: 30px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: bajar 0.3s ease; }
		@keyframes bajar { from {transform: translateY(-20px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
		
		.rating-group { display: flex; flex-direction: row-reverse; justify-content: center; margin: 15px 0; }
		.rating-group input { display: none; }
		.rating-group label { font-size: 30px; color: #ddd; cursor: pointer; transition: 0.2s; }
		.rating-group input:checked ~ label, .rating-group label:hover, .rating-group label:hover ~ label { color: #FFD700; }
		.rating-group label { font-size: 30px; color: #ddd; cursor: pointer; transition: 0.2s; }
		.rating-group input:checked ~ label, .rating-group label:hover, .rating-group label:hover ~ label { color: #FFD700; }
	</style>

	<div class="top-bar">
		<div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
			<i class="fa-solid fa-tv"></i> Mis Series
		</div>
		
		<div class="controles-derecha">
			<form method="GET" style="background:none; padding:0; height:auto; display:block;">
				<select name="orden" class="select-orden" onchange="this.form.submit()">
					<option value="fecha" <?= $orden == 'fecha' ? 'selected' : '' ?>>📅 Fecha</option>
					<option value="alfa" <?= $orden == 'alfa' ? 'selected' : '' ?>>🔤 A-Z</option>
					<option value="prioridad" <?= $orden == 'prioridad' ? 'selected' : '' ?>>🔥 Prioridad</option>
				</select>
			</form>
			<a href="formulario_serie.php" class="btn-agregar-diseno" style="margin: 0; text-decoration:none;">
				<span class="plus-icon"><i class="fa-solid fa-plus"></i></span> Nueva Serie
			</a>
		</div>
	</div>

	<div class="dashboard-container">
		
		<div>
			<div class="columna-header header-por-ver">Por Ver</div>
			<div class="columna-body">
				<?php foreach ($series as $s): ?>
					<?php if ($s['estado'] == 'Por_ver'): ?>
					<div class="card-peli">
						<img src="<?= !empty($s['imagen_url']) ? htmlspecialchars($s['imagen_url']) : 'https://via.placeholder.com/60x90?text=TV' ?>" class="card-img">
						<div class="card-info">
							<h4><?= htmlspecialchars($s['titulo']) ?></h4>
							<small><?= htmlspecialchars($s['comentario']) ?></small>
							<span class="badge-prioridad">Prioridad: <?= htmlspecialchars($s['nivel_prioridad'] ?? 'Media') ?></span>
						</div>
						<div class="card-actions">
							<form method="POST" style="background:none; padding:0; height:auto;">
								<input type="hidden" name="accion" value="empezar">
								<input type="hidden" name="id" value="<?= $s['id'] ?>">
								<button type="submit" class="btn-cuadrado btn-play" title="Empezar a ver">
									<i class="fa-solid fa-play"></i>
								</button>
							</form>
							<form method="POST" onsubmit="return confirm('¿Borrar serie?');" style="background:none; padding:0; height:auto;">
								<input type="hidden" name="accion" value="borrar">
								<input type="hidden" name="id" value="<?= $s['id'] ?>">
								<button type="submit" class="btn-cuadrado btn-trash"><i class="fa-solid fa-trash"></i></button>
							</form>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

		<div>
			<div class="columna-header header-viendo">Viendo</div>
			<div class="columna-body">
				<?php foreach ($series as $s): ?>
					<?php if ($s['estado'] == 'Viendo'): ?>
					<div class="card-peli" style="border-left-color: #F09819;">
						<img src="<?= !empty($s['imagen_url']) ? htmlspecialchars($s['imagen_url']) : 'https://via.placeholder.com/60x90?text=TV' ?>" class="card-img">
						<div class="card-info">
							<h4><?= htmlspecialchars($s['titulo']) ?></h4>
							<small><?= htmlspecialchars($s['comentario']) ?></small>
							<span class="badge-prioridad" style="background:#fff3cd; color:#856404;">En progreso</span>
						</div>
						<div class="card-actions">
							<button class="btn-cuadrado btn-check" onclick="abrirModalCalificar(<?= $s['id'] ?>)" title="Terminar serie">
								<i class="fa-solid fa-check"></i>
							</button>
							<form method="POST" onsubmit="return confirm('¿Borrar?');" style="background:none; padding:0; height:auto;">
								<input type="hidden" name="accion" value="borrar">
								<input type="hidden" name="id" value="<?= $s['id'] ?>">
								<button type="submit" class="btn-cuadrado btn-trash"><i class="fa-solid fa-trash"></i></button>
							</form>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

		<div>
			<div class="columna-header header-vistas">Vistas</div>
			<div class="columna-body">
				<?php foreach ($series as $s): ?>
					<?php if ($s['estado'] == 'Vistas'): ?>
					<div class="card-peli" style="opacity: 0.9; border-left-color: #20B2AA;">
						<img src="<?= !empty($s['imagen_url']) ? htmlspecialchars($s['imagen_url']) : 'https://via.placeholder.com/60x90?text=TV' ?>" class="card-img">
						<div class="card-info">
							<h4><?= htmlspecialchars($s['titulo']) ?></h4>
							<small>Terminada: <?= htmlspecialchars($s['fecha_visualizacion']) ?></small>
							<?php $pts = (int)($s['puntuacion'] ?? 0); ?>
							<div class="estrellas">
								<?= str_repeat('<i class="fa-solid fa-star"></i>', $pts) ?>
								<?= str_repeat('<i class="fa-regular fa-star"></i>', 5 - $pts) ?>
							</div>
						</div>
						<div class="card-actions">
							<form method="POST" onsubmit="return confirm('¿Borrar historial?');" style="background:none; padding:0; height:auto;">
								<input type="hidden" name="accion" value="borrar">
								<input type="hidden" name="id" value="<?= $s['id'] ?>">
								<button type="submit" class="btn-cuadrado btn-trash"><i class="fa-solid fa-trash"></i></button>
							</form>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

	</div>



	<div id="modalCalificar" class="modal-fondo">
		<div class="modal-contenido" style="text-align: center; border-radius: 15px; padding: 40px; border-top: 6px solid #FF4500;">
			<h2 style="color: #FF4500; font-weight: 800; margin-top: 0; margin-bottom: 5px; font-size: 1.8rem;">
				<i class="fa-solid fa-flag-checkered"></i> ¡Serie Terminada!
			</h2>
			<p style="color: #666; margin-bottom: 25px; font-size: 0.95rem;">
				Felicidades, otra más a la lista.
			</p>
			
			<form method="POST">
				<input type="hidden" name="accion" value="terminar">
				<input type="hidden" name="id" id="idPeliCalificar">

				<div class="input-group" style="text-align: left;">
					<label style="display:block; font-size:0.8rem; font-weight:bold; color:#333; margin-bottom:5px;">Fecha de finalización:</label>
					<input type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>" 
						   style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-family: inherit;">
				</div>

				<div style="margin: 20px 0;">
					<label style="display:block; font-size:0.8rem; font-weight:bold; color:#333; margin-bottom:5px;">Nota Final:</label>
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
