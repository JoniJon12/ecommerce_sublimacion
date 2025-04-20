<?php

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../../config.php"; // Aquí ya está session_start()


// Solo responder a POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST["correo"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($correo) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Correo y contraseña requeridos"
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id_usuario, nombre, contraseña FROM usuario WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        if ($usuario && password_verify($password, $usuario["contraseña"])) {
            $_SESSION["usuario_id"] = $usuario["id_usuario"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];

            echo json_encode([
                "status" => "success",
                "message" => "Inicio de sesión exitoso",
                "usuario" => [
                    "id_usuario" => $usuario["id_usuario"],
                    "nombre" => $usuario["nombre"]
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Correo o contraseña incorrectos"
            ]);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Error del servidor: " . $e->getMessage()
        ]);
    }
}
?>
