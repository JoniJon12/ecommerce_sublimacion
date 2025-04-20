document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-upload-producto");

  const usuario = JSON.parse(localStorage.getItem("usuario"));
  if (!usuario) {
    alert("Debes iniciar sesión para subir productos.");
    window.location.href = "login.html";
    return;
  }

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      if (!form.checkValidity()) {
        e.stopPropagation();
        form.classList.add("was-validated");
        return;
      }

      const formData = new FormData(form);
      formData.append("id_usuario", usuario.id_usuario); // Por si lo usas luego

      fetch("http://localhost/ecommerce_sublimacion/backend/api/upload_producto.php", {
        method: "POST",
        body: formData,
      })
      .then(async res => {
        const text = await res.text();
        console.log("🔍 Respuesta del servidor como texto:");
        console.log(text); // <-- muestra la respuesta real del servidor

        try {
          const data = JSON.parse(text); // intenta convertirlo en JSON
          alert(data.message);
          if (data.status === "success") {
            form.reset();
            form.classList.remove("was-validated");
          }
        } catch (e) {
          console.error("❌ No es JSON válido:", e);
          alert("⚠️ El servidor no respondió con JSON válido. Mira la consola.");
        }
      })
      .catch(err => {
        console.error("⛔ Error en la petición:", err);
        alert("Error al conectar con el servidor.");
      });
    });
  }
});
