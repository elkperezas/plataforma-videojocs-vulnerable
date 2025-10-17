<?php
// ... (código PHP de plataforma.php - sin cambios) ...

require_once '../conexion.php';

// Verificar si el usuario NO está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php'); 
    exit;
}

$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'PLAYER 1';

// Lógica de logout
if (isset($_POST['logout'])) {
    session_unset();    
    session_destroy();  
    header('Location: ../index.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PLATAFORMA ARCADE</title>
    <link rel="stylesheet" href="../arcade.css"> 
    <style>
        /* Estilos ESPECÍFICOS para la plataforma de juegos */
        .arcade-container {
            max-width: 700px; 
        }
        
        /* Estilos para los enlaces de navegación Perfil/Ranking */
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-links a {
            font-size: 0.9em;
            text-transform: uppercase;
            padding: 8px 15px;
            border: 2px solid var(--color-texto);
            color: var(--color-texto);
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparente */
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-links a:hover {
            border-color: var(--color-boton-fondo);
            color: var(--color-boton-fondo);
            box-shadow: 0 0 10px var(--color-boton-fondo);
        }
        /* Fin Estilos Nav */
        
        .games-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        
        .game-card {
            height: 200px;
            border: 5px solid var(--color-marco);
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            position: relative;
        }
        
        /* Overlay para el texto del juego */
        .game-card::before {
            content: attr(data-title);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2em;
            color: var(--color-texto);
            text-shadow: 3px 3px var(--color-fondo);
            padding: 10px;
        }

        .game-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px var(--color-marco);
        }

        /* Imágenes de fondo (rutas relativas a plataforma.php en Backend/) */
        .game-buscaminas {
            background-image: url('../img/buscaminas.jpg'); 
        }
        .game-naves {
            background-image: url('../img/naves.jpg'); 
        }

        .logout-form {
            margin-top: 30px;
        }
        .logout-form button {
            background-color: var(--color-error);
            border-color: var(--color-texto);
        }
    </style>
</head>
<body>
    <div class="arcade-container">
        <h1>PLATAFORMA DE JUEGOS</h1>
        
        <p style="color: var(--color-marco); font-size: 1em;">¡BIENVENIDO, **<?php echo htmlspecialchars(strtoupper($nombre_usuario)); ?>**! PREPÁRATE PARA JUGAR.</p>
        
        <div class="nav-links">
            <a href="perfil.php">MI PERFIL</a>
            <a href="ranking.php">RANKING TOP</a>
        </div>
        <h2>ELIGE NIVEL:</h2>
        
        <div class="games-grid">
            
            <a href="Juegos/Buscaminas/index.html" class="game-card game-buscaminas" data-title="BUSCAMINAS">
            </a>
            
            <a href="Juegos/juego_naves/index.html" class="game-card game-naves" data-title="NAVES ESPACIALES">
            </a>
            
        </div>
        
        <form method="POST" action="plataforma.php" class="logout-form">
            <button type="submit" name="logout">FINISH GAME (LOGOUT)</button>
        </form>
    </div>
</body>
</html>