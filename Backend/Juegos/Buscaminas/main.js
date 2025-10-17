const tableroHTML = document.querySelector("#tablero");
const btnIniciar = document.querySelector("#btnIniciar");
const nivelSelect = document.querySelector("#nivel");

let filas, columnas, minas;
let tablero = [];
let juegoTerminado = false; // ✅ Paso 1: control del estado del juego

btnIniciar.addEventListener("click", iniciarJuego);

function iniciarJuego() {
  const nivel = nivelSelect.value;
  if (nivel === "facil") { filas = 7; columnas = 7; minas = 8; }
  else if (nivel === "medio") { filas = 10; columnas = 10; minas = 15; }
  else { filas = 14; columnas = 14; minas = 30; }

  tablero = [];
  tableroHTML.innerHTML = "";
  juegoTerminado = false; // Reinicia estado

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

      // clic izquierdo
      celda.elementHTML.addEventListener("click", () => {
        if (juegoTerminado) return; // ✅ Paso 2: bloquear clics si juego terminado
        if (!celda.revelada && !celda.bandera) {
          if (celda.mina) {
            mostrarTodasLasMinas();
            alert("¡Has perdido!");
            juegoTerminado = true; // ✅ Paso 3: bloquear el juego al perder
          } else {
            revelarCelda(f, c);
            comprobarVictoria();
          }
        }
      });

      // clic derecho para bandera
      celda.elementHTML.addEventListener("contextmenu", (e) => {
        e.preventDefault();
        if (juegoTerminado) return; // bloquear también clic derecho
        if (!celda.revelada) {
          celda.bandera = !celda.bandera;
          celda.elementHTML.classList.toggle("bandera");
        }
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

  // expansión si es 0
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
        celda.elementHTML.textContent = "🏴";
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
    alert("¡Has ganado!");
    juegoTerminado = true; // ✅ Paso 3: bloquear juego al ganar
  }
}

