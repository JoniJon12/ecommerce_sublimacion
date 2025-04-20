<?php 
require_once '../../config.php';
header('Content-Type: application/json');

// Verificar parámetro
if (!isset($_GET['id_pedido'])) {
    echo json_encode(["status" => "error", "message" => "Falta el ID del pedido"]);
    exit;
}

$id_pedido = intval($_GET['id_pedido']);

// Obtener los productos del pedido
$sql = "SELECT dp.id_producto, dp.cantidad, dp.precio_unitario, p.nombre, p.imagen 
        FROM detalle_pedido dp
        INNER JOIN producto p ON dp.id_producto = p.id_producto
        WHERE dp.id_pedido = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Error preparando consulta"]);
    exit;
}

$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];

while ($row = $result->fetch_assoc()) {
    // Agregar la carpeta 'uploads/' al nombre de la imagen
    $row['imagen'] = 'uploads/' . $row['imagen'];
    $productos[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $productos
]);

$stmt->close();
$conn->close();
?>

