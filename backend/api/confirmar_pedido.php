<?php
require_once '../../config.php';
header('Content-Type: application/json');

// Obtener los datos del usuario
$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = $data['id_usuario'];

if (!$id_usuario) {
    echo json_encode(["status" => "error", "message" => "Falta el id_usuario"]);
    exit;
}

// 1. Buscar el carrito activo del usuario
$sqlCarrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ?";
$stmtCarrito = $conn->prepare($sqlCarrito);
$stmtCarrito->bind_param("i", $id_usuario);
$stmtCarrito->execute();
$resultCarrito = $stmtCarrito->get_result();

if ($resultCarrito->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Carrito no encontrado"]);
    exit;
}

$row = $resultCarrito->fetch_assoc();
$id_carrito = $row['id_carrito'];

// 2. Obtener productos del carrito
$sqlProductos = "SELECT id_producto, cantidad FROM carrito_producto WHERE id_carrito = ?";
$stmtProd = $conn->prepare($sqlProductos);
$stmtProd->bind_param("i", $id_carrito);
$stmtProd->execute();
$resultProd = $stmtProd->get_result();

if ($resultProd->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "El carrito está vacío"]);
    exit;
}

// 3. Calcular total
$total = 0;
$productos = [];
while ($prod = $resultProd->fetch_assoc()) {
    $id_producto = $prod['id_producto'];
    $cantidad = $prod['cantidad'];

    // Obtener precio actual del producto
    $stmtPrecio = $conn->prepare("SELECT precio FROM producto WHERE id_producto = ?");
    $stmtPrecio->bind_param("i", $id_producto);
    $stmtPrecio->execute();
    $resultPrecio = $stmtPrecio->get_result()->fetch_assoc();
    $precio_unitario = $resultPrecio['precio'];
    
    $total += $precio_unitario * $cantidad;

    $productos[] = [
        'id_producto' => $id_producto,
        'cantidad' => $cantidad,
        'precio_unitario' => $precio_unitario
    ];
}

// 4. Crear pedido
$sqlPedido = "INSERT INTO pedido (id_usuario, fecha_pedido, estado, total) VALUES (?, NOW(), 'Pendiente', ?)";
$stmtPedido = $conn->prepare($sqlPedido);
$stmtPedido->bind_param("id", $id_usuario, $total);
$stmtPedido->execute();
$id_pedido = $stmtPedido->insert_id;

// 5. Insertar en detalle_pedido
foreach ($productos as $prod) {
    $sqlDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                   VALUES (?, ?, ?, ?)";
    $stmtDetalle = $conn->prepare($sqlDetalle);
    $stmtDetalle->bind_param("iiid", $id_pedido, $prod['id_producto'], $prod['cantidad'], $prod['precio_unitario']);
    $stmtDetalle->execute();
}

// 6. Vaciar el carrito
$conn->query("DELETE FROM carrito_producto WHERE id_carrito = $id_carrito");

// 7. Registrar pago (estado: pendiente)
$sqlPago = "INSERT INTO pago (id_pedido, metodo_pago, estado_pago, fecha_pago)
            VALUES (?, 'Pendiente', 'Pendiente', NOW())";
$stmtPago = $conn->prepare($sqlPago);
$stmtPago->bind_param("i", $id_pedido);
$stmtPago->execute();

// ✅ Éxito
echo json_encode(["status" => "success", "message" => "Pedido confirmado y registrado", "id_pedido" => $id_pedido]);
?>
