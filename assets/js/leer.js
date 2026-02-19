if (window.speechSynthesis.speaking) {
  window.speechSynthesis.cancel();
}
$(".leer").click(function () {
  if (window.speechSynthesis.speaking || window.speechSynthesis.pending) {
    window.speechSynthesis.cancel();
  }
  setTimeout(() => {
    // Obtener el texto del div
    const div = document.getElementById("lectura");
    const texto = div.innerText;

    const fragmentos = dividirTexto(texto, 100);
    console.log(fragmentos);
    // Función para leer fragmentos de texto secuencialmente
    let indice = 0;

    function leerFragmento() {
      if (indice < fragmentos.length) {
        const mensaje = new SpeechSynthesisUtterance();
        mensaje.text = fragmentos[indice];
        mensaje.lang = "es-ES";
        mensaje.rate = 1;

        mensaje.onend = () => {
          indice++; // Pasar al siguiente fragmento
          leerFragmento(); // Leer el siguiente fragmento
        };

        mensaje.onerror = (event) => {
          console.error("Error durante la lectura:", event.error);
        };

        window.speechSynthesis.speak(mensaje);
      } else {
        console.log("Lectura completada.");
        // alert("Lectura completada.");
      }
    }

    leerFragmento(); // Iniciar la lectura de los fragmentos
  }, 100); // Breve retraso para evitar problemas de cancelación
  return false;
});

function dividirTexto(texto, limite) {
  const palabras = texto.split(" ");
  const fragmentos = [];
  let fragmentoActual = "";

  for (const palabra of palabras) {
    if ((fragmentoActual + palabra).length <= limite) {
      fragmentoActual += palabra + " ";
    } else {
      fragmentos.push(fragmentoActual.trim());
      fragmentoActual = palabra + " ";
    }
  }

  if (fragmentoActual.length > 0) {
    fragmentos.push(fragmentoActual.trim());
  }

  return fragmentos;
}
