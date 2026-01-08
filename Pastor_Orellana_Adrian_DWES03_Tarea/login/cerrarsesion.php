<?php
session_start();

 //Comprobamos si existe una sesión de usuario activa.
 //Si existe, se elimina la información del usuario autenticado,
// cerrando así la sesión.

if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    // Eliminamos los datos del usuario de la sesión
    $mensaje = "Sesión cerrada correctamente.";
} else {
    $mensaje = "No había ninguna sesión iniciada.";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
    <title>Cierre de sesión</title>
</head>

<body>
    <h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
    <h1>Cierre de sesión</h1>
     <div class="mensaje-exito">
    <p><?= htmlspecialchars($mensaje) ?></p>
</div>
 <div class="acciones">
<a href="../index.php" class="boton-enlace">Volver a la página principal</a>
 </div>
</body>
</html>