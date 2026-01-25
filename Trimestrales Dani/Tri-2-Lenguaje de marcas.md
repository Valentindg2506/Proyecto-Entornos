Nuestro proyecto de un kanban de series/películas (AdminViews) es un aplicación web por lo que esta compuesto de unas vistas, estas son lo que aparecen en pantalla cada que haces algo, estas están diseñadas con los lenguaje de marcas `HTML`, `CSS` y `JS`, además de `PHP` para hacerlo dinámico, esta parte del proyecto es lo que ve el usuario final a la hora de utilizar la aplicación.

---

Diagrama de flujo

---

El proyecto esta divido en dos partes, el front, que sería por donde navegará en nuestra aplicación un usuario ordinario, este podrá acceder a la parte final del proyecto la cuál la parte funcional de este, es donde se puede hacer lo que queremos que haga la aplicación y el admin, que es el panel de administrador el cual solo podrá acceder un administrador con sus credenciales, en este se pueden ver estadísticas, los usuarios y la información de estos.

Vamos a empezar explicando como estas diseñadas las vistas del front.

Estás vistas están diseñadas con una base en `HTML`, es decir, todo el lenguaje de marcas utilizado esta en una etiqueta `<html>` que define la página, luego utilizando etiquetas básicas como `<head>` y `<body>` que serían las principales y después dentro de estas habría mas.

```
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
```

Empecemos con el `<head>`, aquí se añaden sobre todo el titulo utilizando una etiqueta `<title>` conexiones con `<php>` a otros archivos para añadir el `CSS` o alguna plantilla de cabecera o pie de página, es decir en el `<head>` al ser el inicio de la página es donde se exportan y detallan las cosas que se van a utilizar en la página utilizando etiquetas como `<meta>` o `<link>`.

```
	<!doctype html>
	<html lang="es">
		<head>
		    <title>AdminViews</title>
		    <meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		    <link rel="icon" type="image/png" href="img/adminviews_favicon.png">
		    <link rel="stylesheet" href="css/estilo.css">
		</head>
```

Continuando con el `<body>`, en esta etiqueta puede a ver variedad de cosas y también se divide en 3 partes que serían `<header>`, `<main>` y `<footer>`, pero en series y películas la cabecera y el pie de página están estandarizados y utilizan el mismo, ya que lo exportamos utilizando `PHP` de la carpeta `inc/`, sin embargo el inicio de sesión no utiliza ni cabecera ni pie de página y la página principal utiliza una cabecera independiente pero si el pie de página estandarizado.

```
	<?php include "inc/piedepagina.php" ?>
```

_cabecera.php_
```
	<body>
        <header>
        	<img src="img/adminviews.png" class="logo-admin" alt="Logo AdminViews">
        	
        	<nav class="navegacion-header">
				<a href="exito.php">Inicio</a>
				<a href="series.php">Series</a>
				<a href="peliculas.php">Películas</a>
			</nav>
        	
	        <a href="exito.php" class="btn-inicio"><img src="img/iconovuelta.png" alt="Volver al Inicio" title="Inicio"></a> 
        </header>
        <main>
```

_piedepagina.php_
```
		</main>
		<footer>
			<div class="footer-content">
				<p>&copy; 2025 | Valentín De Gennaro - Daniel Oliveira Vidal</p>
			</div>
		</footer>
	</body>
```


Vayamos ahora con el `<main>` que es el desenlace de la página aquí es donde ocurre la magia se pueden añadir etiquetas como `<script>` para poner código de `JS`, etiquetas semánticas como `<section>`, `<article>` para así añadir secciones, menús y de más que con ayuda del `CSS` ajustaremos y para que aparezca de una forma ordenada a la vez que estética.

_dentro de main_
```
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
```

Las vistas toman orden y estética cuando en el `<head>` se exporta el estilo `CSS` de un archivo externo, este lo vinculamos mediante una etiqueta `<link>`.

```
	<link rel="stylesheet" href="css/estilo.css">
```

El estilo que tenemos escrito, se divide por secciones y secciones que definen cada parte de la página, en estas se va retocando cada parte y ordenándola y dejándola en el sitio que le corresponde de una manera que todo aquel que entre a la página vea todo bien y perfecto y a la vez de una manera intuitiva.

