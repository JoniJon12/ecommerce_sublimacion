document.addEventListener("DOMContentLoaded", function () {
  const productosContainer = document.getElementById("productos-container");
  const usuario = JSON.parse(localStorage.getItem("usuario"));

  fetch("http://localhost/ecommerce_sublimacion/backend/api/listar_productos.php")
    .then(async (res) => {
      const text = await res.text();
      try {
        const data = JSON.parse(text);

        if (data.status === "success") {
          data.productos.forEach((producto) => {
            const div = document.createElement("div");
            div.className = "producto";
            div.innerHTML = `
              <h4>${producto.nombre}</h4>
              <img src="http://localhost/ecommerce_sublimacion/uploads/${producto.imagen}" width="150" alt="${producto.nombre}">
              <p>Precio: ${producto.precio} €</p>
              <p>Stock: ${producto.stock}</p>
              <button class="btn btn-success mt-2" onclick="agregarAlCarrito(${producto.id_producto})">Agregar al carrito</button>
              <hr>
            `;
            productosContainer.appendChild(div);
          });
        } else {
          productosContainer.innerHTML = "<p>No hay productos disponibles.</p>";
        }
      } catch (e) {
        console.error("❌ Respuesta no es JSON válido:", e);
        console.log("🔍 Respuesta como texto:", text);
        productosContainer.innerHTML = "<p>Error al cargar productos.</p>";
      }
    })
    .catch((err) => {
      console.error("Error de red al obtener productos:", err);
      productosContainer.innerHTML = "<p>Error al cargar productos.</p>";
    });
});

function agregarAlCarrito(id_producto) {
  const usuario = JSON.parse(localStorage.getItem("usuario"));
  if (!usuario) {
    alert("Debes iniciar sesión para agregar productos al carrito.");
    window.location.href = "login.html";
    return;
  }

  const cantidad = 1; // Por defecto se agrega 1 unidad

  fetch("http://localhost/ecommerce_sublimacion/backend/api/agregar_al_carrito.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id_usuario: usuario.id_usuario,
      id_producto: id_producto,
      cantidad: cantidad,
    }),
  })
    .then(async (res) => {
      const text = await res.text();
      try {
        const data = JSON.parse(text);
        alert(data.message);
      } catch (e) {
        console.error("❌ No es JSON válido al agregar al carrito:", e);
        console.log("🔍 Respuesta como texto:", text);
        alert("Error inesperado al agregar al carrito.");
      }
    })
    .catch((err) => {
      console.error("Error de red al agregar al carrito:", err);
      alert("Hubo un error al agregar al carrito.");
    });
}
