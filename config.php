<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_sublimacion";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
