<?php
session_start();

require_once __DIR__ . '/accesoareaprivada.php';
require_once __DIR__ . '/../funciones/dao.php';
// Variables de control de estado y mensajes
$error = false;
$mensaje = '';

// Recuperamos el usuario autenticado que se guarda en la sesion
//cuando hacemos el login
$idUsuario = $_SESSION['user']['id_usuario'] ?? null;

// Recuperamos y validamos el id de lacritica
$idCritica = filter_input(INPUT_POST, 'id_critica', FILTER_VALIDATE_INT);
//si no existe o no es un numero entero lanzamos un error
if (!$idCritica) {
    $error = true;
    $mensaje = 'Identificador de crítica no válido.';
} else {

    // Comprobamos que la crítica exista en la base de datos
    $critica = obtenerCriticaPorId($idCritica);
//si no existe lanzamos un error
    if (!$critica) {
        $error = true;
        $mensaje = 'La valoración solicitada no existe.';
    }

    // Si, exite, verificamos que la crítica pertenezca al usuario
    //para que no pueda ser eliminada por ningun otro usuario
    elseif ($critica['usuario'] != $idUsuario) {
        $error = true;
        $mensaje = 'No puede eliminar una valoración que no le pertenece.';
    }

    // Eliminamos la crítica
    else {
        eliminarCritica($idCritica);
        $mensaje = 'La valoración y el comentario han sido eliminados correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100%, initial-scale=1.0">
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
    <title>Eliminar voto</title>
</head>
<body>
    <h1>DWES 03. AUTOR: Adrian Pastor Orellana.</h1>
    <h1>Eliminar voto y comentario</h1>
<?php if ($error): ?>
    <div class="mensaje-error">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php else: ?>
    <div class="mensaje-exito">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<div class="acciones">
    <a href="../index.php" class="boton-enlace">Volver al listado de películas</a>
</div>

</body>
</html>