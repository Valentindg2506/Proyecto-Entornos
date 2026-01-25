<?php
/**
 * 
 * PÁGINA DE ATERRIZAJE (LOGIN / REGISTRO)
 * 
 * Propósito: 
 * 1. Mostrar formuarios de acceso y creación de cuenta.
 * 2. Gestionar la persistencia de datos (Sticky Form) si falla el registro.
 */
session_start(); 

// Recuperamos errores y datos previos si existen en la sesión (vienen de registro_procesa.php)
// Esto permite que si te equivocas, no tengas que volver a escribir todo el formulario.
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
$datos = isset($_SESSION['datos_viejos']) ? $_SESSION['datos_viejos'] : [];

// Limpiamos la sesión: Si recargas la página (F5), los errores desaparecen.
unset($_SESSION['errores']);
unset($_SESSION['datos_viejos']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="AdminViews - Tracker de series y peliculas">
	<meta name="keywords" content="Peliculas, Series, Seguimiento, Tracker, series y peliculas">
	<meta name="author" content="Valentin de Gennaro, Daniel Oliveira Vidal">
	<meta property="og:title" content="AdminViews - Tracker de series y peliculas">
	<meta property="og:description" content="AdminViews - Tracker de series y peliculas">
	<link rel="icon" type="image/png" href="img/adminviews_favicon.png">
	<link rel="stylesheet" href="css/estilo.css">
	<link rel="stylesheet" href="style/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<title>AdminViews - Acceso</title>
</head>

<body class="login-page">
	
	<div class="container" id="container">
		
		<div class="form-container sign-up-container">
			<form action="controladores/registro_procesa.php" method="POST">
				<h1>Crear Cuenta</h1>
				
				<div class="input-group">
					<input type="text" name="nombrecompleto" placeholder="Nombre Completo" 
						value="<?php echo isset($datos['nombrecompleto']) ? htmlspecialchars($datos['nombrecompleto']) : ''; ?>" 
						required />
					<i class="fa-solid fa-user"></i>
				</div>

				<div class="input-group">
					<input type="email" name="email" placeholder="Email" required
						value="<?php echo isset($datos['email']) ? htmlspecialchars($datos['email']) : ''; ?>"
						/* Feedback visual: Borde rojo si hay error específico en email */
						style="<?php echo isset($errores['email']) ? 'border: 2px solid red;' : ''; ?>"
					/>
					<i class="fa-solid fa-envelope"></i>
				</div>
				<?php if(isset($errores['email'])): ?>
					<small style="color:red; font-size: 0.7em;"><?php echo $errores['email']; ?></small>
				<?php endif; ?>

				<div class="input-group">
					<input type="text" name="usuario" placeholder="Usuario" 
						value="<?php echo isset($datos['usuario']) ? htmlspecialchars($datos['usuario']) : ''; ?>"
						style="<?php echo isset($errores['usuario']) ? 'border: 2px solid red;' : ''; ?>"
						required />
					<i class="fa-solid fa-user-tag"></i>
				</div>
				<?php if(isset($errores['usuario'])): ?>
					<small style="color:red; font-size: 0.7em;"><?php echo $errores['usuario']; ?></small>
				<?php endif; ?>

				<div class="input-group">
					<input type="password" name="contrasena" placeholder="Contraseña" required
						style="<?php echo isset($errores['pass']) ? 'border: 2px solid red;' : ''; ?>"
					/>
					<i class="fa-solid fa-lock"></i>
				</div>
				<?php if(isset($errores['pass'])): ?>
					<small style="color:red; font-size: 0.7em;"><?php echo $errores['pass']; ?></small>
				<?php endif; ?>

				<button type="submit">Registrarse</button>
			</form>
		</div>

		<div class="form-container sign-in-container">
			<form action="controladores/login_procesa.php" method="POST">
				<h1>Iniciar Sesión</h1>
				
				<div class="input-group">
					<input type="text" name="usuario" placeholder="Usuario" required />
					<i class="fa-solid fa-user"></i>
				</div>

				<div class="input-group">
					<input type="password" name="contrasena" placeholder="Contraseña" required
						<?php if(isset($_GET['error'])) echo 'style="border: 2px solid red;"'; ?>
					/>
					<i class="fa-solid fa-lock"></i>
				</div>
				
				<?php
					if (isset($_GET['error'])) {
						echo "<p style='color: red; font-size: 0.8em; margin-top:5px;'>Usuario o contraseña incorrectos</p>";
					}
				?>
				<button type="submit">Iniciar Sesión</button>
			</form>
		</div>

		<div class="overlay-container">
			<div class="overlay">
				<div class="overlay-panel overlay-left">
					<h1>¡Hola, Bienvenido!</h1>
					<p>Introduce tus datos personales y comienza tu experiencia con nosotros.</p>
					<button class="ghost" id="signIn">Iniciar Sesión</button>
				</div>
				<div class="overlay-panel overlay-right">
					<h1>¡Bienvenido de nuevo!</h1>
					<p>Para mantenerte conectado con nosotros, por favor inicia sesión con tus datos.</p>
					<button class="ghost" id="signUp">Registrarse</button>
				</div>
			</div>
		</div>
	</div>

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
</body>
</html>
