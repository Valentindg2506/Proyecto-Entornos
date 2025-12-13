CREATE TABLE usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(255),
	apellidos VARCHAR(255),
	usuario VARCHAR(255),
	correo VARCHAR(255),
	contrasena VARCHAR(255)
);

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
