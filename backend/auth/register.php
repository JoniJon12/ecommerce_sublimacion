<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Habilitar CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Incluir la conexión a la base de datos
require_once __DIR__ . '/../../config.php';

// Verificar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validar que no estén vacíos
    if (empty($nombre) || empty($correo) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
        exit;
    }
	// Comprobar si el correo ya existe
	$checkEmail = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
	$checkEmail->bind_param("s", $correo);
	$checkEmail->execute();
	$checkEmail->store_result();

	if ($checkEmail->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "El correo ya está registrado"]);
    exit;
	}


    // Encriptar la contraseña antes de guardarla
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Preparar la consulta para insertar el usuario
    $sql = "INSERT INTO usuario (nombre, correo, contraseña, rol, fecha_registro) VALUES (?, ?, ?, 'cliente', NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("sss", $nombre, $correo, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registro exitoso"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>
