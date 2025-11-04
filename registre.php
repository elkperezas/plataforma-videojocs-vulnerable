<?php
// ... Tu código PHP de conexión (asumiendo que 'conexion.php' establece $pdo) ...
require_once 'conexion.php';

$mensaje = '';
$error = '';
// Inicializar la variable $usuario para evitar errores si no hay POST
$usuario = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    
    $usuario = $_POST['usuario']; 
    $password = $_POST['password']; 
    $password_confirm = $_POST['password_confirm'];

    if (empty($usuario) || empty($password) || empty($password_confirm)) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $password_confirm) {
        $error = "Las contraseñas no coinciden.";
    } else {
        try {
            
            $sql = "SELECT id FROM usuarios WHERE usuario = :usuario";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "El nombre de usuario ya está en uso.";
            } else {
                
               
                $password_a_guardar = $password;

                $sql_insert = "INSERT INTO usuarios (usuario, password) VALUES (:usuario, :password)";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->bindParam(':usuario', $usuario);
                $stmt_insert->bindParam(':password', $password_a_guardar); // Se inserta la contraseña sin cifrar.
                
                if ($stmt_insert->execute()) {
                    $mensaje = "¡Registro exitoso para " . $usuario . "! Ya puedes <a href='index.php'>iniciar sesión</a>.";
                    // NO limpiamos $usuario para la demostración de XSS en el HTML.
                } else {
                    $error = "Error al intentar registrar el usuario.";
                }
            }
        } catch (PDOException $e) {
            $error = "Error de base de datos: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro ORBR Vulnerable</title>
    <link rel="stylesheet" href="arcade.css"> 
</head>
<body>
    <div class="arcade-container">
        <h1>REGISTRO NUEVO JUGADOR</h1>
        
        <?php if ($mensaje): ?>
            <p class="mensaje-exito"><?php echo $mensaje; ?></p>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="registre.php">
            <div>
                <label for="usuario">NOMBRE DE JUGADOR (ALIAS):</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>
            </div>
            <div>
                <label for="password">CÓDIGO SECRETO:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password_confirm">REPITE CÓDIGO SECRETO:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <div>
                <button type="submit" name="registro">EARN WINGS</button>
            </div>
        </form>
        
        <p>¿YA ESTÁS REGISTRADO? <a href="index.php">VOLVER AL LOGIN</a></p>
    </div>
</body>
</html>