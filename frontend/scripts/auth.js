document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("login-form");
  const registerForm = document.getElementById("register-form");
  const logoutBtn = document.getElementById("logout-btn");

  // LOGIN
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(loginForm);

      fetch("http://localhost/ecommerce_sublimacion/backend/auth/login.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            localStorage.setItem("usuario", JSON.stringify(data.usuario));
            alert("Login exitoso");
            window.location.href = "index.html";
          } else {
            alert(data.message || "Error al iniciar sesión");
          }
        })
        .catch((err) => {
          console.error("Error:", err);
          alert("Error al conectar con el servidor.");
        });
    });
  }

  // REGISTRO
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(registerForm);

      fetch("http://localhost/ecommerce_sublimacion/backend/auth/register.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            alert("Registro exitoso");
            window.location.href = "login.html";
          } else {
            alert(data.message || "Error al registrar");
          }
        })
        .catch((err) => {
          console.error("Error:", err);
          alert("Error al registrar.");
        });
    });
  }

  // LOGOUT
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function () {
      localStorage.removeItem("usuario");
      alert("Sesión cerrada.");
      window.location.href = "login.html";
    });
  }
});

// FUNCIONES REUTILIZABLES

function obtenerUsuario() {
  const usuario = localStorage.getItem("usuario");
  return usuario ? JSON.parse(usuario) : null;
}

function protegerRuta() {
  const usuario = obtenerUsuario();
  if (!usuario) {
    alert("Debes iniciar sesión.");
    window.location.href = "login.html";
  }
}

function mostrarUsuarioNavbar() {
  const usuario = obtenerUsuario();
  const contenedor = document.getElementById("usuario-nombre");
  if (usuario && contenedor) {
    contenedor.textContent = usuario.nombre;
  }
}
