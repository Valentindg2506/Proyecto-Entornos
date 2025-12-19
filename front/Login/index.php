<?php
	session_start(); 

	// Recuperamos errores y datos viejos si vienen del procesador
	$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
	// Aquí estaba el error:
	$datos = isset($_SESSION['datos_viejos']) ? $_SESSION['datos_viejos'] : [];

	// Borramos la sesión para limpiar errores al recargar
	unset($_SESSION['errores']);
	unset($_SESSION['datos_viejos']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<title>AdminViews</title>
</head>

<body class="login-page">
	<div class="container" id="container">
		
		<div class="form-container sign-up-container">
			<form action="registro_procesa.php" method="POST">
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
			<form action="login_procesa.php" method="POST">
				<h1>Iniciar Sesión</h1>
				<div class="input-group">
					<input type="text" name="usuario" placeholder="usuario" required />
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
		const signUpButton = document.getElementById('signUp');
		const signInButton = document.getElementById('signIn');
		const container = document.getElementById('container');

		signUpButton.addEventListener('click', () => {
			container.classList.add("right-panel-active");
		});

		signInButton.addEventListener('click', () => {
			container.classList.remove("right-panel-active");
		});

		// FIX IMPORTANTE:
		// Si PHP detecta errores en el registro ($errores no está vacío),
		// forzamos al panel a mostrarse en el lado de "Registro" automáticamente.
		<?php if (!empty($errores)): ?>
			container.classList.add("right-panel-active");
		<?php endif; ?>
	</script>
</body>
</html>
