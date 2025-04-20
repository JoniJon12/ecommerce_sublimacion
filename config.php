<?php
// Definir la URL base del proyecto
define("BASE_URL", "http://localhost/ecommerce_sublimacion/");

// Configuración de zona horaria
date_default_timezone_set("Europe/Madrid");

// Iniciar sesión para manejo de autenticación
session_start();

// Incluir la conexión a la base de datos
require_once __DIR__ . '/backend/includes/conexion.php';

?>
