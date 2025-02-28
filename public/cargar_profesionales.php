<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado.']);
    exit;
}

// Conectar a la base de datos
require_once '../database/database.php';

// Consulta para obtener todos los profesionales
$sql = "SELECT id, nombre FROM profesionales";
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $conn->error]);
    exit;
}

$profesionales = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $profesionales[] = $row;
    }

    // Devolver la lista de profesionales en formato JSON
    echo json_encode(['success' => true, 'data' => $profesionales]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron profesionales.']);
}
?>