En este se usan propiedades `flex` en secciones para ordenarlas de manera cuadrática y ordenada, se usan propiedades de colores como `color` para definir el color de algo, `background-color` para definir el color del fondo de un objeto y demás propiedades, además de otras propiedades para alinear objetos que los centran, los transforman, añaden margenes respecto a algo y demás, como pueden ser las propiedades como `margin`, `padding`, `transition`, `width`, `align-items`... Haciendo así un archivo muy completo de estilo que complementa a la página y la deja de una forma clara e intuitiva.

**Ejemplo de CSS**

```
	.btn-inicio img {
		width: 100%;
		height: 100%;
		object-fit: contain;
	}
```

```
	.navegacion-header {
		display: flex;
		gap: 25px; /* Separación entre las palabras */
		align-items: center;
	}
```

```
	.card-body p {
		font-size: 1rem;
		line-height: 1.5;
		color: #333;
		margin-bottom: 25px;
	}
```

```
	.img-serie {
		width: 60px;
		height: 60px;
		object-fit: cover;
		border-radius: 5px;
		border: 1px solid #ccc;
	}
```

También utilizamos una herramienta muy util que es una propiedad del `CSS` que se exporta con un `<meta>` que es el `viewport` que se utiliza para que la página se ajuste a dispositivos moviles:

```
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

```
	@media (max-width: 768px) {
		header {
		    flex-direction: column; /* Cambia a vertical */
		    gap: 15px; /* Espacio entre logo, menú y botón */
		    padding: 15px;
		}

		.navegacion-header {
		    flex-direction: row; /* Mantiene los enlaces en fila */
		    gap: 15px;
		    font-size: 0.9rem;
		}
	}
```

Por ultimo en lenguaje de marcas hemos utilizado `JS` para realizar eventos puntuales por los archivos en complementación al `CSS` como puede ser la animación en el inicio de sesión que cambia de inicio de sesión a registro y viceversa o a la hora de pasar una película/seria a la columna `Vistas` la calificación en estrellas que se le atribuye.

```
	<script>
		// Referencias al DOM
		const signUpButton = document.getElementById('signUp');
		const signInButton = document.getElementById('signIn');
		const container = document.getElementById('container');

		// 1. Evento para mostrar panel de Registro
		signUpButton.addEventListener('click', () => {
			container.classList.add("right-panel-active");
		});

		// 2. Evento para mostrar panel de Login
		signInButton.addEventListener('click', () => {
			container.classList.remove("right-panel-active");
		});

		/**
		 * LÓGICA MIXTA PHP/JS:
		 * Si PHP detecta que hubo errores en el registro ($errores no está vacío),
		 * inyectamos JS para activar el panel de registro automáticamente.
		 * Esto evita que el usuario tenga que volver a hacer clic en "Registrarse"
		 * para ver sus errores.
		 */
		<?php if (!empty($errores)): ?>
			container.classList.add("right-panel-active");
		<?php endif; ?>
	</script>
```

Además también se ha utilizado en los formularios de películas/series como una api para la lógica de búsqueda de estas. 

```
	<script>
		// API TMDB - Lógica de búsqueda (SERIES)
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
					// endpoint: search/tv
					const res = await fetch(`https://api.themoviedb.org/3/search/tv?api_key=${API_KEY}&language=es-ES&query=${query}`);
					const data = await res.json();
					suggestionsBox.innerHTML = '';

					if (data.results && data.results.length > 0) {
						suggestionsBox.style.display = 'block';
						data.results.slice(0, 5).forEach(serie => {
							const div = document.createElement('div');
							div.className = 'sugg-item';
							
							// Para series es 'name' y 'first_air_date'
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
```

---

En conclusión vemos como los lenguajes de marcas son la parte que materializa la aplicación en lo que vamos a ver en pantalla, utilizando los diferentes lenguajes para que quede una interfaz bonita y estética a la vez que funcional, ordenada e intuitiva, haciendo así una experiencia del usuario placentera y unica.


