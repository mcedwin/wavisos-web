// // Obtener el input y las cajas
// const searchInput = document.getElementById('searchInput');
// const boxes = document.querySelectorAll('.box');

// // Escuchar el evento de escritura en el input
// searchInput.addEventListener('input', function() {
//   const searchTerm = searchInput.value.toLowerCase(); // Convertir a minúsculas para hacer la búsqueda insensible a mayúsculas
//   boxes.forEach(function(box) {
//     const boxText = box.textContent.toLowerCase(); // Obtener el texto de cada caja
//     if (boxText.includes(searchTerm)) {
//       box.style.display = 'block'; // Mostrar la caja si el texto coincide
//     } else {
//       box.style.display = 'none'; // Ocultar la caja si no coincide
//     }
//   });
// });

$(document).ready(function() {
  // Actualizar título
  $('#titulo').on('input', function() {
      let titleValue = $(this).val();
      $('.preview-title').text(titleValue || 'Título aquí');
  });

  // Actualizar descripción
  $('#contenido').on('input', function() {
      let descriptionValue = $(this).val();
      $('.preview-description').text(descriptionValue || 'Descripción aquí');
  });
  // Actualizar descripción
  $('#numero').on('input', function() {
      let descriptionValue = $(this).val();
      $('.preview-phone').text(descriptionValue || 'Teléfono de contacto aquí');
  });
});



$("form.publicar").submit(function () {

  $(this).mysave((data) => {
    window.location.href = data.redirect;
  });
  return false;
});
