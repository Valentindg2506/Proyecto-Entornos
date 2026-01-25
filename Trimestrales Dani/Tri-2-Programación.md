En el proyecto que hemos hecho, ya hemos explicado el `back` detrás del proyecto, con base de datos y demás, hemos explicado también la parte del `front` en lenguaje de marcas pero ahora tenemos que poner el foco en el motor que hace que funcione todo, la parte de programación, mediante `PHP` hemos hecho el motor del programa utilizando conexiones y funciones para hacer el programa.

---

La programación en un proyecto esta en todas partes y conecta todos los puntos de este, en nuestro caso como lenguaje de programación y más siendo una aplicación web hemos utilizado `PHP` como hemos mencionado antes, esto nos ayuda también a en un mismo archivo de `PHP` poder utilizar lenguajes de marcas y que sea mas sencillo.

Para comenzar hemos estado utilizando `PHP` para poder conectarnos a nuestra base de datos mediante unas credenciales de usuario que creamos en nuestra base de datos, explicadas ya en el trimestral de la asignatura _"Base de Datos"_ y mandar las acciones que queramos en esta, `PHP` sencillamente esta ocasión hace de intermediario creando un `cursor` en `MySQL` y escribiendo en función de `variables`, en estas se guarda información que en este caso es lo que quiera hacer el usuario en la aplicación, así dependiendo de lo que quiera hacer el usuario se harán unas funciones u otras:

_Conexión_

```
	<?php
		$host = "localhost";        // Servidor (suele ser localhost en XAMPP)
		$user = "AdminViews";       // Usuario de la BD
		$pass = "AdminViews123$";   // Contraseña del usuario
		$db   = "AdminViews";       // Nombre exacto de la base de datos

		$conexion = new mysqli($host, $user, $pass, $db);

		if ($conexion->connect_error) {
			die("Error crítico de conexión: " . $conexion->connect_error);
		}

		$conexion->set_charset("utf8");
	?>
```

Para las acciones a la vez que la conexión a la base de datos hemos utilizado estructuras de control para identificar las acciones, hemos añadido una estructura de control para capturar las acciones que se pueden hacer a referencia de las listas de las películas/series, tenemos varias opciones como mover entre columnas que se hacen mediante botones y agregar una película/serie que se hace mediante un botón que lleva a un formulario y al rellenarse se agrega a la base de datos:

_Ejemplo de acciones_

```
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
```

En relación a la base de datos también hemos añadido código para que las películas/series añadidas puedan ordenarse en diferentes formas, para ello creamos un desplegable en el cual el usuario tenia que elegir una opción esta la captura `PHP` y mediante comandos `SQL` se ordenan de una forma distinta en cada caso, además también hemos añadido una estructura de control para así poder saber que acción busca hacer el usuario, sabiendo que quiere hacer el usuario pedimos un `SELECT` con el orden captura y los mostramos en pantalla:

```
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
```

---

Capturas diferente orden de las tarjetas

---

También hemos utilizado bucle `foreach` para que el programa analice la lista de películas y cree una tarjeta por cada película/serie que haya en cada columna:

```
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
```
---

Captura tarjetas creadas

---

> Esto es un bucle controlado por lo que lo cerramos cuando terminen las películas/series de la lista

Esto lo utilizamos tanto en cada columna de las películas y series.

También al comienzo de cada página hemos utilizado un código para comprobar que el usuario a iniciado sesión y no a entrado mediante otros métodos, este bloque de código comprueba si existe una variable de sesión y en el caso de no existir lo redirige a otra página avisando de que conocemos que ha entrado sin iniciar sesión:

```
	<?php
	session_start();
	 
	if (!isset($_SESSION['usuario'])) {
		header("Location: intruso.php");
		exit;
	}
	?>
```

En el login utilizamos `PHP` para detectar errores a la hora de iniciar sesión, credenciales erróneas o al registrarse valores incorrectos para poder registrarse, lo que hace en este archivo es detectar los errores y los manda a otro archivo que el que procesa el registro o el login, estos archivos están enlazados dentro de un `<form>` de `HTML` en el que la propiedad `action` es este archivo, por lo que el código esta entre lazado entre `HTML` y `PHP`:

_Registro_

```
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
```

_LogIn_

```
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
```

Otras funciones que hemos utilizado son las inserciones de archivos para el ahorro de código utilizando la función `include` de `PHP` sobre todo a la hora de las cabeceras y pies de página como mencionamos en el examen de marcas:

```
	<?php include "inc/cabecera.php" ?>
```

```
	<?php include "inc/piedepagina.php" ?>
```

Estos dos archivos son el inicio y el final de una estructura básica de `HTML`.

Esto sería todo el contenido de programación que hay en las vistas, vayamos ahora con controladores y el panel de admim.

Empezando por los controladores que en este caso hemos utilizado 2 distintos, estos dos son para la parte de LogIn y registro:

Los controladores de inicio de sesión y registro utilizan simple estructuras de selección para detectar si lo rellenado en el formulario de inicio de sesión/registro es correcto si no detectar errores y devolverlo al archivo que mencionamos antes el cual estaba conectado con estos, allí ya se encarga mediante lenguaje de marcas en avisar visualmente al usuario, estas estructuras de selección se tratan de unos `if`/`elif`/`esle` y los errores se comprueban mediante una conexión a la base de datos comprobando por ejemplo, si el usuario esta creado o no cumple los requisitos el nombre de usuario, el correo o la contraseña, todo esto en el caso del registro o comprobar si el usuario existe y en común a la contraseña en el caso del inicio de sesión:

