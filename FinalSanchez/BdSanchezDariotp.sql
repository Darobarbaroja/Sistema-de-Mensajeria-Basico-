CREATE DATABASE BdSanchezDariotp;
USE BdSanchezDariotp;

-- Tabla de estados de mensajes
CREATE TABLE estados (
    id TINYINT(4),
    estado VARCHAR(30),
    PRIMARY KEY(id)
);

-- Tabla de roles de usuario
CREATE TABLE roles (
    id TINYINT(4),
    rol VARCHAR(30),
    PRIMARY KEY(id)
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    idUsuario INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(30),
    apellido VARCHAR(30),
    usuario VARCHAR(30),
    pass VARCHAR(100),
    rol TINYINT(4),
    PRIMARY KEY(idUsuario),
    FOREIGN KEY (rol) REFERENCES roles(id),
    UNIQUE (usuario)
);

-- Tabla de mensajes
CREATE TABLE mensajes (
    idMensaje INT(11) NOT NULL AUTO_INCREMENT,
    de INT(11),
    para INT(11),
    asunto VARCHAR(100),
    mensaje VARCHAR(500),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    origen INT(11),
    estado TINYINT(4),
    PRIMARY KEY(idMensaje),
    FOREIGN KEY(de) REFERENCES usuarios(idUsuario),
    FOREIGN KEY(para) REFERENCES usuarios(idUsuario),
    FOREIGN KEY(estado) REFERENCES estados(id)
);

-- Tabla de accesos (para registrar logins)
CREATE TABLE accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50),
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    minutos INT
);

-- Insertar roles
INSERT INTO roles (id, rol) VALUES 
(1, 'administrador'),
(2, 'usuario');

-- Insertar estados de mensajes
INSERT INTO estados (id, estado) VALUES 
(0, 'no leído'),
(1, 'leído'),
(2, 'eliminado');

-- Insertar usuario administrador y un usuario normal (contraseñas simples por ahora)
INSERT INTO usuarios (nombre, apellido, usuario, pass, rol) VALUES
('Dario', 'Sanchez', 'Daro', 'daro123', 1),
('Pepe', 'González', 'pepe', 'pepe123', 2),
('Omar', 'López', 'omar', 'omar123', 2),
('Hugo', 'Pérez', 'hugo', 'hugo123', 2),
('Ana', 'Martínez', 'ana', 'ana123', 2),
('Luca', 'Ramírez', 'luca', 'luca123', 2),
('Sofi', 'Fernández', 'sofi', 'sofi123', 2);


