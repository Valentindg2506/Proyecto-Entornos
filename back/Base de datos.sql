CREATE DATABASE AdminViews;
USE AdminViews;

-- Tablas
CREATE TABLE usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(255),
	usuario VARCHAR(255),
	correo VARCHAR(255),
	contrasena VARCHAR(255)
);

ALTER TABLE usuario ADD token VARCHAR(255) NULL;

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
ALTER TABLE contenido 
ADD COLUMN imagen_url VARCHAR(255) NULL;


-- usuario

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
