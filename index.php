<?php
// ... Tu código PHP de conexión (asumiendo que 'conexion.php' establece $pdo) ...
require_once 'conexion.php';

// Si el usuario ya está logueado, redirigir a la página de selección de juegos
if (isset($_SESSION['usuario_id'])) {
    header('Location: seleccion_juegos.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    if (empty($usuario) || empty($password)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        // Consultar el usuario (Esta consulta sigue siendo SEGURA contra SQLi)
        $sql = "SELECT id, password FROM usuarios WHERE usuario = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        
        // 🛑 VULNERABILIDAD 1: Falla en la prevención de Enumeración de Usuarios por Tiempo
        // (Aunque no se introduce aquí directamente, el manejo de errores lo facilita).
        // Se ejecuta la consulta, lo que revela si el usuario existe antes de verificar la contraseña.
        $stmt->execute();
        $usuario_db = $stmt->fetch();

        // 🛑 VULNERABILIDAD 2: Verificación de Contraseña Insegura (Sin Hashing)
        // El script de registro vulnerable anterior ya guardaba la contraseña en texto plano.
        // Aquí se comprueba directamente el texto plano, lo cual es MUY inseguro.
        if ($usuario_db && $password === $usuario_db['password']) {
            
            // Login exitoso
            $_SESSION['usuario_id'] = $usuario_db['id'];
            $_SESSION['usuario_nombre'] = $usuario; 
            
            header('Location: ./Backend/plataforma.php');
            exit;
        } else {
            // 🛑 VULNERABILIDAD 3: Mensaje de error genérico que facilita la Enumeración de Usuarios
            // El mensaje no diferencia entre "Usuario no existe" y "Contraseña incorrecta",
            // pero el tiempo de ejecución sí puede hacerlo (ver explicación abajo).

            // Además, si el registro guarda contraseñas en texto plano, 
            // no hay forma de usar password_verify() aquí, lo cual es la práctica segura.
            $error = "Nombre de usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login ORB Vulnerable</title>
    <link rel="stylesheet" href="arcade.css"> 
</head>
<body>
    <img src="./img/orb.png" alt="Logo ORBR" style="display: block; margin: 20px auto; max-width: 500px;">
    <div class="arcade-container">
        <h1>LOGIN PLAYER MV 1 VULNERABLE</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="index.php">
            <div>
                <label for="usuario">NOMBRE DE USUARIO:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div>
                <label for="password">CONTRASEÑA:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit" name="login">START FLY</button>
            </div>
        </form>
        
        <p>¿NO TIENES TUS ALAAAAS? <a href="registre.php">EARN WINGS</a></p>
    </div>
</body>
</html>