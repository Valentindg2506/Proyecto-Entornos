CREATE DATABASE IF NOT EXISTS CRM
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE CRM;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    nombrecompleto VARCHAR(150) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usuarios (usuario, contrasena, nombrecompleto, email)
VALUES
('admin', 'admin123', 'admin', 'admin@tinoprop.com');

+----+---------+------------+----------------+--------------------+---------------------+
| id | usuario | contrasena | nombrecompleto | email              | creado_en           |
+----+---------+------------+----------------+--------------------+---------------------+
|  1 | admin   | admin123   | admin          | admin@tinoprop.com | 2025-12-10 17:20:52 |
+----+---------+------------+----------------+--------------------+---------------------+
