document.addEventListener("DOMContentLoaded", () => {
  protegerRuta(); // Requiere sesión

  const usuario = obtenerUsuario();
  const contenedor = document.getElementById("carrito-container");

  fetch(`http://localhost/ecommerce_sublimacion/backend/api/ver_carrito.php?id_usuario=${usuario.id_usuario}`)
    .then(res => res.json())
    .then(data => {
      if (data.status === "success" && data.carrito.length > 0) {
        data.carrito.forEach(producto => {
          const div = document.createElement("div");
          div.className = "card mb-3";
          div.innerHTML = `
            <div class="row g-0">
              <div class="col-md-2">
                <img src="http://localhost/ecommerce_sublimacion/backend/uploads/${producto.imagen}" class="img-fluid rounded-start" alt="${producto.nombre}">
              </div>
              <div class="col-md-10">
                <div class="card-body">
                  <h5 class="card-title">${producto.nombre}</h5>
                  <p class="card-text">Precio: ${producto.precio} €</p>
                  <p class="card-text">Cantidad: ${producto.cantidad}</p>
                </div>
              </div>
            </div>
          `;
          contenedor.appendChild(div);
        });
      } else {
        contenedor.innerHTML = "<p>No tienes productos en el carrito.</p>";
      }
    })
    .catch(err => {
      console.error("Error al cargar el carrito:", err);
      contenedor.innerHTML = "<p>Error al obtener el carrito.</p>";
    });
});
