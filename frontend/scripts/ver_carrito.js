function eliminarDelCarrito(id_producto) {
  // Obtener el usuario desde localStorage
  const usuario = JSON.parse(localStorage.getItem("usuario"));
  
  // Validar si el usuario está logueado
  if (!usuario || !usuario.id_usuario) {
    alert("Debes iniciar sesión");
    window.location.href = "login.html";
    return;
  }

  // Enviar solicitud al backend para eliminar producto del carrito
  fetch("http://localhost/ecommerce_sublimacion/backend/api/eliminar_del_carrito.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      id_usuario: usuario.id_usuario,
      id_producto: id_producto
    }),
  })
    .then(res => res.json())
    .then(data => {
      console.log(data); // para depuración

      if (data.status === "success") {
        alert("Producto eliminado del carrito.");
        location.reload(); // Recargar para actualizar la lista
      } else {
        alert(data.message || "No se pudo eliminar el producto.");
      }
    })
    .catch(err => {
      console.error("Error al eliminar producto:", err);
      alert("Error en la solicitud al eliminar el producto.");
    });
}
