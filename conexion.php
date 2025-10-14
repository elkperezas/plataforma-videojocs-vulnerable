<?php
// Iniciar sesión para usar variables de sesión
session_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost'); // Tu host (generalmente 'localhost')
define('DB_USER', 'fernando'); // Tu usuario de la base de datos
define('DB_PASS', 'la33seacerca'); // Tu contraseña de la base de datos
define('DB_NAME', 'a14mng'); // El nombre de tu base de datos

try {
    // Cadena de conexión DSN
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    
    // Opciones de conexión
    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activar excepciones para errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Modo de obtención predeterminado: array asociativo
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Desactivar la emulación de preparaciones para mayor seguridad
    ];
    
    // Crear la instancia de PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    
} catch (PDOException $e) {
    // En caso de error de conexión, detener el script y mostrar el error
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Nota: Para este ejemplo, se asume que tienes una tabla 'usuarios' con campos: 'id', 'usuario', 'password'.
?>