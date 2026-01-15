Tenemos un proyecto base de un kanban de series/películas que hemos realizado en la asignatura de **Entornos de Desarrollo**, este proyecto lleva detrás una base de datos para poder almacenar todo el contenido de las series o películas que el usuario hace, es decir, las series/películas que ponga en su lista además de si están en un estado, es decir, `por ver`, `viendo`(En el caso de las series) o `Vistas`.

---

En la base de todo tenemos una base de datos sobre el proyecto, en este caso la hemos llamado `AdminViews` que es como se llama nuestra aplicación:

```
	CREATE DATABASE AdminViews;
```

En esta vamos a crear dos tablas una usuario y otra contenido, en la usuario almacenaremos todo la información de los usuarios que se registren y en la de contenido se almacenará todas las series/películas que añada el usuario a su lista.

Vayamos primero con la de usuario, esta estará estructura de forma que la tabla almacene nombre de usuario, correo, nombre completo del usuario y la contraseña hasheada, siendo estas en formato `VARCHAR` para almacenar toda esta información, también tiene una columna `id` que será una clave primaría que se auto incremente realizará que no haya ningún `id` repetido, este `id` será un número entero por lo que la columna estará en formato `INT`:

```
	CREATE TABLE usuario (
		id INT AUTO_INCREMENT PRIMARY KEY,
		nombre VARCHAR(255),
		usuario VARCHAR(255),
		correo VARCHAR(255),
		contrasena VARCHAR(255) 
	);
```

Después alteramos la tabla para añadir un token a la tabla, que nos ayudará más adelante para la validación de saber si se ha iniciado sesión o no:

```
	ALTER TABLE usuario ADD token VARCHAR(255) NULL
```

Ahora haremos la tabla de contenido esta tendrá diferentes columnas, primero de todo tendrá un `id` igual al de la tabla `usuario`, además de una columna `id_usuario` con una clave foranea que vincule el contenido con cada usuario.

Luego tendremos 4 columnas en formato `VARCHAR` que almacenarán el titulo, la puntuación, el comentario que se haga de la serie/película y la fecha de visualización.

También tenemos 3 columnas en formato `ENUM` que es una lista de posibles estados, esta obliga a que el dato almacenado sea uno predefinido en la lista, las columnas en las que utilizamos esto es en el tipo, si es película o serie lo que se almacena; el estado, si la serie/película esta "por ver","viendo" o "vista"; y en el nivel de prioridad que se le da a esa serie/película, esta puede ser "alta", "media", o "baja.

Además en las columnas `estado` y `nivel_priorida` las definimos como `NOT NULL` para que tengan un valor siempre y definimos por defecto uno de los estados utilizando `DEFAULT`.

Por ultimo hemos añadido a la tabla un `CONSTRAINT` para evitar que se cree contenido para un usuario ya exitente:

```
	CREATE TABLE contenido (
	id INT AUTO_INCREMENT PRIMARY KEY, 
	usuario_id INT,  
	titulo VARCHAR(255),
	tipo ENUM('pelicula', 'serie'),
	estado ENUM('Por_ver', 'Viendo', 'Vistas') NOT NULL DEFAULT 'Por_ver', 
	puntuacion VARCHAR(255),
	comentario VARCHAR(255),
	fecha_visualizacion VARCHAR(255),
	nivel_prioridad ENUM('Alta', 'Media', 'Baja') NOT NULL DEFAULT 'Media',
	
	CONSTRAINT fk_contenido_1 FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);
```

Ahora la alteraremos para añadir un columna en la cual podamos añadir la URL de las imágenes de las películas:

```
	ALTER TABLE contenido 
	ADD COLUMN imagen_url VARCHAR(255) NULL;
```

Ya tenemos estructurada nuestra base de datos ahora para que próximamente podamos conectarnos a ella creamos un usuario en la base de datos.

Al crear el usuario le atribuiremos una serie de permisos con los cuales más adelante podremos añadir, mostrar, actualizar o eliminar cosas de nuestra base de datos:

```
	CREATE USER 
	'AdminViews'@'localhost' 
	IDENTIFIED  BY 'AdminViews123$';

	GRANT USAGE ON *.* TO 'AdminViews'@'localhost';

	ALTER USER 'AdminViews'@'localhost' 
	REQUIRE NONE 
	WITH MAX_QUERIES_PER_HOUR 0 
	MAX_CONNECTIONS_PER_HOUR 0 
	MAX_UPDATES_PER_HOUR 0 
	MAX_USER_CONNECTIONS 0;

	GRANT ALL PRIVILEGES ON AdminViews.* 
	TO 'AdminViews'@'localhost';

	FLUSH PRIVILEGES;
```

