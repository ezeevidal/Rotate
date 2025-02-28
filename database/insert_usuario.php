<?php
// Incluir el archivo de conexión a la base de datos
include 'database.php';

// Datos del usuario
$usuario = "juli";
$contraseña_plana = "123";

// Cifrar la contraseña
$contraseña_cifrada = password_hash($contraseña_plana, PASSWORD_DEFAULT);

// Consulta SQL para insertar el usuario
$sql = "INSERT INTO usuarios (usuario, contraseña)
VALUES ('$usuario', '$contraseña_cifrada')";

if ($conn->query($sql) === TRUE) {
    echo "Nuevo usuario agregado exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
