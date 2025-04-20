<?php
require_once "includes/conexion.php";

if ($conexion) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Error de conexión.";
}
?>
