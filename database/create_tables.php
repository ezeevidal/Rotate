<?php
// Incluir el archivo de conexión a la base de datos
include 'database.php';

// Crear tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'usuarios' creada exitosamente";
} else {
    echo "Error creando la tabla 'usuarios': " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
