# Informe del Proyecto: AdminViews

Este informe detalla el análisis del proyecto **AdminViews**, una aplicación web para la gestión y seguimiento de series y películas personal.

## 1. Información General
*   **Nombre del Proyecto:** AdminViews
*   **Propósito:** Tracker personal de contenido audiovisual (series y películas). Permite a los usuarios registrarse, iniciar sesión y organizar su historial de visualización.
*   **Autores:** Valentin De Gennaro, Daniel Oliveira.
*   **Repositorio/Ubicación:** `https://github.com/Valentindg2506/Proyecto-Entornos`

## 2. Estructura del Proyecto
El proyecto está organizado de manera modular, separando la interfaz de usuario (`front`) del panel de administración (`admin`) y la estructura de datos.

### 2.1. Directorios Principales
*   **`/front`**: Contiene la lógica y vistas de la aplicación principal para los usuarios finales.
*   **`/admin`**: Contiene herramientas de administración y los archivos de definición de la base de datos.
*   **`/screenshots`**: Almacena capturas de pantalla de la aplicación para documentación.

### 2.2. Análisis de Archivos Clave
#### En `/front` (Aplicación de Usuario)
*   **`index.php`**: Punto de entrada. Maneja el inicio de sesión y registro de usuarios.
*   **`exito.php`**: Dashboard principal. Se muestra tras un login exitoso.
*   **`peliculas.php` / `series.php`**: Vistas principales para listar y filtrar el contenido del usuario.
*   **`formulario_pelicula.php` / `formulario_serie.php`**: Interfaces para añadir o editar registros.
*   **`/inc`**: Archivos de inclusión común:
    *   `db.php`: Conexión centralizada a la base de datos.
    *   `cabecera.php` / `piedepagina.php`: Elementos UI compartidos.
*   **`/controladores`**: Scripts PHP que procesan las peticiones (POST) de los formularios (login, registro, etc.).

#### En `/admin` (Administración y Datos)
*   **`/BBDD`**: Contiene los scripts SQL para la creación de la base de datos (`Base de datos.sql`, `BD.sql`) y diagramas (`BD.svg`).
*   **`usuarios.php`**: Gestión de usuarios registrados.
*   **`peliculas.php` / `series.php`**: Vistas administrativas del contenido.

## 3. Tecnologías Utilizadas
El proyecto utiliza un stack clásico de desarrollo web:

*   **Lenguaje Servidor:** PHP (Nativo, sin uso de frameworks pesados).
*   **Base de Datos:** MySQL.
*   **Frontend:**
    *   HTML5 (Estructura semántica).
    *   CSS3 (Estilos personalizados, animaciones, diseño responsivo).
    *   JavaScript (Interactividad y consumo de APIs).
*   **APIs Externas:** TheMovieDB (TMDB) API para obtener metadatos e imágenes de películas-series automáticamente.

## 4. Base de Datos
El esquema de datos (definido en `admin/BBDD/Base de datos.sql`) es relacional y conciso.

**Nombre de la BD:** `AdminViews`

### Tablas
1.  **`usuario`**
    *   Gestión de credenciales y perfiles.
    *   Columnas: `id` (PK), `nombre`, `usuario`, `correo`, `contrasena` (Hash), `token`.

2.  **`contenido`**
    *   Almacena cada entrada de serie o película por usuario.
    *   Columnas: `id` (PK), `usuario_id` (FK), `titulo`, `tipo` ('pelicula', 'serie'), `estado` ('Por_ver', 'Viendo', 'Vistas'), `puntuacion`, `comentario`, `fecha_visualizacion`, `nivel_prioridad`, `imagen_url`.

### Seguridad DB
*   Se crea un usuario específico `AdminViews` con permisos limitados y contraseña definida para la conexión desde la aplicación.

## 5. Funcionalidad y Flujo
1.  **Acceso:** El usuario ingresa en `index.php`. Si no tiene cuenta, se registra. Las contraseñas se almacenan encriptadas (`password_hash`).
2.  **Dashboard:** Al ingresar, `exito.php` presenta un resumen o menú principal.
3.  **Gestión (CRUD):**
    *   El usuario puede buscar una película/serie (autocompletado por la API).
    *   Al guardar, se crea un registro en la tabla `contenido`.
    *   El usuario puede cambiar el estado (ej. de "Por ver" a "Vistas"), puntuar y comentar.
4.  **Persistencia:** Todos los datos se guardan en MySQL y se recuperan mediante consultas PHP en las vistas correspondientes.
