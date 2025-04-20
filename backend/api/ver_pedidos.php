<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

if (!isset($_GET['id_usuario'])) {
    echo json_encode(["status" => "error", "message" => "Falta el id_usuario"]);
    exit;
}

$id_usuario = intval($_GET['id_usuario']);

// Obtener pedidos del usuario
$sql = "SELECT id_pedido, fecha_pedido, total, estado FROM pedido WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$pedidos = [];

while ($row = $result->fetch_assoc()) {
    $pedido_id = $row['id_pedido'];

    // Obtener productos de este pedido
    $sql_detalle = "
        SELECT dp.id_producto, p.nombre, dp.cantidad, dp.precio_unitario
        FROM detalle_pedido dp
        JOIN producto p ON dp.id_producto = p.id_producto
        WHERE dp.id_pedido = ?
    ";
    $stmt_detalle = $conn->prepare($sql_detalle);
    $stmt_detalle->bind_param("i", $pedido_id);
    $stmt_detalle->execute();
    $result_detalle = $stmt_detalle->get_result();

    $productos = [];
    while ($prod = $result_detalle->fetch_assoc()) {
        $productos[] = $prod;
    }

    $row['productos'] = $productos;
    $pedidos[] = $row;
}

echo json_encode(["status" => "success", "pedidos" => $pedidos], JSON_UNESCAPED_UNICODE);

$conn->close();
?>
