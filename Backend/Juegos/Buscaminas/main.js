const tableroHTML = document.querySelector("#tablero");
const btnIniciar = document.querySelector("#btnIniciar");
const nivelSelect = document.querySelector("#nivel");
const marcador = document.querySelector("#marcador");

const datos = {
  usuari_id, // ❌ No necesario si usas sesión, y mal obtenido
  joc_id, // ID del juego
  nivell_jugat: nivelSelect.value, // No se usa en el PHP
  puntuacio_obtinguda: puntuacionFinal // Nombre incorrecto
};


let filas, columnas, minas;
let tablero = [];
let juegoTerminado = false;
let puntuacion = 0;
const joc_id = 1; // ID del juego "Buscaminas" en tu tabla jocs

btnIniciar.addEventListener("click", iniciarJuego);

function iniciarJuego() {
  const nivel = nivelSelect.value;
  if (nivel === "facil") { filas = 7; columnas = 7; minas = 8; }
  else if (nivel === "medio") { filas = 10; columnas = 10; minas = 15; }
  else { filas = 14; columnas = 14; minas = 30; }

  tablero = [];
  tableroHTML.innerHTML = "";
  juegoTerminado = false;
  puntuacion = 0;
  actualizarMarcador();

  crearTablero();
  colocarMinas();
  calcularNumeros();
}

function crearTablero() {
  tableroHTML.style.gridTemplateColumns = `repeat(${columnas}, 30px)`;
  tableroHTML.style.gridTemplateRows = `repeat(${filas}, 30px)`;
  tableroHTML.style.display = "grid";

  for (let f = 0; f < filas; f++) {
    tablero[f] = [];
    for (let c = 0; c < columnas; c++) {
      const celda = new Celda(f, c);
      tablero[f][c] = celda;
      tableroHTML.appendChild(celda.elementHTML);

      // 🖱️ Click izquierdo
      celda.elementHTML.addEventListener("click", () => {
        if (juegoTerminado || celda.revelada || celda.bandera) return;

        celda.revelar();

        if (!celda.mina) {
          puntuacion += celda.numero === 0 ? 5 : 10;
          actualizarMarcador();
        } else {
          mostrarTodasLasMinas();
          alert("💣 ¡Has perdido!");
          juegoTerminado = true;
          partidaTerminada(puntuacion); // 💾 Guardar puntuación
        }

        if (!juegoTerminado) comprobarVictoria();
      });

      // 🚩 Click derecho (bandera)
      celda.elementHTML.addEventListener("contextmenu", (e) => {
        e.preventDefault();
        if (juegoTerminado || celda.revelada) return;

        celda.bandera = !celda.bandera;
        celda.elementHTML.classList.toggle("bandera");

        if (celda.bandera && celda.mina) puntuacion += 20;
        actualizarMarcador();
      });
    }
  }
}

function colocarMinas() {
  let colocadas = 0;
  while (colocadas < minas) {
    const f = Math.floor(Math.random() * filas);
    const c = Math.floor(Math.random() * columnas);
    if (!tablero[f][c].mina) {
      tablero[f][c].mina = true;
      colocadas++;
    }
  }
}

function calcularNumeros() {
  for (let f = 0; f < filas; f++) {
    for (let c = 0; c < columnas; c++) {
      if (tablero[f][c].mina) continue;
      let contador = 0;
      for (let df = -1; df <= 1; df++) {
        for (let dc = -1; dc <= 1; dc++) {
          const nf = f + df, nc = c + dc;
          if (nf >= 0 && nf < filas && nc >= 0 && nc < columnas) {
            if (tablero[nf][nc].mina) contador++;
          }
        }
      }
      tablero[f][c].numero = contador;
    }
  }
}

function revelarCelda(f, c) {
  const celda = tablero[f][c];
  if (celda.revelada) return;
  celda.revelar();

  if (!celda.mina) {
    puntuacion += celda.numero === 0 ? 5 : 10;
    actualizarMarcador();
  }

  if (celda.numero === 0 && !celda.mina) {
    for (let df = -1; df <= 1; df++) {
      for (let dc = -1; dc <= 1; dc++) {
        const nf = f + df, nc = c + dc;
        if (nf >= 0 && nf < filas && nc >= 0 && nc < columnas) {
          revelarCelda(nf, nc);
        }
      }
    }
  }
}

function mostrarTodasLasMinas() {
  for (let f = 0; f < filas; f++) {
    for (let c = 0; c < columnas; c++) {
      const celda = tablero[f][c];
      if (celda.mina) {
        celda.elementHTML.classList.add("mina");
        celda.elementHTML.textContent = "💣";
      }
    }
  }
}

function comprobarVictoria() {
  let celdasReveladas = 0;
  for (let f = 0; f < filas; f++) {
    for (let c = 0; c < columnas; c++) {
      if (tablero[f][c].revelada) celdasReveladas++;
    }
  }
  if (celdasReveladas === filas * columnas - minas && !juegoTerminado) {
    puntuacion += 200;
    actualizarMarcador();
    alert("🎉 ¡Has ganado!");
    juegoTerminado = true;
    partidaTerminada(puntuacion); // 💾 Guardar puntuación
  }
}

function actualizarMarcador() {
  marcador.textContent = `Puntuación: ${puntuacion}`;
}
/* 💾 Función para enviar la puntuación al backend */
function partidaTerminada(puntuacionFinal) {
    // El ID del usuario (usuari_id) se obtiene en el backend desde la sesión de PHP, 
    // no debemos enviarlo desde el cliente por seguridad y simplicidad.
    
    // Si necesitas diferenciar los juegos en el ranking, usa 'joc_id'
    const joc = (joc_id === 1) ? 'buscaminas' : 'desconocido';

    const datos = {
        // Renombramos la clave para coincidir con lo que espera PHP
        puntuacion: puntuacionFinal, 
        // Renombramos la clave para coincidir con lo que espera PHP (juego o joc)
        juego: joc 
    };

    fetch("../../Backend/guardar_puntuacion.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(res => {
        // Verificar si la respuesta fue un error (4xx o 5xx) antes de intentar parsear JSON
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.success === true) { // Usamos 'success' que es la clave del PHP
            console.log("✅ Puntuación guardada correctamente:", data.score);
            alert("Puntuación guardada: " + data.score);
        } else {
            // Manejar error devuelto por el script PHP (ej: error DB)
            console.error("❌ Error al guardar puntuación:", data.message);
            alert("Error al guardar puntuación: " + data.message);
        }
    })
    .catch(err => {
        // Manejar errores de red o errores HTTP
        console.error("❌ Error de conexión/servidor:", err);
        alert("Error de conexión con el servidor. Verifica el script PHP.");
    });
}
