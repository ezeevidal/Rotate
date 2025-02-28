<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.html');
    exit;
}
?>

<?php include '../template/header.php'; ?>
<?php include '../template/menu.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\dashborad.css">
</head>
<body> 
    <div class="cuerpo">

        <!-- Contenedor para el calendario -->
        <div id="calendar-container">
            <div id="calendar"></div>
        </div>  
        <div class="botonera">
            <button id="openModal"> Automatizar todas las guardias</button>
            <button>Agregar guardia manualmente</button>
            <button>Modificar guardia</button>
            <button>Eliminar guardia</button>
            <button>Agregar condiciones</button>
        </div>  
    </div>
</div>

<div id="guardiasModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Asignar Guardias</h2>
    <form id="formGuardias">
      <label for="profesional">Seleccionar Profesional:</label>
      <select id="profesional" name="profesional">
        <option value="">--Seleccionar--</option>
        <!-- Los profesionales se cargarán aquí mediante JavaScript -->
      </select><br><br>

      <label for="fechas_no_disponibles">Seleccionar Fechas de No Disponibilidad:</label>
      <input type="date" id="fecha_no_disponible" name="fecha_no_disponible"><br><br>

      <label for="hora_inicio">Hora de Inicio de No Disponibilidad:</label>
      <input type="time" id="hora_inicio" name="hora_inicio"><br><br>

      <label for="hora_fin">Hora de Fin de No Disponibilidad:</label>
      <input type="time" id="hora_fin" name="hora_fin"><br><br>

      <button type="button" id="agregarFechaNoDisponible">Agregar Fecha No Disponible</button><br><br>

      <h3>Fechas de No Disponibilidad Seleccionadas:</h3>
      <ul id="fechasList"></ul><br><br>

      <label for="horas_mensuales">Horas Mensuales:</label>
      <input type="number" id="horas_mensuales" name="horas_mensuales" required><br><br>

      <label for="duracion_guardia">Duración de Guardia (en horas):</label>
      <input type="number" id="duracion_guardia" name="duracion_guardia" required><br><br>

      <label for="horario_ingreso">Horario de Ingreso:</label>
      <input type="time" id="horario_ingreso" name="horario_ingreso" required><br><br>

      <label for="horario_egreso">Horario de Egreso:</label>
      <input type="time" id="horario_egreso" name="horario_egreso" required><br><br>

      <button type="button" id="cargarGuardias">Cargar Guardias</button>
    </form>
  </div>
</div>


    <!-- Scripts de FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
// Mostrar modal
document.getElementById("openModal").addEventListener("click", function() {
    document.getElementById("guardiasModal").style.display = "block";
    cargarProfesionales(); // Cargar los profesionales cuando se abre el modal
});

// Cerrar modal
document.querySelector(".close").addEventListener("click", function() {
    document.getElementById("guardiasModal").style.display = "none";
});

// Agregar fecha de no disponibilidad
let fechasNoDisponibles = [];

document.getElementById("agregarFechaNoDisponible").addEventListener("click", function() {
    const fechaSeleccionada = document.getElementById("fecha_no_disponible").value;
    const horaInicio = document.getElementById("hora_inicio").value;
    const horaFin = document.getElementById("hora_fin").value;

    if (fechaSeleccionada && horaInicio && horaFin) {
        fechasNoDisponibles.push({
            fecha: fechaSeleccionada,
            horaInicio: horaInicio,
            horaFin: horaFin
        });

        actualizarListaFechas();
        document.getElementById("fecha_no_disponible").value = "";
        document.getElementById("hora_inicio").value = "";
        document.getElementById("hora_fin").value = "";
    } else {
        alert("Por favor, ingresa una fecha y un rango horario.");
    }
});

function actualizarListaFechas() {
    const lista = document.getElementById("fechasList");
    lista.innerHTML = "";
    fechasNoDisponibles.forEach((fecha) => {
        const li = document.createElement("li");
        li.textContent = `${fecha.fecha} - ${fecha.horaInicio} a ${fecha.horaFin}`;
        lista.appendChild(li);
    });
}

document.getElementById("cargarGuardias").addEventListener("click", function() {
    const profesionalSeleccionado = document.getElementById("profesional").value;
    const horasMensuales = document.getElementById("horas_mensuales").value;
    const duracionGuardia = document.getElementById("duracion_guardia").value;
    const horarioIngreso = document.getElementById("horario_ingreso").value;
    const horarioEgreso = document.getElementById("horario_egreso").value;

    if (!profesionalSeleccionado) {
        alert("Por favor, selecciona un profesional.");
        return;
    }

    if (fechasNoDisponibles.length === 0) {
        alert("Por favor, selecciona al menos una fecha de no disponibilidad.");
        return;
    }

    if (!horasMensuales || !duracionGuardia || !horarioIngreso || !horarioEgreso) {
        alert("Por favor, completa todos los campos.");
        return;
    }

    asignarGuardias(profesionalSeleccionado, fechasNoDisponibles, horasMensuales, duracionGuardia, horarioIngreso, horarioEgreso);
});

function asignarGuardias(profesionalId, fechasNoDisponibles, horasMensuales, duracionGuardia, horarioIngreso, horarioEgreso) {
    let fechaInicio = new Date();
    let fechaFin = new Date();
    fechaFin.setMonth(fechaInicio.getMonth() + 1);

    let guardiasAsignadas = [];

    for (let fecha = new Date(fechaInicio); fecha <= fechaFin; fecha.setDate(fecha.getDate() + 1)) {
        let fechaString = fecha.toISOString().split("T")[0];

        const noDisponible = fechasNoDisponibles.find((f) => f.fecha === fechaString);
        if (noDisponible) {
            continue;
        }

        let horaActual = new Date(fecha.setHours(horarioIngreso.split(":")[0], horarioIngreso.split(":")[1]));
        let horaFinal = new Date(fecha.setHours(horarioEgreso.split(":")[0], horarioEgreso.split(":")[1]));

        while (horaActual < horaFinal) {
            let guardia = {
                profesionalId: profesionalId,
                fecha: fechaString,
                horaInicio: horaActual.toISOString().split("T")[1].split(".")[0],
                horaFin: new Date(horaActual.getTime() + duracionGuardia * 60 * 60 * 1000).toISOString().split("T")[1].split(".")[0]
            };
            guardiasAsignadas.push(guardia);
            horaActual.setMinutes(horaActual.getMinutes() + 30);
        }
    }

    if (guardiasAsignadas.length > 0) {
        console.log("Guardias asignadas:", guardiasAsignadas);
        alert("Guardias asignadas con éxito.");
    } else {
        alert("No se pudieron asignar guardias debido a la no disponibilidad.");
    }
}

function cargarProfesionales() {
    fetch('cargar_profesionales.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById("profesional");
                data.data.forEach(profesional => {
                    const option = document.createElement("option");
                    option.value = profesional.id;
                    option.textContent = profesional.nombre;
                    select.appendChild(option);
                });
            } else {
                alert("No se pudieron cargar los profesionales.");
            }
        })
        .catch(error => {
            console.error("Error al cargar los profesionales:", error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) { 
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            events: [
                { title: 'Guardia Matutina', start: '2025-02-28' },
                { title: 'Reunión de equipo', start: '2025-03-02' }
            ],
            dateClick: function(info) {
                alert('Fecha seleccionada: ' + info.dateStr);
            }
        });
        calendar.render();
    } else {
        console.error("No se encontró el contenedor del calendario.");
    }
});
</script>

    </script>

    <?php include '../template/footer.php'; ?>

</body>
</html>
