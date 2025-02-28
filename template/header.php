<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rotate | Escritorio</title>
    <link rel="shortcut icon" href="../assets/Recurso 1.png" type="image/x-icon">
    <link rel="stylesheet" href="../public/css/header.css">
</head>
<body>
<header>
    <div style="display: flex; justify-content: center;">
        <img src="../assets/Recurso 1.png" alt="Logo" id="logo">
        <img src="../assets/Recurso 2.png" alt="Logo 2" id="logo2">
    </div>
</header>

    <script>
        // JavaScript para mostrar/ocultar el menú desplegable
        const menuBtn = document.getElementById('menuBtn');
        const menuDropdown = document.getElementById('menuDropdown');

        menuBtn.addEventListener('click', function() {
            // Alterna la clase activa para mostrar u ocultar el menú
            menuBtn.classList.toggle('active');
        });

        // Cerrar el menú al hacer clic fuera de él
        window.addEventListener('click', function(event) {
            if (!menuBtn.contains(event.target) && !menuDropdown.contains(event.target)) {
                menuBtn.classList.remove('active');
            }
        });
    </script>
</body>
</html>
