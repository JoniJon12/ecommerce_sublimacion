<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ecommerce_sublimacion';

$conn = new mysqli($host, $user, $password, $database);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
