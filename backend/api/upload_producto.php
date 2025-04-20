<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

// Conexión a la base de datos
require_once __DIR__ . '/../../config.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != 0) {
    echo json_encode(["status" => "error", "message" => "Error al subir la imagen"]);
    exit;
}

// Validar campos
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : null;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : null;

if (empty($nombre) || is_null($precio) || is_null($stock)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
    exit;
}

// Guardar imagen
$nombreArchivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
$rutaDestino = __DIR__ . '/../../uploads/' . $nombreArchivo;

// Guardar físicamente la imagen
if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
    echo json_encode(["status" => "error", "message" => "No se pudo guardar la imagen"]);
    exit;
}

// 🚨 GUARDAMOS SOLO EL NOMBRE DE ARCHIVO, NO 'uploads/' 🚨
$sql = "INSERT INTO producto (nombre, precio, stock, imagen, fecha_creacion) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Error preparando la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("sdis", $nombre, $precio, $stock, $nombreArchivo);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Producto creado con éxito",
        "producto" => [
            "id" => $stmt->insert_id,
            "nombre" => $nombre,
            "precio" => $precio,
            "stock" => $stock,
            "imagen" => $nombreArchivo
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al insertar el producto: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
