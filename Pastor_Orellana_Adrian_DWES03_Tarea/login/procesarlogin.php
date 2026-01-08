<?php
session_start();
require_once __DIR__ . '/../funciones/dao.php';
// Variables de control del resultado del proceso
$error = false;
$mensaje= ""; 
// Usuario ya autenticado, si existe informacion no es necesario volver a procesar el login
if (isset($_SESSION['user'])) {
    $mensaje = "El usuario ya estaba autenticado previamente.";
    $error = false;
}
// Acceso no válido, si el acceso no es via POST, se considera un
// intento de acceso no valido
elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $mensaje = "Acceso no válido al formulario.";
    $error = true;
}
else {//procesaimiento normal del formulario
    //recogemos y saneamos los datos
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['contraseña'] ?? '';
//validacion basica, ambos campos son obligatirios
    if ($login === '' || $password === '') {
        $mensaje = "Debe introducir usuario y contraseña.";
        $error = true;
    } else {
        try {
            //conectamos con la base de datos y validamos las credenciales
            $pdo = Conectar();
            $id_usuario = autenticarUsuario($pdo, $login, $password);
               //si buiera error lo recogemos para mostrarlo
            if ($id_usuario === false) {
                $mensaje = "Usuario o contraseña incorrectos.";
                $error = true;
                //en caso de todo correcto, iniciamos la sesion con los datos del usuario
            } else {
                $_SESSION['user']['id_usuario'] = $id_usuario;
                $_SESSION['user']['ultimoacceso'] = time();
                $mensaje = "Autenticación correcta.";
                $error = false;
            }

        } catch (Exception $e) {
            $mensaje = "Error en la conexión con la base de datos.";
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de la operación de login</title>
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
    <h2>Resultado de la operación de login</h2>
<div class="<?= $error ? 'mensaje-error' : 'mensaje-exito' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<div class="acciones">

<a href="form-login.php" class="boton-enlace">Volver al formulario de login</a><br>
<a href="../index.php" class="boton-enlace">Volver a la página principal</a>
 </div>
</body>
</html>