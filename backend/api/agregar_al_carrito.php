<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../config.php';

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data['id_usuario'] ?? null;
$id_producto = $data['id_producto'] ?? null;
$cantidad = $data['cantidad'] ?? 1;

if (!$id_usuario || !$id_producto || $cantidad <= 0) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

// Verificar si el usuario ya tiene un carrito activo
$sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $carrito = $result->fetch_assoc();
    $id_carrito = $carrito['id_carrito'];
} else {
    // Crear nuevo carrito
    $sql = "INSERT INTO carrito (id_usuario, fecha_creacion, estado) VALUES (?, NOW(), 'activo')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    if ($stmt->execute()) {
        $id_carrito = $stmt->insert_id;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al crear el carrito"]);
        exit;
    }
}

// Insertar o actualizar producto en carrito
$sql = "INSERT INTO carrito_producto (id_carrito, id_producto, cantidad)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE cantidad = cantidad + ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $id_carrito, $id_producto, $cantidad, $cantidad);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Producto añadido al carrito"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al añadir producto al carrito"]);
}

$stmt->close();
$conn->close();
?>
