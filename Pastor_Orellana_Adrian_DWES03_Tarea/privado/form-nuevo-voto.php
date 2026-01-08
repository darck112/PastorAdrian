<?php
session_start();

require_once __DIR__ . '/accesoareaprivada.php';
require_once __DIR__ . '/../funciones/dao.php';

 // Variables de contro
 //  $aviso aviso informativo (no bloquea el formulario)
 // $mostrarFormulario-> permite decidir si se muestra o no el formulario

$error = false;
$mensaje = '';
$aviso = '';
$mostrarFormulario = true;

// Recibimos el id de la película
$id_pelicula = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
//Si el id no existe mostramos los errores
if (!$id_pelicula) {
    $error = true;
    $mensaje = 'Id de película no válido.';
    $mostrarFormulario = false;
    //en caso contrario buscamos la pelicula en la base de datos
} else {
    $pelicula = obtenerPeliculaPorId($id_pelicula);
//si esta no existe lanzamos el mensaje
    if (!$pelicula) {
        $error = true;
        $mensaje = 'Película no encontrada.';
        $mostrarFormulario = false;
    }
}

// Recuperamos el usuario de la sesion, que deberá estar autenticado
$idUsuario = $_SESSION['user']['id_usuario'] ?? null;
//Si existen errores y el usuario es nulo lanzamos error
//Esto mas aque nada por precaucion. 
if (!$error && $idUsuario === null) {
    $error = true;
    $mensaje = 'No hay usuario autenticado en la sesión.';
    $mostrarFormulario = false;
}

// Si pasa las verificaciones anteriores, comprobamos que el usuario
// no haya votado ya anteriormente la pelicula
if (!$error && usuarioHaVotadoPelicula($idUsuario, $id_pelicula)) {
    $error = true;
    $mensaje = 'Ya ha votado esta película. No se permite más de un voto por película.';
    $mostrarFormulario = false;
}

// Hacemos la comprobacion de si se ha quedado una votacion pendiente
//para lanzar un aviso
if (!$error && !empty($_SESSION['voto_en_curso'])) {
    $aviso = 'Existe una votación en curso pendiente de confirmar.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de votación</title>
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
</head>
<body>

<h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
<h1>Formulario de votación</h1>

<?php if ($error): ?>

    <div class="mensaje-error">
        <?= htmlspecialchars($mensaje) ?>
    </div>

    <div class="acciones">
        <a href="../index.php" class="boton-enlace">Volver al listado de películas</a>
    </div>

<?php else: ?>

    <?php if ($aviso): ?>
        <div class="mensaje-aviso">
            <?= htmlspecialchars($aviso) ?>
        </div>

        <div class="acciones">
            <a href="form-confirmar-voto.php" class="boton-enlace">
                Ir a confirmar la votación
            </a>
        </div>
    <?php endif; ?>

    <h2>Datos de la película</h2>
    <div>
        <strong>Título:</strong> <?= htmlspecialchars($pelicula['titulo']) ?><br>
        <strong>Género:</strong> <?= htmlspecialchars($pelicula['genero']) ?><br>
        <strong>Director:</strong> <?= htmlspecialchars($pelicula['direccion']) ?><br>
        <strong>Duración:</strong> <?= (int)$pelicula['duracion'] ?> minutos<br>
        <strong>Año:</strong> <?= (int)$pelicula['anio'] ?>
    </div>

    <h2>Formulario para votar y comentar</h2>
    <form action="form-confirmar-voto.php" method="POST">

        <input type="hidden" name="id_pelicula" value="<?= (int)$pelicula['id'] ?>">

        <label for="valoracion">Valoración (1–5):</label>
        <select name="valoracion" id="valoracion" required>
            <option value="">Seleccione</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="comentario">Comentario:</label>
        <textarea name="comentario" id="comentario" rows="4"></textarea>

        <input type="submit" value="Enviar voto y comentario">
    </form>

<?php endif; ?>

</body>
</html>
