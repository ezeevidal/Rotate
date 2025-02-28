<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.html');
    exit;
}

// Conectar a la base de datos
require_once '../database/database.php';

// Consulta para obtener todos los profesionales
$sql = "SELECT * FROM profesionales";
$result = $conn->query($sql);

if ($result === false) {
    // Si la consulta falla, devolver un mensaje de error
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

<?php include '../template/header.php'; ?>
<?php include '../template/menu.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Profesionales</title>
    <link rel="stylesheet" href="css/profesionales.css">
</head>
<body>
    <div class="contenedor">
        <h1>Lista de Profesionales</h1>
        <ul id="profesionales-lista">
            <!-- Lista de profesionales cargada dinámicamente -->
        </ul>
    </div>

    <!-- Modal de detalles del profesional -->
<!-- Modal de detalles del profesional -->
<div id="modal" style="display:none;">
    <div id="modalContent">
        <!-- Botón de cierre en forma de cruz -->
        <span id="closeModal" class="close-btn">×</span>
        <div id="modalDetails"></div>
    </div>
</div>


    <script>
    // Función para obtener la lista de profesionales
    function cargarProfesionales() {
        fetch('cargar_profesionales.php')
            .then(response => response.json())
            .then(data => {
                const listaProfesionales = document.getElementById('profesionales-lista');
                if (data.success) {
                    data.data.forEach(profesional => {
                        const li = document.createElement('li');
                        li.innerHTML = `${profesional.nombre} <a class="info" href="javascript:void(0)" onclick="mostrarDetalles(${profesional.id})">Información</a>`;
                        listaProfesionales.appendChild(li);
                    });
                } else {
                    alert('No se encontraron profesionales.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los profesionales.');
            });
    }

    // Función para mostrar detalles del profesional en el modal
    function mostrarDetalles(idProfesional) {
        console.log("ID del profesional que se está pasando: ", idProfesional); // Depuración

        // Verificamos que la URL de la petición esté bien formada
        const url = `obtener_profesionales.php?id=${idProfesional}`;
        console.log("URL de la solicitud:", url);  // Depuración
        
        // Hacemos la solicitud fetch para obtener los detalles del profesional
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                console.log("Datos recibidos: ", data); // Verifica si la respuesta es correcta
                if (data.success) {
                    const modalDetails = document.getElementById('modalDetails');
                    modalDetails.innerHTML = `
                    <p><strong></strong> <img src="${data.data.foto}" alt="Foto del profesional" style="width:150px; height:auto;"/></p>
                        <p><strong>Nombre:</strong> ${data.data.nombre}</p>
                        <p><strong>Fecha de Nacimiento:</strong> ${data.data.fecha_nacimiento}</p>
                        <p><strong>DNI:</strong> ${data.data.dni}</p>
                        <p><strong>Teléfono:</strong> ${data.data.telefono}</p>
                        <p><strong>Email:</strong> ${data.data.email}</p>
                        <p><strong>Profesión:</strong> ${data.data.profesion}</p>
                        <p><strong>Matrícula:</strong> ${data.data.matricula}</p>
                        <p><strong>Información Adicional:</strong> ${data.data.informacion_adicional}</p>
                        
                    `;
                    const modal = document.getElementById('modal');
                    modal.style.display = 'block';
                } else {
                    alert("Error al cargar la información del profesional");
                }
            })
            .catch(error => {
                console.error("Error al obtener la información:", error);
                alert('Error al obtener la información del profesional.');
            });
    }

    // Cerrar el modal
    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('modal').style.display = 'none';
    });
    

    // Cargar la lista de profesionales al cargar la página
    window.onload = cargarProfesionales;
</script>

</body>
</html>
