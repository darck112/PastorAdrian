<?php
session_start();
require_once __DIR__ . '/accesoareaprivada.php';
require_once __DIR__ . '/../funciones/dao.php';

// Variables para controlar el estado final del proceso
// $mensaje contendrá el texto a mostrar al usuario
// $error indicará si el proceso ha fallado o no
$mensaje = '';
$error = false;

/*
 * Hacemos una comprobación básica del proceso de votación
 * 
 * Para que el voto sea válido deben cumplirse TRES condiciones:
 *  - El usuario ha marcado el checkbox de confirmación
 *  - El usuario ha marcado el checkbox de declaración
 *  - Existe información de una votación en curso almacenada en sesión
 * 
 * Si alguna de estas condiciones no se cumple, el proceso se considera inválido.
 */
if (
    empty($_POST['confirmar']) ||
    empty($_POST['declaracion']) ||
    empty($_SESSION['voto_en_curso'])
) {
    $error = true;
    $mensaje = 'No ha confirmado correctamente el proceso de votación.';
} else {

    /*
     * Recuperamos los datos de la votación en curso desde la sesión
     * 
     * Estos datos fueron almacenados previamente en form-confirmar-voto.php
     * y contienen la película, la valoración y el comentario.
     */
    $voto = $_SESSION['voto_en_curso'];

    // Recuperamos el identificador del usuario autenticado
    $idUsuario = $_SESSION['user']['id_usuario'];

    /*
     * Comprobamos nuevamente si el usuario ya había votado esta película
     * 
     * Esta comprobación es redundante de forma intencionada,
     * ya que nunca se debe confiar únicamente en validaciones anteriores.
     */
    if (usuarioHaVotadoPelicula($idUsuario, $voto['pelicula'])) {

        // Eliminamos la votación en curso para evitar estados inconsistentes
        unset($_SESSION['voto_en_curso']);

        $error = true;
        $mensaje = 'Ya había votado esta película. No se permite más de un voto.';
    } else {

        /*
         * Insertamos el voto en la base de datos
         * 
         * La información utilizada procede exclusivamente de la sesión,
         * no del formulario, para evitar manipulaciones.
         */
        insertarVoto(
            $voto['pelicula'],
            $idUsuario,
            $voto['valoracion'],
            $voto['comentario']
        );

        /*
         * Una vez registrado el voto, eliminamos la información
         * de votación en curso para permitir nuevas votaciones.
         */
        unset($_SESSION['voto_en_curso']);

        $mensaje = 'Voto registrado correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100%, initial-scale=1.0">
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
    <title>Proceso de votación</title>
</head>
<body>

<h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>

<?php if ($error): ?>
    <div class="mensaje-error">
        <?= htmlspecialchars($mensaje) ?>
    </div>
   <div class="acciones">
    <a href="../index.php">Volver al listado de películas</a>
    <a href="form-confirmar-voto.php">Volver a la confirmación</a>
</div>

<?php else: ?>
    <div class="mensaje-exito">
        <?= htmlspecialchars($mensaje) ?>
    </div>
   <div class="acciones">
    <a href="../index.php">Volver al listado de películas</a>
</div>
<?php endif; ?>

</body>
</html>
