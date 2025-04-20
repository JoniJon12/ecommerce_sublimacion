// scripts/ver_pedidos.js

document.addEventListener("DOMContentLoaded", () => {
  const usuario = obtenerUsuario(); // función de auth.js
  protegerRuta(); // redirige si no hay sesión
  mostrarUsuarioNavbar(); // opcional: muestra nombre en el navbar

  const id_usuario = usuario.id_usuario;
  const pedidosContainer = document.getElementById("pedidos-container");

  fetch(`http://localhost/ecommerce_sublimacion/backend/api/ver_pedidos_completos.php?id_usuario=${id_usuario}`)
    .then(res => res.json())
    .then(data => {
      if (data.status === "success" && data.pedidos.length > 0) {
        pedidosContainer.innerHTML = "";

        data.pedidos.forEach(pedido => {
          const pedidoDiv = document.createElement("div");
          pedidoDiv.classList.add("pedido");

          pedidoDiv.innerHTML = `
            <h3>Pedido #${pedido.id_pedido}</h3>
            <p><strong>Fecha:</strong> ${pedido.fecha_pedido}</p>
            <p><strong>Estado:</strong> ${pedido.estado}</p>
            <p><strong>Total:</strong> ${pedido.total} €</p>
            <div class="productos">
              ${pedido.productos.map(producto => `
                <div class="producto">
                  <img src="../backend/uploads/productos/${producto.imagen}" alt="${producto.nombre}" />
                  <p><strong>${producto.nombre}</strong></p>
                  <p>Cantidad: ${producto.cantidad}</p>
                  <p>Precio: ${producto.precio_unitario} €</p>
                </div>
              `).join("")}
            </div>
          `;

          pedidosContainer.appendChild(pedidoDiv);
        });
      } else {
        pedidosContainer.innerHTML = "<p>No tienes pedidos.</p>";
      }
    })
    .catch(err => {
      console.error(err);
      pedidosContainer.innerHTML = "<p>Error al cargar los pedidos.</p>";
    });
});
