// ----- Clase Celda -----
class Celda {
  constructor(fila, col) {
    this.fila = fila;
    this.col = col;
    this.mina = false;
    this.revelada = false;
    this.bandera = false;
    this.numero = 0;

    this.elementHTML = document.createElement("div");
    this.elementHTML.classList.add("celda");
  }

  mostrarNumero() {
    if (this.numero > 0) {
      this.elementHTML.textContent = this.numero;
    }
  }

  revelar() {
    this.revelada = true;
    this.elementHTML.classList.add("revelada");
    this.mostrarNumero();
  }
}
