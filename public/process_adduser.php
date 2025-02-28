<?php
// Incluir el archivo de conexión a la base de datos
include '../database/database.php';

session_start();

// Datos del formulario
$usuario = $_POST['username'];
$contraseña_plana = $_POST['password'];

// Cifrar la contraseña
$contraseña_cifrada = password_hash($contraseña_plana, PASSWORD_DEFAULT);

// Consulta SQL para insertar el usuario
$sql = "INSERT INTO usuarios (usuario, contraseña)
VALUES ('$usuario', '$contraseña_cifrada')";

if ($conn->query($sql) === TRUE) {
    $response = array('status' => 'success', 'message' => 'Nuevo usuario agregado exitosamente');
} else {
    $response = array('status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . $conn->error);
}

// Cerrar la conexión
$conn->close();

// Cerrar la sesión del administrador
session_destroy();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
