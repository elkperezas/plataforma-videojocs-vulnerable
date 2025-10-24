<?php
// Usamos el ajuste de ruta para salir de Backend/
require_once '../conexion.php'; 

// 1. Verificar Sesión (Seguridad)
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401); // No Autorizado
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. No has iniciado sesión.']);
    exit;
}

// 2. Obtener y validar datos de la solicitud (asumiendo que se envía JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$usuario_id = $_SESSION['usuario_id'];
$puntuacion = $data['puntuacion'] ?? 0;
$nombre_juego = $data['juego'] ?? 'desconocido'; // Útil si quieres diferenciar rankings por juego

// Convertir a entero y validar
$puntuacion = (int)$puntuacion;
if ($puntuacion < 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Puntuación inválida.']);
    exit;
}

// 3. Guardar en la Base de Datos
try {
    // Consulta para insertar la nueva partida
    $sql = "INSERT INTO partides (usuario_id, puntuacio, joc) VALUES (:usuario_id, :puntuacion, :juego)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':puntuacion', $puntuacion, PDO::PARAM_INT);
    $stmt->bindParam(':juego', $nombre_juego, PDO::PARAM_STR);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Puntuación guardada con éxito.', 'score' => $puntuacion]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta de guardado.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>


