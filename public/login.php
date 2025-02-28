<?php
session_start();
include '../database/database.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE usuario='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['contraseña'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
    } else {
        echo 'Contraseña incorrecta';
    }
} else {
    echo 'Usuario no encontrado';
}

$conn->close();
?>
