<?php
//quitamos el session_start de este script, para evitar duplicidades en el resto de script que hacen uso de el
//session_start();
// Inicializamos variables de control
$error = false;
$mensaje = '';

//Comprobamos que exista un usuario autenticado
//si no existe se deniega el acceso
if (!isset($_SESSION['user'])) {
    $error = true;
    $mensaje = 'Debe iniciar sesión para acceder a esta sección.';
}

// Validamos la existencia del último acceso
//si por algun motivo no existe, consideramos la sesion invalida
//eliminando la informacion del usuario
elseif (!isset($_SESSION['user']['ultimoacceso'])) {
    unset($_SESSION['user']);
    $error = true;
    $mensaje = 'Sesión inválida. Por favor, inicie sesión de nuevo.';
}

//Comprobamos caducidad de sesión (300 segundos)
else {
    $tiempo_actual = time();
    $ultimo_acceso = $_SESSION['user']['ultimoacceso'];
    //si caduca eliminamos los datos de usuario
    if (($tiempo_actual - $ultimo_acceso) > 300) {
        unset($_SESSION['user']);
        $error = true;
        $mensaje = 'La sesión ha caducado por inactividad.';
    } else {
        // Renovamos el tiempo de último acceso
        //haciendo que la sesion siga siendo valida
        $_SESSION['user']['ultimoacceso'] = $tiempo_actual;
    }
}

//  Si hay error, guardamos el mensaje y redirigimos
if ($error) {
    $_SESSION['mensaje_error'] = $mensaje;
    header('Location: ../index.php');
    exit;
}