<?php
require_once '../conexion.php'; // ✅ Usamos la conexión real del proyecto

header('Content-Type: application/json');

// 🔹 Leemos el cuerpo JSON del POST
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['usuario_id']) || !isset($data['puntuacion'])) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$usuario_id = (int)$data['usuario_id'];
$puntuacion = (int)$data['puntuacion'];
$fecha = date('Y-m-d H:i:s');

try {
    // 🔹 Guardar una nueva partida (se acumulan las puntuaciones)
    $sql = "INSERT INTO partides (usuario_id, puntuacio, data_partida) 
            VALUES (:usuario_id, :puntuacion, :fecha)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':puntuacion' => $puntuacion,
        ':fecha' => $fecha
    ]);

    // 🔹 (Opcional) Devolvemos también el total acumulado de puntos del jugador
    $sql_total = "SELECT SUM(puntuacio) AS total_puntos 
                  FROM partides 
                  WHERE usuario_id = :usuario_id";
    $stmt_total = $pdo->prepare($sql_total);
    $stmt_total->execute([':usuario_id' => $usuario_id]);
    $total = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_puntos'] ?? 0;

    echo json_encode([
        "success" => true,
        "message" => "Puntuación guardada correctamente",
        "total_puntos" => (int)$total
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al guardar puntuación",
        "detalle" => $e->getMessage()
    ]);
}
?>
