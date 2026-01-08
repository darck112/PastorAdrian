<?php
session_start();

require_once __DIR__ . '/accesoareaprivada.php';
require_once __DIR__ . '/../funciones/dao.php';
//variables de control
$error = false;
$mensaje = '';
$votos = [];
$pelicula = null;

// Validar id de película
$id_pelicula = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
// si no es correcto el id lanzamos un error 
if (!$id_pelicula) {
    $error = true;
    $mensaje = 'Película no válida.';
   //obtenemos la pelicula de la base de datos
} else {
    $pelicula = obtenerPeliculaPorId($id_pelicula);
//si la pelicula no existe lanzamos un error
    if (!$pelicula) {
        $error = true;
        $mensaje = 'La película no existe.';
    }
}

// Si todo es correcto, obentemos los votos 
if (!$error) {
    $votos = obtenerVotosPorPelicula($id_pelicula);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de votos y críticas</title>
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
</head>
<body>

<h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
<h1>Lista de votos y críticas</h1>

<?php if ($error): ?>

    <div class="mensaje-error">
        <?= htmlspecialchars($mensaje) ?>
    </div>

    <div class="acciones">
        <a href="../index.php" class="boton-enlace">Volver al listado de películas</a>
    </div>

<?php else: ?>

    <h2>Datos de la película</h2>
    <p>
        <strong>Título:</strong> <?= htmlspecialchars($pelicula['titulo']) ?><br>
        <strong>Director:</strong> <?= htmlspecialchars($pelicula['direccion']) ?><br>
        <strong>Año:</strong> <?= (int)$pelicula['anio'] ?>
    </p>

    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Voto</th>
                <th>Crítica</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>

        <?php if (empty($votos)): ?>
            <tr>
                <td colspan="4">No hay votos para esta película.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($votos as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['login']) ?></td>
                    <td><?= (int)$v['voto'] ?></td>
                    <td><?= htmlspecialchars($v['critica']) ?></td>
                    <td>
                        <?php if ($_SESSION['user']['id_usuario'] == $v['usuario_id']): ?>
                            <form method="post" action="eliminarvoto.php">
                                <input type="hidden" name="id_critica" value="<?= (int)$v['id'] ?>">
                                <input type="submit" value="Eliminar voto/crítica">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

    <div class="acciones">
        <a href="../index.php">Volver al listado de películas</a>
        <a href="../login/cerrarsesion.php">Cerrar sesión</a>
    </div>

<?php endif; ?>

</body>
</html>
