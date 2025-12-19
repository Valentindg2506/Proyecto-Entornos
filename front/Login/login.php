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

<body>
	<div class="container" id="container">
		<div class="form-container sign-up-container">
			<form action="registro_procesa.php" method="POST">
				<h1>Crear Cuenta</h1>
				<div class="input-group">
					<input type="text" name="nombrecompleto" placeholder="Nombre Completo" required />
					<i class="fa-solid fa-user"></i>
				</div>
				<div class="input-group">
					<input type="email" name="email" placeholder="Email" required />
					<i class="fa-solid fa-envelope"></i>
				</div>
				<div class="input-group">
					<input type="text" name="usuario" placeholder="Usuario" required />
					<i class="fa-solid fa-user-tag"></i>
				</div>
				<div class="input-group">
					<input type="password" name="contrasena" placeholder="Contraseña" required />
					<i class="fa-solid fa-lock"></i>
				</div>
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
						<?php if(isset($_GET['error'])) echo 'style="border: 3px solid red;"'; ?>
					/>
					<i class="fa-solid fa-lock"></i>

				</div>
				<a href="#">¿Olvidaste tu contraseña?</a>
				<button type="submit">Iniciar Sesión</button>
				<?php
					if (isset($_GET['error'])) {
						echo "<p style='color: red; font-weight: bold;'>Contraseña incorrecta</p>";
					}
				?>
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
	</script>
</body>

</html>
