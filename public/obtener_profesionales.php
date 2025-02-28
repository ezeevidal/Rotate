<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado.']);
    exit;
}

// Verificar si se recibió el id del profesional
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No se ha proporcionado el ID del profesional.']);
    exit;
}

$idProfesional = $_GET['id'];

// Conectar a la base de datos
require_once '../database/database.php';

// Consulta para obtener los detalles del profesional
$sql = "SELECT * FROM profesionales WHERE id = ?";
$stmt = $conn->prepare($sql);

// Verificar si la consulta se preparó correctamente
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $idProfesional);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si la consulta fue exitosa
if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $conn->error]);
    exit;
}

$profesional = $result->fetch_assoc();

if ($profesional) {
    // Devolver la información del profesional
    echo json_encode(['success' => true, 'data' => $profesional]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontró el profesional.']);
}
?>
