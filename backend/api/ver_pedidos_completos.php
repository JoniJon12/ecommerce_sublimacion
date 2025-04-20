<?php
require_once '../../config.php';
header('Content-Type: application/json');

// Obtener ID del usuario desde los parámetros
$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;

if ($id_usuario <= 0) {
    echo json_encode(["status" => "error", "message" => "ID de usuario inválido"]);
    exit;
}

// Obtener pedidos del usuario
$sqlPedidos = "SELECT id_pedido, fecha_pedido, total, estado FROM pedido WHERE id_usuario = ?";
$stmt = $conn->prepare($sqlPedidos);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$pedidos = [];

while ($pedido = $result->fetch_assoc()) {
    $id_pedido = $pedido['id_pedido'];

    // Obtener los productos de cada pedido
    $sqlDetalle = "
        SELECT dp.id_producto, dp.cantidad, dp.precio_unitario, p.nombre, p.imagen
        FROM detalle_pedido dp
        JOIN producto p ON dp.id_producto = p.id_producto
        WHERE dp.id_pedido = ?
    ";
    $stmtDetalle = $conn->prepare($sqlDetalle);
    $stmtDetalle->bind_param("i", $id_pedido);
    $stmtDetalle->execute();
    $resultDetalle = $stmtDetalle->get_result();

    $productos = [];
    while ($prod = $resultDetalle->fetch_assoc()) {
        $productos[] = $prod;
    }

    $pedido['productos'] = $productos;
    $pedidos[] = $pedido;
}

echo json_encode([
    "status" => "success",
    "pedidos" => $pedidos
]);
?>
