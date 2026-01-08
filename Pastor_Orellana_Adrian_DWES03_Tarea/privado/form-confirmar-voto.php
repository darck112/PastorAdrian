<?php
session_start();

require_once __DIR__ . '/accesoareaprivada.php';
require_once __DIR__ . '/../funciones/dao.php';

$error = false;
$mensaje = '';
$mostrarFormulario = false;

$idUsuario = $_SESSION['user']['id_usuario'] ?? null;

/*
 * CASO 1: venimos del formulario de votación (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idPelicula  = filter_input(INPUT_POST, 'id_pelicula', FILTER_VALIDATE_INT);
    $valoracion  = filter_input(INPUT_POST, 'valoracion', FILTER_VALIDATE_INT);
    $comentario  = trim($_POST['comentario'] ?? '');

    if (!$idPelicula || !$valoracion || $valoracion < 1 || $valoracion > 5) {
        $error = true;
        $mensaje = 'Los datos de la votación no son válidos.';
    }
    elseif (usuarioHaVotadoPelicula($idUsuario, $idPelicula)) {
        $error = true;
        $mensaje = 'Ya ha votado esta película. No se permiten votos duplicados.';
    }
    else {
        // Guardamos la votación en curso
        $_SESSION['voto_en_curso'] = [
            'pelicula'   => $idPelicula,
            'valoracion' => $valoracion,
            'comentario' => $comentario,
            'usuario'    => $idUsuario
        ];

        $mostrarFormulario = true;
    }
}

/*
 * CASO 2: acceso directo o sesión inconsistente
 */
elseif (empty($_SESSION['voto_en_curso'])) {
    $error = true;
    $mensaje = 'No hay ninguna votación en curso para confirmar.';
}

/*
 * Si todo es correcto, recuperamos los datos necesarios
 */
if (!$error && !empty($_SESSION['voto_en_curso'])) {
    $voto = $_SESSION['voto_en_curso'];
    $pelicula = obtenerPeliculaPorId($voto['pelicula']);

    if (!$pelicula) {
        $error = true;
        $mensaje = 'La película asociada a la votación no existe.';
    } else {
        $mostrarFormulario = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100%, initial-scale=1.0">
    <title>Confirme la valoración</title>
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
    <h1>Confirme la valoración</h1>
   <?php if ($error): ?>
    <div class="mensaje-error">
        <?= htmlspecialchars($mensaje) ?>
    </div>

    <div class="acciones">
        <a href="../index.php" class="boton-enlace">Volver al listado de películas</a>
    </div>

<?php elseif ($mostrarFormulario): ?>
       <h2>Datos de la película</h2>
<div>
    <strong>Título:</strong> <?= htmlspecialchars($pelicula['titulo']) ?><br>
    <strong>Género:</strong> <?= htmlspecialchars($pelicula['genero']) ?><br>
    <strong>Director:</strong> <?= htmlspecialchars($pelicula['direccion']) ?><br>
    <strong>Duración:</strong> <?= (int)$pelicula['duracion'] ?> minutos<br>
    <strong>Año:</strong> <?= (int)$pelicula['anio'] ?>
</div>

<!-- Resumen del voto introducido -->
<h2>Resumen de su valoración</h2>
<div>
    <strong>Valoración:</strong> <?= (int)$voto['valoracion'] ?><br>
    <strong>Comentario:</strong><br>
    <?= nl2br(htmlspecialchars($voto['comentario'])) ?>
</div>

<!-- Formulario de confirmación -->
<h2>Confirmación final</h2>
<form action="votar.php" method="POST">

    <label>
        <input type="checkbox" name="confirmar" value="1">
        Confirmo que deseo enviar esta valoración y comentario.
    </label>
    <br><br>

    <label>
        <input type="checkbox" name="declaracion" value="1">
        Declaro que mi valoración y crítica se ajustan a las normas de la comunidad.
    </label>

    <br><br>
    <input type="submit" value="Confirmar voto y comentario">
</form>

<!-- Formulario independiente para descartar -->
<form action="descartarvoto.php" method="POST">
    <input type="submit" value="Descartar voto y comentario">
</form>
<?php endif; ?>
</body>
</html>