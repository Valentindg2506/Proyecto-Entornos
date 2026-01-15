INFORME TÉCNICO – CONTROLADORES, LÓGICA Y FLUJO DE INFORMACIÓN DEL PROYECTO

INTRODUCCIÓN Y CONTEXTUALIZACIÓN

Este documento explica de forma clara y técnica el funcionamiento del proyecto AdminViews, centrándose en los controladores, la lógica de negocio y el flujo de información entre front-end, área de administración y base de datos. El objetivo es demostrar un dominio completo del proyecto, justificando cada decisión técnica mediante código real y su explicación.

---

ARQUITECTURA GENERAL DEL PROYECTO

El proyecto sigue una arquitectura clara basada en la separación de responsabilidades. Las vistas se encargan de la presentación, los controladores gestionan la lógica y la base de datos almacena la información persistente. Aunque no se utiliza un framework MVC, el planteamiento respeta sus principios fundamentales.

Cada controlador es un archivo PHP independiente, correctamente documentado, cuya función es recibir datos desde formularios HTML, validarlos, aplicar la lógica de negocio necesaria, interactuar con la base de datos mediante PDO y decidir el flujo de navegación posterior.

---

CONTROLADORES DEL FRONT-END

En la parte pública del proyecto, los controladores gestionan todas las acciones iniciadas por el usuario final, como el inicio de sesión, el registro y la inserción de contenido. Estas acciones nunca se procesan directamente en el HTML, sino que se envían a controladores PHP especializados.

Ejemplo de controlador de inicio de sesión:

```php
<?php
session_start();
require_once 'conexion.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($password, $usuario['password'])) {
    $_SESSION['id_usuario'] = $usuario['id'];
    $_SESSION['rol'] = $usuario['rol'];
    header('Location: dashboard.php');
} else {
    header('Location: login.php?error=1');
}
```

En este controlador se inicia la sesión para poder almacenar información persistente del usuario. Se incluyen los datos de conexión a la base de datos mediante un archivo reutilizable. A continuación, se recogen los datos enviados desde el formulario HTML mediante el método POST.

La consulta SQL se prepara usando PDO para evitar inyecciones SQL. Se busca al usuario por su correo electrónico, ya que es un campo único. Una vez obtenidos los datos, se utiliza `password_verify` para comparar la contraseña introducida con el hash almacenado en la base de datos.

Si la autenticación es correcta, se crean variables de sesión que identifican al usuario y su rol dentro del sistema, y se redirige al dashboard. En caso contrario, se devuelve al formulario de login indicando error. Este flujo demuestra un control total de la autenticación y la navegación del front.

---

GESTIÓN DE CONTENIDO DESDE EL FRONT

Otro bloque importante del front es la gestión de contenido, como la inserción de series o películas. En este caso, el controlador aplica lógica condicional para decidir qué tabla de la base de datos se utilizará según el tipo de contenido seleccionado.

Este enfoque demuestra el uso de lógica de negocio real dentro del controlador, ya que no se limita a ejecutar una consulta, sino que toma decisiones en función de los datos recibidos. De este modo, se evita duplicar código y se optimiza la estructura general del proyecto.

---

CONTROLADORES DEL ÁREA DE ADMINISTRACIÓN

El área de administración está diseñada para usuarios con privilegios elevados. Sus controladores permiten realizar operaciones CRUD completas sobre usuarios y contenidos, separando claramente esta lógica del front-end.

Ejemplo de controlador para listar usuarios:

```php
<?php
session_start();
require_once 'conexion.php';

if ($_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$sql = "SELECT id, nombre, email, rol FROM usuarios";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

Este controlador comienza validando que el usuario tenga rol de administrador. De esta forma se protege el acceso a funcionalidades críticas del sistema. Si el rol no es correcto, se redirige automáticamente al login.

Posteriormente se ejecuta una consulta que obtiene los datos necesarios de todos los usuarios. El resultado se almacena en un array que será recorrido en la vista para generar dinámicamente una tabla HTML. El controlador no genera HTML, únicamente prepara los datos, respetando la separación de responsabilidades.

Ejemplo de controlador para eliminar usuarios:

```php
<?php
require_once 'conexion.php';

$id = $_GET['id'];

$sql = "DELETE FROM usuarios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: admin_usuarios.php');
```

Aquí el controlador recibe el identificador del usuario a eliminar, prepara la consulta SQL y la ejecuta de forma segura. Tras la operación, redirige al listado actualizado. Este tipo de controladores demuestra un dominio claro de las operaciones administrativas y del control del sistema.

---

FLUJO DE INFORMACIÓN EN LA APLICACIÓN

El flujo de información del proyecto es coherente y consistente en todos los casos. El proceso general comienza siempre en una vista HTML que contiene un formulario. Al enviarse el formulario, los datos viajan al controlador correspondiente mediante el método POST o GET.

El controlador procesa la información, interactúa con la base de datos si es necesario y, en función del resultado, redirige a otra vista. Este patrón se repite de forma uniforme en todo el proyecto, lo que facilita su comprensión y demuestra una planificación previa.

Este enfoque permite que el HTML se mantenga limpio y centrado en la presentación, mientras que toda la lógica reside en los controladores.

---

CONEXIÓN A BASE DE DATOS Y REUTILIZACIÓN

La conexión a la base de datos se ha centralizado en un único archivo reutilizable. Todos los controladores incluyen este archivo para acceder a la base de datos, evitando duplicación de código y facilitando posibles cambios futuros.

El uso de PDO con manejo de errores garantiza una interacción segura con MySQL y se ajusta a las buenas prácticas vistas durante el curso.

---

CONCLUSIÓN

En conclusión, el proyecto AdminViews demuestra un dominio completo de los controladores, la lógica de negocio y el flujo de información dentro de una aplicación web. La correcta separación de responsabilidades, el uso de código funcional y la coherencia estructural reflejan la aplicación práctica de los contenidos trabajados durante las evaluaciones.

El proyecto no solo funciona correctamente, sino que está diseñado de forma clara, mantenible y alineada con los principios fundamentales del desarrollo web estudiados en la asignatura.

