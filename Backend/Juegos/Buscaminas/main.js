const tableroHTML = document.querySelector("#tablero");
const btnIniciar = document.querySelector("#btnIniciar");
const nivelSelect = document.querySelector("#nivel");
const marcador = document.querySelector("#marcador");

let filas, columnas, minas;
let tablero = [];
let juegoTerminado = false;
let puntuacion = 0;

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

      celda.elementHTML.addEventListener("click", () => {
        if (juegoTerminado || celda.revelada || celda.bandera) return;

        celda.revelar();

        // Puntos solo suman si no es mina
        if (!celda.mina) {
          puntuacion += celda.numero === 0 ? 5 : 10;
          actualizarMarcador();
        } else {
          mostrarTodasLasMinas();
          alert("💣 ¡Has perdido!");
          juegoTerminado = true;
        }

        if (!juegoTerminado) comprobarVictoria();
      });

      celda.elementHTML.addEventListener("contextmenu", (e) => {
        e.preventDefault();
        if (juegoTerminado || celda.revelada) return;

        celda.bandera = !celda.bandera;
        celda.elementHTML.classList.toggle("bandera");

        // Puntos por bandera correcta
        if (celda.bandera && celda.mina) puntuacion += 20;
        if (!celda.bandera && celda.mina) puntuacion -= 0; // nunca negativo
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
    puntuacion += 200; // bonus por ganar
    actualizarMarcador();
    alert("🎉 ¡Has ganado!");
    juegoTerminado = true;
  }
}

function actualizarMarcador() {
  marcador.textContent = `Puntuación: ${puntuacion}`;
}
