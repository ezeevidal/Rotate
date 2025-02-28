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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_profesional'])) {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $profesion = $_POST['profesion'];
    $matricula = $_POST['matricula'];
    $informacion_adicional = $_POST['informacion_adicional'];

    // Validar los datos
    if (!empty($_POST['nombre']) && !empty($_POST['fecha_nacimiento']) && !empty($_POST['dni']) && !empty($_POST['telefono']) && !empty($_POST['email']) && !empty($_POST['profesion']) && !empty($_POST['matricula'])) {
        // Procesar la imagen subida
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = $_FILES['foto_perfil']['name'];
            $nombre_temporal = $_FILES['foto_perfil']['tmp_name'];
            $ruta_destino = '../assets/uploads/' . uniqid() . '_' . $nombre_archivo;

            if (move_uploaded_file($nombre_temporal, $ruta_destino)) {
                $foto = $ruta_destino;
            } else {
                echo "<script>mostrarModal('Error al subir la imagen.');</script>";
                return;
            }
        } else {
            $foto = '';
        }

        // Preparar y ejecutar la consulta SQL para insertar los datos del profesional
        $sql_profesional = "INSERT INTO profesionales (nombre, fecha_nacimiento, dni, telefono, email, profesion, matricula, foto, informacion_adicional) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql_profesional)) {
            $fecha_nacimiento = date('Y-m-d', strtotime($fecha_nacimiento));
            $stmt->bind_param("sssssssss", $nombre, $fecha_nacimiento, $dni, $telefono, $email, $profesion, $matricula, $foto, $informacion_adicional);

            if ($stmt->execute()) {
                $profesional_id = $stmt->insert_id;

                echo "<script>mostrarModal('Profesional registrado exitosamente.');</script>";
                echo "<script>document.getElementById('profesionalId').value = '" . $profesional_id . "';</script>";
            } else {
                echo "<script>mostrarModal('Error al registrar el profesional: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>mostrarModal('Error al preparar la consulta: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>mostrarModal('Por favor, complete todos los campos.');</script>";
    }
}
?>
<?php include '../template/header.php'; ?>
<?php include '../template/menu.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Profesional</title>
    <link rel="stylesheet" href="css/profnuevo.css">
</head>
<body>
    <div class="contenedor">
        <div class="container">
            <h1>Agregar Nuevo Profesional</h1>
            <form method="POST" enctype="multipart/form-data">
                <div class="left-side">
                    <label for="nombre">Nombre y Apellido:</label>
                    <input type="text" id="nombre" name="nombre" required>

                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" required>

                    <label for="telefono">Número de Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="right-side">
                    <label for="profesion">Profesión:</label>
                    <select id="profesion" name="profesion" required>
                        <option value="medico">Médico</option>
                        <option value="bioquimico">Bioquímico</option>
                    </select>

                    <label for="matricula">Matrícula:</label>
                    <input type="text" id="matricula" name="matricula" required>

                    <label for="foto_perfil">Foto de Perfil:</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">

                    <label for="informacion_adicional">Información Adicional:</label>
                    <textarea id="informacion_adicional" name="informacion_adicional"></textarea>

                    <button type="submit" name="guardar_profesional">Guardar Profesional</button>
                </div>
            </form>
        </div>

    <input type="hidden" id="profesionalId" value="">

    <script>
        let recordatorios = [];

        function guardarRecordatorio() {
            const textoRecordatorio = document.getElementById("noDisponibilidadTexto").value;
            const fechaDesde = document.getElementById("fechaDesde").value;
            const horaDesde = document.getElementById("horaDesde").value;
            const fechaHasta = document.getElementById("fechaHasta").value;
            const horaHasta = document.getElementById("horaHasta").value;

            const recordatorio = {
                texto: textoRecordatorio,
                fecha_desde: fechaDesde + ' ' + horaDesde,
                fecha_hasta: fechaHasta + ' ' + horaHasta
            };

            recordatorios.push(recordatorio);

            document.getElementById("recordatoriosTexto").innerHTML = recordatorios.map(recordatorio => `<p>${recordatorio.texto}</p>`).join('');

            const profesionalId = document.getElementById("profesionalId").value;
            const recordatoriosJSON = JSON.stringify(recordatorios);

            fetch('guardar_recordatorios.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    profesional_id: profesionalId,
                    recordatorios: recordatoriosJSON
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Recordatorios guardados correctamente.');
                } else {
                    alert('Error al guardar recordatorios: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error en la solicitud fetch:", error);
                alert('Error al guardar recordatorios.');
            });
        }

        function generarOpcionesHora(selector) {
            const select = document.getElementById(selector);
            for (let hora = 0; hora < 24; hora++) {
                for (let minutos = 0; minutos < 60; minutos += 30) {
                    const option = document.createElement('option');
                    const horaFormateada = `${String(hora).padStart(2, '0')}:${String(minutos).padStart(2, '0')}`;
                    option.value = horaFormateada;
                    option.textContent = horaFormateada;
                    select.appendChild(option);
                }
            }
        }

        // Inicializa las opciones de hora en los selectores
        generarOpcionesHora("horaDesde");
        generarOpcionesHora("horaHasta");
    </script>
</body>
</html>
<?php include '../template/footer.php'; ?>