<?php
require_once '../../config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id_usuario = $data['id_usuario'];
    $productos = $data['productos']; // Array de productos

    if (empty($id_usuario) || empty($productos)) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        exit;
    }

    // Calcular total
    $total = 0;
    foreach ($productos as $p) {
        $total += $p['cantidad'] * $p['precio_unitario'];
    }

    // Crear pedido
    $stmt = $conn->prepare("INSERT INTO pedido (id_usuario, fecha_pedido, estado, total) VALUES (?, NOW(), 'Pendiente', ?)");
    $stmt->bind_param("id", $id_usuario, $total);

    if ($stmt->execute()) {
        $id_pedido = $stmt->insert_id;
        $stmt->close();

        // Insertar detalle del pedido
        $stmt_detalle = $conn->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");

        foreach ($productos as $p) {
            $stmt_detalle->bind_param("iiid", $id_pedido, $p['id_producto'], $p['cantidad'], $p['precio_unitario']);
            $stmt_detalle->execute();
        }

        $stmt_detalle->close();
        echo json_encode(["status" => "success", "message" => "Pedido creado con éxito", "id_pedido" => $id_pedido]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al crear el pedido"]);
    }

    $conn->close();
}
?>
