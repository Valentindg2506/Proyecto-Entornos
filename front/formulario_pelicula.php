<?php
	/**
	 * -------------------------------------------------------------------------
	 * FORMULARIO: AÑADIR PELÍCULA
	 * -------------------------------------------------------------------------
	 */
	session_start(); 
	if (!isset($_SESSION['usuario'])) {
		header("Location: intruso.php");
		exit;
	}
?>
<?php include "inc/cabecera.php" ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<style>
		.container {
			padding: 40px 20px;
			display: flex;
			justify-content: center;
			align-items: flex-start;
		}
		
		/* Reutilizamos estilo similar al modal pero en página completa */
		.form-card {
			background-color: white;
			padding: 30px;
			border-radius: 10px;
			border: 1px solid #000;
			box-shadow: 0 10px 25px rgba(0,0,0,0.1);
			width: 100%;
			max-width: 500px;
		}

		.form-title {
			text-align: center;
			color: #FF4500; /* Naranja corporativo */
			margin-bottom: 25px;
			font-size: 1.8rem;
			font-weight: bold;
		}

		.input-group {
			position: relative;
			margin-bottom: 20px;
		}

		.input-group input, .input-group select {
			width: 100%;
			padding: 12px 15px 12px 40px; /* Espacio para icono */
			border: 1px solid #ccc;
			border-radius: 5px;
			font-family: inherit;
			box-sizing: border-box;
		}
		
		.input-group i {
			position: absolute;
			left: 15px;
			top: 50%;
			transform: translateY(-50%);
			color: #aaa;
		}

		button[type="submit"] {
			width: 100%;
			padding: 12px;
			border: none;
			border-radius: 25px;
			background: linear-gradient(to right, #ff4e00, #ff7f50);
			color: white;
			font-weight: bold;
			font-size: 1rem;
			cursor: pointer;
			transition: transform 0.2s;
			margin-top: 10px;
		}

		button[type="submit"]:hover {
			transform: scale(1.02);
		}

		/* Sugerencias API */
		.suggestions-box {
			position: absolute; top: 100%; left: 0; width: 100%;
			background: #fff; border: 1px solid #ddd; border-top: none;
			z-index: 1001; max-height: 200px; overflow-y: auto;
			box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px; 
			display: none;
		}
		.sugg-item { padding: 10px; display: flex; align-items: center; cursor: pointer; border-bottom: 1px solid #eee; }
		.sugg-item:hover { background-color: #f9f9f9; }
		.sugg-item img { width: 40px; margin-right: 10px; border-radius: 4px; }
	</style>

	<div class="container">
		<div class="form-card">
			<form action="controladores/guardar_contenido.php" method="POST">
				<input type="hidden" name="tipo" value="pelicula">
				
				<h1 class="form-title">Nueva Película</h1>
				
				<!-- Preview de la imagen seleccionada -->
				<div id="preview_container" style="text-align:center; display:none; margin-bottom:20px;">
					<img id="preview_img" src="" style="width:120px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
				</div>
				<input type="hidden" name="imagen_url" id="imagen_input">

				<div class="input-group">
					<input type="text" name="nombre" id="titulo_input" placeholder="Buscar título..." autocomplete="off" required />
					<i class="fa-solid fa-magnifying-glass"></i>
					<div id="suggestions" class="suggestions-box"></div>
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
						<option value="Por_ver" selected>Por ver</option>
						<option value="Vistas">Vista</option>
					</select>
					<i class="fa-solid fa-eye"></i>
				</div>

				<button type="submit">Guardar Película</button>
				
				<div style="text-align:center; margin-top:15px;">
					<a href="peliculas.php" style="color: #666; text-decoration:none; font-size:0.9rem;">
						Cancelar
					</a>
				</div>
			</form>
		</div>
	</div>

	<script>
		// API TMDB - Lógica de búsqueda
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
					const res = await fetch(`https://api.themoviedb.org/3/search/movie?api_key=${API_KEY}&language=es-ES&query=${query}`);
					const data = await res.json();
					suggestionsBox.innerHTML = '';

					if (data.results && data.results.length > 0) {
						suggestionsBox.style.display = 'block';
						data.results.slice(0, 5).forEach(movie => {
							const div = document.createElement('div');
							div.className = 'sugg-item';
							
							const title = movie.title;
							const year = movie.release_date ? movie.release_date.split('-')[0] : '';
							const poster = movie.poster_path ? `https://image.tmdb.org/t/p/w92${movie.poster_path}` : '';
							const fullPoster = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : '';

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

<?php include "inc/piedepagina.php" ?>
