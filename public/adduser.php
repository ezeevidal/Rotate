<?php
session_start();
include 'config.php';

$error_message = '';

// Verificar si la contraseña de administrador ha sido enviada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password'])) {
    $admin_password = $_POST['admin_password'];
    
    // Verificar si la contraseña es correcta
    if ($admin_password === ADMIN_PASSWORD) {
        $_SESSION['admin_loggedin'] = true;
    } else {
        $error_message = 'Contraseña de administrador incorrecta';
    }
}

// Si el administrador no ha iniciado sesión, mostrar el formulario de contraseña
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Contraseña de Administrador</title>
        <link rel="stylesheet" href="css/adduser.css">
    </head>
    <body>
        <div class="login-container">
            <h1>Contraseña de Administrador</h1>
            <form method="POST">
                <label for="admin_password">Contraseña:</label>
                <input type="password" id="admin_password" name="admin_password" required>
                <button type="submit">Enviar</button>
            </form>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <br>
            <button onclick="window.location.href='index.html'">Volver al Inicio</button>
        </div>
    </body>
    </html>
    <?php
    exit; // Terminar el script aquí para no mostrar el formulario de agregar usuario si no está validada la contraseña
}

// Si la contraseña es correcta, permitir el acceso al formulario de agregar usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rotate | Agregar usuario</title>
    <link rel="shortcut icon" href="../assets/Recurso 1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/adduser.css">
</head>
<body>
    <div class="login-container">
        <h1>Agregar Usuario</h1>
        <form id="addUserForm" action="process_adduser.php" method="POST">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Agregar Usuario</button>
        </form>
        <br>
        <button onclick="window.location.href='index.html'">Volver al Inicio</button>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p id="modal-message"></p>
        </div>
    </div>

    <script>
        // Obtener elementos del DOM
        const addUserForm = document.getElementById('addUserForm');
        const modal = document.getElementById('modal');
        const modalMessage = document.getElementById('modal-message');
        const closeButton = document.querySelector('.close-button');

        // Manejar el submit del formulario
        addUserForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(addUserForm);

            fetch('process_adduser.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalMessage.textContent = data.message;
                modal.style.display = 'block';
                if (data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 3000);
                }
            })
            .catch(error => {
                modalMessage.textContent = 'Error al agregar el usuario';
                modal.style.display = 'block';
            });
        });

        // Cerrar el modal
        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Cerrar el modal al hacer clic fuera del modal
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
