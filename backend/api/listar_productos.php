<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config.php';

$response = [
    "status" => "error",
    "productos" => [],
    "message" => "Error inesperado"
];

// Consulta para obtener productos
$sql = "SELECT id_producto, nombre, precio, stock, imagen, fecha_creacion FROM producto";

if ($result = $conn->query($sql)) {
    $productos = [];

    while ($row = $result->fetch_assoc()) {
        // 👇 Ya no modificamos $row['imagen'] aquí
        $productos[] = $row;
    }

    $response['status'] = "success";
    $response['productos'] = $productos;
    $response['message'] = count($productos) > 0 ? "Productos encontrados" : "No hay productos disponibles";
} else {
    $response['message'] = "Error en la consulta: " . $conn->error;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conn->close();
?>