_Controlador Inicio de sesion_
```
	session_start();
	require_once '../inc/db.php'; // Conexión a BD

	if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
		
		$usuario = $_POST['usuario'];
		$pass_ingresada = $_POST['contrasena'];

		$sql = "SELECT id, usuario, contrasena FROM usuario WHERE usuario = ?";
		$stmt = $conexion->prepare($sql);
		$stmt->bind_param("s", $usuario); // "s" = string
		$stmt->execute();
		
		$resultado = $stmt->get_result();

		if ($fila = $resultado->fetch_assoc()) {
		    
		    $hash_guardado = $fila['contrasena']; // El hash encriptado de la BD

		    if (password_verify($pass_ingresada, $hash_guardado)) {
		    
		        $_SESSION['usuario'] = $fila['usuario'];
		        $_SESSION['id_usuario'] = $fila['id'];
		        
		        if ($fila['usuario'] === 'Admin1') {  // SI EL USUARIO ES ADMIN
		            header("Location: ../../admin/index.php"); // LO LLEVA A AL PANEL DE ADMIN
		        } else {
		            header("Location: ../exito.php"); // Ruta normal
		        }
		        
		        exit;
		    } else {
		        header("Location: ../index.php?error=1");
		        exit;
		    }

		} else {
		    header("Location: ../index.php?error=1");
		    exit;
		}

		$stmt->close();
		$conexion->close();

	} else {
		header("Location: ../index.php");
	}
```

_Controlador Registro_

```
	session_start();
	require_once '../inc/db.php'; // Conexión a BD

	$nombre = $_POST['nombrecompleto'];
	$usuario = $_POST['usuario'];
	$contrasena = $_POST['contrasena'];
	$correo = $_POST['email'];

	$errores = []; // Array para guardar fallos encontrados

	$usuario_seguro = mysqli_real_escape_string($conexion, $usuario);
	$sql_check = "SELECT * FROM usuario WHERE usuario = '$usuario_seguro'";
	$res_check = mysqli_query($conexion, $sql_check);

	if (mysqli_num_rows($res_check) > 0) {
		$errores['usuario'] = "Este usuario ya está ocupado. Elige otro.";
	}
		
	if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
		$errores['email'] = "El correo no tiene un formato válido.";
	}

	if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,16}$/', $contrasena)) {
		$errores['pass'] = "La contraseña debe tener 8-16 carácteres, 1 Mayúscula y 1 Símbolo";
	}

	if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9_]{5,20}$/', $usuario)) {
		$errores['usuario'] = "El usuario requiere 5-20 caracteres, al menos 1 mayúscula y 1 número.";
	}

	if (!empty($errores)) {
		$_SESSION['errores'] = $errores;
		$_SESSION['datos_viejos'] = $_POST; // Sticky Form: Para no tener que reescribir todo
		
		header("Location: ../index.php"); 
		exit;
	}

	$passHash = password_hash($contrasena, PASSWORD_DEFAULT);

	$sql = "INSERT INTO usuario (usuario, contrasena, nombre, correo) VALUES (?, ?, ?, ?)";
	$stmt = $conexion->prepare($sql);
	$stmt->bind_param("ssss", $usuario, $passHash, $nombre, $correo);

	if($stmt->execute()){
		// Registro correcto: Redirigimos al login con mensaje de éxito
		header("Location: ../index.php?registro=ok"); 
	} else {
		echo "Error BD: " . $conexion->error;
	}
```

---

Capturas inicio sesión y registro erróneos

---

Vayamos ahora con el panel de administrador, en este se vuelven a utilizar muchas de las cosas que hemos utilizado en el `front` pero para otras cosas.

Se utilizan `includes` para añadir en cada una de las vistas del panel un barra lateral y la conexión a la base de datos y también se utilizan bucles controlados, aunque en este caso no son `foreach` sino que bucles `while`, para listar usuarios, películas y series que hay en la base de datos en tablas combinandolo con `HTML`:

_Ejemplo de un include_

```
	<?php include "inc/sidebar.php"; ?>
```

_Ejemplo de una de las tablas_

```
	<?php if ($resultado->num_rows > 0): ?>
        <?php while($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if($row['imagen_url']): ?>
                        <img src="<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="Cover" class="img-tabla-mini">
                    <?php else: ?>
                        <div class="no-img-mini"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($row['titulo']); ?></strong></td>
                <td><span class="badge status-<?php echo strtolower($row['estado']); ?>"><?php echo $row['estado']; ?></span></td>
                <td><?php echo $row['puntuacion']; ?>/5</td>
                <td><?php echo $row['nivel_prioridad']; ?></td>
                <td>
                    <a href="#" class="btn-action edit"><i class="fas fa-edit"></i></a>
                    <a href="#" class="btn-action delete"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align:center;">No hay películas registradas.</td>
        </tr>
    <?php endif; ?>
```

---

Capturas del panel de admin y las tablas

---

En conclusión vemos como todo lo trabajado en otras materias no serviría de nada si no conseguimos llegar a una conexión entre estas, que en este caso es `PHP` esto nos permite afianzar la aplicación, es decir, hablando metafóricamente la base de datos sería el cerebro de la aplicación ya que se encargar de la información, el lenguaje de marcas sería la piel por que es lo que se ve y el que recibe las cosas y el `PHP` sería todos esos órganos que sirven para que todo el cuerpo funcione, en este caso nuestra aplicación, lo que trato de explicar es la importancia de la parte de programación.
