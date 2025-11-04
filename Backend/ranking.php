<?php
require_once '../conexion.php'; // Se asume que aquí ya se inicia sesión y define $pdo

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

try {
    // 🔹 Mostrar los usuarios ordenados por puntos totales (sumando todas sus partidas)
    $stmt = $pdo->query("
        SELECT 
            u.usuario,
            COALESCE(SUM(p.puntuacio), 0) AS puntuacion
        FROM usuarios u
        LEFT JOIN partides p ON u.id = p.usuario_id
        GROUP BY u.id, u.usuario
        ORDER BY puntuacion DESC
        LIMIT 10
    ");

    $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error al cargar ranking: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>RANKING GLOBAL</title>
    <link rel="stylesheet" href="../arcade.css">
    <style>
        .arcade-container { max-width: 600px; margin: auto; text-align: center; }
        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
            margin-top: 20px;
        }
        .ranking-table th, .ranking-table td {
            border: 1px solid var(--color-marco, #333);
            padding: 8px;
        }
        .ranking-table th {
            background-color: var(--color-marco, #333);
            color: var(--color-fondo, #fff);
        }
        .ranking-table tr:nth-child(even) {
            background-color: #222;
        }
    </style>
</head>
<body>
    <div class="arcade-container">
        <h1>🏆 RANKING GLOBAL TOP 10 🏆</h1>

        <table class="ranking-table">
            <thead>
                <tr>
                    <th>POS.</th>
                    <th>JUGADOR</th>
                    <th>PUNTOS TOTALES</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ranking && count($ranking) > 0): ?>
                    <?php foreach ($ranking as $i => $fila): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($fila['usuario']) ?></td>
                            <td><?= htmlspecialchars($fila['puntuacion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Aún no hay puntuaciones registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p style="margin-top: 30px;"><a href="plataforma.php">⬅️ VOLVER A LA PLATAFORMA</a></p>
    </div>
</body>
</html>

