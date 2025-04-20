document.addEventListener("DOMContentLoaded", function () {
  protegerRuta();
  mostrarUsuarioNavbar();

  const params = new URLSearchParams(window.location.search);
  const id_pedido = params.get("id");

  const container = document.getElementById("detalle-container");

  if (!id_pedido) {
    container.innerHTML = "<p>Pedido no especificado.</p>";
    return;
  }

  fetch(`http://localhost/ecommerce_sublimacion/backend/api/ver_productos_pedido.php?id_pedido=${id_pedido}`)
    .then(res => res.json())
    .then(data => {
      if (data.status === "success" && data.data.length > 0) {
        let total = 0;
        const productosHTML = data.data.map(p => {
          const subtotal = p.precio_unitario * p.cantidad;
          total += subtotal;

          return `
            <div class="col-md-4">
              <div class="card h-100 shadow-sm">
                <img src="../${p.imagen}" class="card-img-top" alt="${p.nombre}">
                <div class="card-body">
                  <h5 class="card-title">${p.nombre}</h5>
                  <p class="card-text">${p.precio_unitario} € x ${p.cantidad}</p>
                  <p class="card-text fw-bold">Subtotal: ${subtotal.toFixed(2)} €</p>
                </div>
              </div>
            </div>
          `;
        }).join("");

        container.innerHTML = `
          <h4 class="mb-4">Pedido #${id_pedido}</h4>
          <div class="row g-4">
            ${productosHTML}
          </div>
          <div class="mt-4">
            <h5 class="fw-bold">Total estimado: ${total.toFixed(2)} €</h5>
            <a href="ver_pedidos.html" class="btn btn-outline-primary mt-2">Volver a pedidos</a>
          </div>
        `;
      } else {
        container.innerHTML = "<p>No se encontraron productos en este pedido.</p>";
      }
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = "<p>Error al cargar el pedido.</p>";
    });
});
