const tableroHTML = document.querySelector("#tablero");
const btnIniciar = document.querySelector("#btnIniciar");
const marcador = document.querySelector("#marcador");

let filas, columnas, minas;
let tablero = [];
let juegoTerminado = false;
let puntuacion = 0;
let nivelActual = 1;
let primerClick = true;
const joc_id = 1;

// ---------- Botón iniciar (ahora fuerza reset) ----------
btnIniciar.addEventListener("click", () => iniciarJuego(true));

/**
 * iniciarJuego(resetScore = true)
 * - resetScore = true  => partida nueva (puntuación y nivel a 0/1)
 * - resetScore = false => conservar puntuación y nivelActual (para avanzar nivel)
 */
function iniciarJuego(resetScore = true) {
  if (resetScore) {
    nivelActual = 1;
    puntuacion = 0;
  }
  primerClick = true;
  configurarNivel(nivelActual);

  tablero = [];
  tableroHTML.innerHTML = "";
  juegoTerminado = false;
  actualizarMarcador();

  crearTablero();
  colocarMinas();
  calcularNumeros();
}

function configurarNivel(nivel) {
  switch (nivel) {
    case 1: filas = 5; columnas = 5; minas = 5; break;
    case 2: filas = 7; columnas = 7; minas = 10; break;
    case 3: filas = 10; columnas = 10; minas = 20; break;
    case 4: filas = 12; columnas = 12; minas = 30; break;
    case 5: filas = 15; columnas = 15; minas = 45; break;
    default: filas = 7; columnas = 7; minas = 10;
  }
  console.log(`Iniciando nivel ${nivel} — ${filas}x${columnas}, ${minas} minas`);
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

      celda.elementHTML.addEventListener("click", () => manejarClick(celda, f, c));
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

function manejarClick(celda, f, c) {
  if (juegoTerminado || celda.revelada || celda.bandera) return;

  // Primer clic seguro
  if (primerClick) {
    primerClick = false;
    if (celda.mina) {
      celda.mina = false;
      recolocarMina();
      calcularNumeros();
    }
  }

  celda.revelar();

  if (celda.mina) {
    mostrarTodasLasMinas();
    alert("💣 ¡Has perdido!");
    juegoTerminado = true;
    guardarPuntuacion(puntuacion);
    // Reinicio claro: reiniciamos nivel y puntuación al iniciar nueva partida
    // No llamamos iniciarJuego() aquí; dejamos que el usuario pulse "Iniciar"
    // Si prefieres reiniciar automáticamente, usa:
    // reiniciarJuego();
    return;
  }

  // Revelar áreas vacías (estándar buscaminas)
  if (celda.numero === 0) {
    revelarVacias(f, c);
  }

  // Sumar puntos por la celda clicada y actualizar
  puntuacion += celda.numero === 0 ? 5 : 10;
  actualizarMarcador();

  comprobarVictoria();
}

function recolocarMina() {
  // Recoloca una mina en una celda aleatoria no revelada
  while (true) {
    const f = Math.floor(Math.random() * filas);
    const c = Math.floor(Math.random() * columnas);
    if (!tablero[f][c].mina && !tablero[f][c].revelada) {
      tablero[f][c].mina = true;
      break;
    }
  }
}

function revelarVacias(f, c) {
  const dirs = [
    [-1,-1],[-1,0],[-1,1],
    [0,-1],       [0,1],
    [1,-1], [1,0], [1,1]
  ];
  for (const [df, dc] of dirs) {
    const nf = f + df, nc = c + dc;
    if (nf >= 0 && nf < filas && nc >= 0 && nc < columnas) {
      const cel = tablero[nf][nc];
      if (!cel.revelada && !cel.mina) {
        cel.revelar();
        // sumar puntos por las celdas que se abren automáticamente
        puntuacion += cel.numero === 0 ? 5 : 10;
        if (cel.numero === 0) revelarVacias(nf, nc);
      }
    }
  }
  actualizarMarcador();
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
      let cnt = 0;
      for (let df = -1; df <= 1; df++) {
        for (let dc = -1; dc <= 1; dc++) {
          const nf = f + df, nc = c + dc;
          if (nf >= 0 && nf < filas && nc >= 0 && nc < columnas) {
            if (tablero[nf][nc].mina) cnt++;
          }
        }
      }
      tablero[f][c].numero = cnt;
    }
  }
}

function mostrarTodasLasMinas() {
  for (let f = 0; f < filas; f++) {
    for (let c = 0; c < columnas; c++) {
      if (tablero[f][c].mina) {
        tablero[f][c].elementHTML.classList.add("mina");
        tablero[f][c].elementHTML.textContent = "💣";
      }
    }
  }
}

function comprobarVictoria() {
  let reveladas = 0;
  for (let f = 0; f < filas; f++) {
    for (let c = 0; c < columnas; c++) {
      if (tablero[f][c].revelada) reveladas++;
    }
  }

  if (reveladas === filas * columnas - minas && !juegoTerminado) {
    puntuacion += 200;
    actualizarMarcador();
    guardarPuntuacion(puntuacion);

    juegoTerminado = true;
    if (nivelActual < 5) {
      alert(`🎉 Nivel ${nivelActual} superado — pasas al ${nivelActual + 1}`);
      nivelActual++;
      // Avanza sin resetear la puntuación
      setTimeout(() => iniciarJuego(false), 1200);
    } else {
      alert("🏆 ¡Has completado todos los niveles!");
    }
  }
}

function actualizarMarcador() {
  marcador.textContent = `Nivel: ${nivelActual} | Puntuación: ${puntuacion}`;
}

function reiniciarJuego() {
  // Fuerza reinicio total y arranca automáticamente
  nivelActual = 1;
  puntuacion = 0;
  setTimeout(() => iniciarJuego(true), 800);
}

function guardarPuntuacion(puntos) {
  const usuario_id = localStorage.getItem("usuario_id");
  if (!usuario_id) {
    console.warn("No hay usuario logueado — no se guarda puntuación.");
    return;
  }

  fetch("http://172.20.0.108/Backend/guardar_puntuacion.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      usuario_id,
      joc_id,
      puntuacion: puntos
    })
  })
    .then(res => res.json())
    .then(data => console.log("Guardado:", data))
    .catch(err => console.error("Error guardando puntuación:", err));
}
