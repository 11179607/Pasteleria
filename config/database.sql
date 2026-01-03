-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS pasteleria;
USE pasteleria;

-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    pastel_basico INT DEFAULT 0,
    pastel_mediano INT DEFAULT 0,
    pastel_grande INT DEFAULT 0,
    estado ENUM('recepcionado', 'despachado') DEFAULT 'recepcionado',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_pedido)
);

-- Insertar datos de ejemplo
INSERT INTO pedidos (nombre_cliente, pastel_basico, pastel_mediano, pastel_grande, estado) VALUES
('Juan Pérez', 2, 1, 0, 'recepcionado'),
('María Gómez', 1, 0, 1, 'despachado'),
('Carlos López', 3, 2, 1, 'recepcionado'),
('Ana Rodríguez', 0, 1, 0, 'despachado');

-- Verificar la tabla
SELECT * FROM pedidos;