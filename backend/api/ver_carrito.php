<?php
require_once '../../config.php';
header('Content-Type: application/json');

$id_usuario = $_GET['id_usuario'] ?? null;

if (!$id_usuario) {
    echo json_encode(["status" => "error", "message" => "Falta el id_usuario"]);
    exit;
}

// 1. Buscar el carrito activo del usuario
$sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "success", "carrito" => []]); // Carrito vacío
    exit;
}

$id_carrito = $result->fetch_assoc()['id_carrito'];

// 2. Obtener productos del carrito con info real
$sql = "SELECT 
            p.id_producto,
            p.nombre,
            p.precio,
            p.imagen,
            cp.cantidad
        FROM carrito_producto cp
        INNER JOIN producto p ON cp.id_producto = p.id_producto
        WHERE cp.id_carrito = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_carrito);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode([
    "status" => "success",
    "carrito" => $productos
]);
?>

