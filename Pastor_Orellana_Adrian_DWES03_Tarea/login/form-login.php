<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../codeparts/estilo.css">
    <title>Formulario de Login</title>
</head>
<body>
    <h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
    <h1>Formulario de Login</h1>
<?php if (isset($_SESSION['user'])): ?>
    <div class="mensaje-exito">
        <p>Ya hay una sesión iniciada. No es necesario volver a autenticarse.</p>
    </div>
<div class="acciones">
<a href="../index.php" class="boton-enlace">Volver a la página principal</a>
 </div><?php else: ?>
    <form action="procesarlogin.php" method="POST">
        <div>
            <label for="login">Usuario</label>
            <input type="text" id="login" name="login" required>
        </div>

        <div>
            <label for="contraseña">Contraseña</label>
            <input type="password" id="contraseña" name="contraseña" required>
        </div>

        <input type="submit" value="Entrar">
    </form>
<?php endif; ?>

</body>
</html>