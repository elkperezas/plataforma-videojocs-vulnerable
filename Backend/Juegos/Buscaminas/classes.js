class Celda {
  constructor(fila, columna) {
    this.fila = fila;
    this.columna = columna;
    this.mina = false;
    this.numero = 0;
    this.revelada = false;
    this.bandera = false;

    this.elementHTML = document.createElement("div");
    this.elementHTML.classList.add("celda");
  }

  revelar() {
    if (this.revelada) return;
    this.revelada = true;
    this.elementHTML.classList.add("revelada");

    if (this.mina) {
      this.elementHTML.textContent = "💣";
    } else if (this.numero > 0) {
      this.elementHTML.textContent = this.numero;
      this.elementHTML.classList.add("numero");
    } else {
      this.elementHTML.textContent = "";
      this.elementHTML.classList.add("vacia"); // 🔹 Fondo claro para vacías
    }
  }
}
