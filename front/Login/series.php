<?php include "../inc/cabecera.php" ?>
<?php
	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: SESIÓN Y CONFIGURACIÓN
	 * -------------------------------------------------------------------------
	 */
	session_start();

	if (!isset($_SESSION['usuario'])) {
		header("Location: intruso.php");
		exit;
	}

	require_once __DIR__ . '/db.php'; 

	/**
	 * -------------------------------------------------------------------------
	 * BLOQUE PHP: BACKEND (ACCIONES)
	 * -------------------------------------------------------------------------
	 */
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$accion = $_POST['accion'] ?? '';
		$id = (int)($_POST['id'] ?? 0);
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
			
			// IMPORTANTE: Tipo fijo como 'serie'
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

	// FILTRAR SOLO SERIES (tipo = 'serie')
	$sql = "SELECT * FROM contenido WHERE tipo = 'serie' $sql_order";
	$result = $conexion->query($sql);

	$series = [];
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$series[] = $row;
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mis Series</title>
	<link rel="stylesheet" href="style/style.css">
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
		/* Opcional: Estilizar la barra de scroll para que se vea más fina */
			.columna-body::-webkit-scrollbar {
				width: 8px;
			}
			.columna-body::-webkit-scrollbar-track {
				background: #f1f1f1;
				border-radius: 0 0 10px 0;
			}
			.columna-body::-webkit-scrollbar-thumb {
				background: #ccc; 
				border-radius: 4px;
			}
			.columna-body::-webkit-scrollbar-thumb:hover {
				background: var(--primary); /* Se pone naranja al pasar el mouse */
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
	</style>
</head>
<body>

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
			<button id="btnAbrirModal" style="margin: 0;">
				<i class="fa-solid fa-plus"></i> Nueva Serie
			</button>
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

	<div id="modalAgregar" class="modal-fondo">
		<div class="modal-contenido">
			<span class="cerrar-modal" onclick="document.getElementById('modalAgregar').style.display='none'" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:1.5rem;">&times;</span>
			<form action="" method="POST">
				<input type="hidden" name="accion" value="agregar">
				<h2 style="text-align:center; color:var(--primary); margin-bottom:20px;">Nueva Serie</h2>

				<div id="preview_container" style="text-align:center; display:none; margin-bottom:15px;">
					<img id="preview_img" src="" style="width:100px; border-radius:5px; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
				</div>
				<input type="hidden" name="imagen_url" id="imagen_input">

				<div class="input-group">
					<input type="text" name="nombre" id="titulo_input" placeholder="Buscar serie TV..." autocomplete="off" required />
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
				
				<div class="input-group">
					<select name="estado">
						<option value="Por_ver" selected>Por ver</option>
						<option value="Viendo">Viendo</option>
						<option value="Vistas">Vista</option>
					</select>
					<i class="fa-solid fa-eye"></i>
				</div>

				<button type="submit" style="width:100%; margin-top:10px;">Guardar</button>
			</form>
		</div>
	</div>

	<div id="modalCalificar" class="modal-fondo">
		<div class="modal-contenido" style="text-align: center;">
			<h3 style="color: #333;">¡Serie Terminada!</h3>
			<p>Ponle nota y fecha</p>
			
			<form method="POST">
				<input type="hidden" name="accion" value="terminar">
				<input type="hidden" name="id" id="idPeliCalificar">

				<div class="input-group">
					<input type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
					<i class="fa-solid fa-calendar"></i>
				</div>

				<div class="rating-group">
					<input type="radio" name="rating" value="5" id="r5"><label for="r5">★</label>
					<input type="radio" name="rating" value="4" id="r4"><label for="r4">★</label>
					<input type="radio" name="rating" value="3" id="r3"><label for="r3">★</label>
					<input type="radio" name="rating" value="2" id="r2"><label for="r2">★</label>
					<input type="radio" name="rating" value="1" id="r1"><label for="r1">★</label>
				</div>

				<div style="display:flex; gap:10px; justify-content:center;">
					<button type="submit">Guardar</button>
					<button type="button" class="ghost" onclick="document.getElementById('modalCalificar').style.display='none'" style="border:1px solid #aaa; color:#555;">Cancelar</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		// MODALES
		const modalAgregar = document.getElementById('modalAgregar');
		const modalCalificar = document.getElementById('modalCalificar');
		const btnAbrir = document.getElementById('btnAbrirModal');

		btnAbrir.addEventListener('click', () => { modalAgregar.style.display = 'flex'; });

		function abrirModalCalificar(id) {
			document.getElementById('idPeliCalificar').value = id;
			modalCalificar.style.display = 'flex';
		}

		window.onclick = function(event) {
			if (event.target == modalAgregar) modalAgregar.style.display = "none";
			if (event.target == modalCalificar) modalCalificar.style.display = "none";
		}

		// API TMDB (MODO SERIE - search/tv)
		const API_KEY = '3fd2be6f0c70a2a598f084ddfb75487c'; 
		const tituloInput = document.getElementById('titulo_input');
		const suggestionsBox = document.getElementById('suggestions');
		const imagenInput = document.getElementById('imagen_input');
		const previewContainer = document.getElementById('preview_container');
		const previewImg = document.getElementById('preview_img');

		if (tituloInput) {
			tituloInput.addEventListener('input', async function() {
				const query = this.value.trim();
				if (query.length < 3) { suggestionsBox.style.display = 'none'; return; }

				try {
					// CAMBIO IMPORTANTE: search/tv en vez de search/movie
					const res = await fetch(`https://api.themoviedb.org/3/search/tv?api_key=${API_KEY}&language=es-ES&query=${query}`);
					const data = await res.json();
					suggestionsBox.innerHTML = '';

					if (data.results && data.results.length > 0) {
						suggestionsBox.style.display = 'block';
						data.results.slice(0, 5).forEach(serie => {
							const div = document.createElement('div');
							div.className = 'sugg-item';
							
							// Series usan 'name' en vez de 'title' y 'first_air_date'
							const title = serie.name;
							const year = serie.first_air_date ? serie.first_air_date.split('-')[0] : '';
							const poster = serie.poster_path ? `https://image.tmdb.org/t/p/w92${serie.poster_path}` : '';
							const fullPoster = serie.poster_path ? `https://image.tmdb.org/t/p/w500${serie.poster_path}` : '';

							div.innerHTML = `<img src="${poster}" alt="img"><div><strong>${title}</strong> <small>(${year})</small></div>`;

							div.addEventListener('click', () => {
								tituloInput.value = title;
								imagenInput.value = fullPoster; 
								if(fullPoster) {
									previewImg.src = fullPoster;
									previewContainer.style.display = 'block';
								}
								suggestionsBox.style.display = 'none';
							});
							suggestionsBox.appendChild(div);
						});
					} else { suggestionsBox.style.display = 'none'; }
				} catch (e) { console.error(e); }
			});
		}
	</script>
</body>
<?php include "../inc/piedepagina.php" ?>
