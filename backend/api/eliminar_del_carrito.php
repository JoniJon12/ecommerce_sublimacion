<?php
require_once '../../config.php';
header('Content-Type: application/json');

// Validar método
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit;
}

// Leer el cuerpo JSON
$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = $data["id_usuario"] ?? null;
$id_producto = $data["id_producto"] ?? null;

if (!$id_usuario || !$id_producto) {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;
}

// Buscar el carrito activo del usuario
$sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Carrito no encontrado"]);
    exit;
}

$id_carrito = $result->fetch_assoc()['id_carrito'];

// Eliminar producto del carrito
$sqlDelete = "DELETE FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("ii", $id_carrito, $id_producto);

if ($stmtDelete->execute()) {
    echo json_encode(["status" => "success", "message" => "Producto eliminado del carrito"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al eliminar el producto"]);
}