Ahora vayamos con los comandos que tenemos repartidos por todo el proyecto, estos los utilizamos para el registro de usuarios, la comprobación en los login, para que el usuario pueda añadir, ver, actualizar o eliminar las series/películas en su lista y demás cosas.

Empecemos con los comando utilizados en los controladores, tenemos tres controladores que son `guardar_contenido`, `login_procesa` y `registro_procesa`, en estos se usan comando básicos como `SELECT * FROM`, `UPDATE` o `DELETE FROM`.

Empecemos en `guardar_contenido` que utiliza un `INSERT` para añadir el contenido de la serie/película que se quiera almacenar, este espera la información y la inserta en la tabla `contenido`

```
	INSERT INTO contenido (usuario_id, titulo, comentario, estado, tipo, nivel_prioridad, imagen_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
```

En `login_procesa` comprobamos que el contenido añadido por el usuario sea igual al de la base de datos, de compararlo se encarga `PHP` pero utilizaremos un `SELECT` para sacar la información que queremos comprobar de la base de datos:

```
	SELECT id, usuario, contrasena FROM usuario WHERE usuario = ?
```

En `registro_procesa` utilizaremos un `INSERT` que añada el usuario nuevo en la base de datos, pero antes de eso utilizaremos un `SELECT * FROM` para evitar que haya un usuario repetido, de la comparación de nuevo se ocupa PHP, sql solo sacará la información de la base de datos:

```
	SELECT * FROM usuario WHERE usuario = '$usuario_seguro'
```

```
	INSERT INTO usuario (usuario, contrasena, nombre, correo) VALUES (?, ?, ?, ?)
```

> Todos los interrogantes son variables utilizadas en PHP

A continuación tenemos las páginas de series y en películas en las cuales se utilizan los mismos comandos en las dos páginas.

En estas páginas utilizamos diferentes comandos que serian, un `SELECT * FROM` para mostrar todo el contenido ya añadido:

```
	SELECT * FROM contenido WHERE tipo = 'pelicula' AND usuario_id = ? $sql_order
```

Después tambien tenemos un botón en PHP para poder ordenar como aparecen las series/películas para esto utilizaremos un `ORDER` en los casos que queremos que son: orden alfabético, orden por prioridad, o el predefinido que el orden de inserción:

```
	ORDER BY id DESC
```

```
	ORDER BY titulo ASC
```

```
	ORDER BY FIELD(nivel_prioridad, 'Alta', 'Media', 'Baja')
```

Luego a la hora de mover de columnas a las series/películas utilizamos un `UPDATE` que actualizará el estado de la misma:

```
	UPDATE contenido SET estado='Vistas', fecha_visualizacion=?, puntuacion=? WHERE id=?
```

Además que tenemos un botón para eliminar alguna de estas, esto lo realizamos con un simple `DELETE`:

```	
	DELETE FROM contenido WHERE id=?
```

Mediante el formulario hecho con HTML y PHP cuando el usuario termine de rellenarlo añadiremos la serie/película utilizando un `INSERT`:

```
	INSERT INTO contenido (usuario_id, titulo, comentario, estado, tipo, nivel_prioridad, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?)
```

Esto sería todo el funcionamiento de `sql` en el `front` de la página, ahora dentro del `back` utilizamos diferente `SELECT` para mostrar contenido sobre todos los usuarios y los contenido para así hacer ciertas estadísticas, utilizando en estos `SELECT` más `ORDER` con esto sacaremos la información de la base de datos para que PHP pueda procesar las diferentes estadísticas que se muestren en el panel de admin, algunos de estos comandos son los siguientes:

```
	SELECT COUNT(*) as total FROM usuario	
```

```
	SELECT COUNT(*) as total FROM contenido WHERE tipo = 'serie'
``` 

``` 
	SELECT COUNT(*) as total FROM contenido WHERE tipo = 'pelicula'
```

```
	SELECT estado, COUNT(*) as cantidad FROM contenido GROUP BY estado
```

```
	SELECT titulo, COUNT(*) as total FROM contenido WHERE tipo = 'serie' AND estado = 'Vistas' GROUP BY titulo ORDER BY total DESC LIMIT 10
```

```
	SELECT titulo, COUNT(*) as total FROM contenido WHERE tipo = 'pelicula' AND estado = 'Vistas' GROUP BY titulo ORDER BY total DESC LIMIT 10
```

```
	SELECT * FROM contenido WHERE tipo = 'pelicula'
```

```
	SELECT * FROM contenido WHERE tipo = 'serie'
```

```
	SELECT * FROM usuario
```

---

En conclusión la base de datos nos ayuda al almacenamiento de datos en un sitio localizado y fácil de acceder, además con la ayuda de sql nos ayuda a conectarlo con PHP y así procesar estos datos.

	


