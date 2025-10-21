<?php
// NOTA: Se usa '../conexion.php' porque perfil.php está dentro de Backend/
require_once '../conexion.php'; 

// Inicia sesión si aún no se ha hecho (asume que la sesión ya está iniciada)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'USUARIO DESCONOCIDO';

$mensaje = '';
$error = '';
$foto_actual = '../img/default_avatar.png'; // Ruta de avatar por defecto

// --- Lógica para obtener la foto actual del usuario ---
try {
    // Esta parte es segura (usa consultas preparadas)
    $sql_fetch = "SELECT foto_perfil FROM usuarios WHERE id = :id";
    $stmt_fetch = $pdo->prepare($sql_fetch);
    $stmt_fetch->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt_fetch->execute();
    $user_data = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

    if ($user_data && $user_data['foto_perfil']) {
        // La ruta de la imagen se usa directamente, lo cual es correcto
        if (file_exists('../' . $user_data['foto_perfil'])) {
            $foto_actual = '../' . $user_data['foto_perfil'];
        }
    }
} catch (PDOException $e) {
    $error = "Error al cargar datos: " . $e->getMessage();
}


// --- Lógica para subir la foto de perfil (VULNERABLE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    $archivo = $_FILES['foto_perfil'];
    
    // 1. Validaciones
    $nombre_archivo = $archivo['name'];
    $tipo_archivo = $archivo['type'];
    $tamano_archivo = $archivo['size'];
    $tmp_ruta = $archivo['tmp_name'];
    $error_archivo = $archivo['error'];

    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    
 

    if ($error_archivo !== UPLOAD_ERR_OK) {
        $error = "Error al subir el archivo. Código: " . $error_archivo;
    } else {
        // 2. Definir la ruta final
        // Usamos el ID del usuario y un timestamp para un nombre de archivo único
        $nuevo_nombre = $usuario_id . '_' . time() . '.' . $extension;
        $ruta_destino_absoluta = dirname(__DIR__) . '/uploads/' . $nuevo_nombre;
        
        // Esta es la ruta que guardaremos en la DB (relativa al directorio principal)
        $ruta_destino_db = 'uploads/' . $nuevo_nombre; 

        if (move_uploaded_file($tmp_ruta, $ruta_destino_absoluta)) {
            
            // 3. Actualizar la base de datos (Esta parte sigue siendo segura contra SQLi)
            $sql_update = "UPDATE usuarios SET foto_perfil = :foto WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':foto', $ruta_destino_db);
            $stmt_update->bindParam(':id', $usuario_id, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                $mensaje = "¡Foto de perfil actualizada con éxito!";
                $foto_actual = '../' . $ruta_destino_db; // Actualizar la imagen mostrada
            } else {
                $error = "Error al guardar la ruta en la base de datos.";
            }
        } else {
            $error = "Error desconocido al mover el archivo subido.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PERFIL DE USUARIO VULNERABLE</title>
    <link rel="stylesheet" href="../arcade.css">
    <style>
        .arcade-container { max-width: 500px; }
        
        .avatar-area {
            margin-bottom: 20px;
            text-align: center;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border: 5px solid var(--color-marco);
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 15px var(--color-marco);
        }
        
        /* Estilo para el input tipo file */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-top: 10px;
            cursor: pointer;
        }

        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-wrapper .btn-arcade {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--color-boton-fondo);
            color: var(--color-boton-texto);
            border: 3px solid #ffffff;
            padding: 8px 15px;
            font-size: 0.8em;
            box-shadow: 2px 2px #000;
        }
        .file-input-wrapper .btn-arcade:hover {
             background-color: #ffa500;
        }
        
    </style>
</head>
<body>
    <div class="arcade-container">
        <h1>PERFIL DE JUGADOR VULNERABLE</h1>

        <?php if ($mensaje): ?>
            <p class="mensaje-exito"><?php echo $mensaje; ?></p>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="avatar-area">
            <img src="<?php echo htmlspecialchars($foto_actual); ?>" alt="Foto de Perfil" class="avatar">
            <p>ALIAS: <span style="color: var(--color-marco);"><?php echo htmlspecialchars(strtoupper($nombre_usuario)); ?></span></p>
        </div>

        <h2 style="font-size: 1em;">CAMBIAR FOTO</h2>
        
        <form method="POST" action="perfil.php" enctype="multipart/form-data" style="margin-top: 20px;">
            
            <div class="file-input-wrapper">
                 <button type="button" class="btn-arcade">SELECCIONAR ARCHIVO</button>
                 <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" onchange="this.form.submit()">
            </div>
            
            <p style="font-size: 0.7em; margin-top: 15px; line-height: 1.5; color: #fff;">Solo se pueden poner extensiones ".png, .jpg".</p>
        </form>
        
        <p style="margin-top: 30px;"><a href="plataforma.php">VOLVER A LA PLATAFORMA</a></p>
    </div>
</body>
</html>