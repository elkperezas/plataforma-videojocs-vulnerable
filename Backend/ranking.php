<?php
// NOTA: Se usa '../conexion.php' porque ranking.php está dentro de Backend/
require_once '../conexion.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>RANKING GLOBAL</title>
    <link rel="stylesheet" href="../arcade.css">
    <style>
        .arcade-container { max-width: 600px; }
        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8em;
            margin-top: 20px;
        }
        .ranking-table th, .ranking-table td {
            border: 1px solid var(--color-marco);
            padding: 8px;
            text-align: left;
        }
        .ranking-table th {
            background-color: var(--color-marco);
            color: var(--color-fondo);
        }
    </style>
</head>
<body>
    <div class="arcade-container">
        <h1>RANKING GLOBAL TOP 10</h1>
        
        <table class="ranking-table">
            <thead>
                <tr>
                    <th>POS.</th>
                    <th>JUGADOR</th>
                    <th>PUNTUACIÓN</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>MASTER_8BIT</td><td>99999</td></tr>
                <tr><td>2</td><td>NEON_GAMER</td><td>85420</td></tr>
                <tr><td>3</td><td>PIXEL_KING</td><td>70125</td></tr>
                </tbody>
        </table>
        
        <p style="margin-top: 30px;"><a href="plataforma.php">VOLVER A LA PLATAFORMA</a></p>
    </div>
</body>
</html>