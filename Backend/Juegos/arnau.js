// --- CÓDIGO DENTRO DE TU ARCHIVO JAVASCRIPT DEL JUEGO ---

function partidaTerminada(puntuacionFinal) {
    // 1. Los datos que vamos a enviar a PHP
    const datos = {
        puntuacion: puntuacionFinal,
        juego: 'buscaminas' // O 'naves'
    };

    // 2. La ruta a tu script PHP (Ajusta la ruta si es necesario)
    // Desde el juego (en una subcarpeta de Juegos/), la ruta al script PHP (en Backend/)
    // debe ser: "../../Backend/guardar_puntuacion.php"
    const url = '../../Backend/guardar_puntuacion.php';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Puntuación enviada correctamente:", data.score);
            alert(`¡Partida terminada! Puntuación guardada: ${data.score}`);
            // Opcional: Redirigir al usuario de vuelta a la plataforma
            // window.location.href = '../../Backend/plataforma.php'; 
        } else {
            console.error("Error al guardar puntuación:", data.message);
            alert("Error al guardar puntuación: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error de red o servidor:', error);
        alert("Hubo un problema al conectar con el servidor.");
    });
}

// Ejemplo de uso (Llamar esta función cuando el juego termine)
// partidaTerminada(1250);