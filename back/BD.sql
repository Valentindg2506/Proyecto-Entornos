CREATE TABLE usuario (
  id INT,
  nombre VARCHAR(255),
  apellidos VARCHAR(255),
  correo VARCHAR(255),
  contrasea VARCHAR(255),
  PRIMARY KEY (id)
);

CREATE TABLE contenido (
  id INT,
  usuario_id INT,
  titulo VARCHAR(255),
  tipo ENUM('pelicula', 'serie'),
  estado ENUM('vista', 'por_ver', 'viendo') NOT NULL DEFAULT 'por_ver',
  puntuacion VARCHAR(255),
  comentario VARCHAR(255),
  fecha_visualizacion VARCHAR(255),
  nivel_prioridad VARCHAR(10),
  PRIMARY KEY (id),
  CONSTRAINT fk_contenido_1 FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);
