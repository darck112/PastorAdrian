<?php
session_start();
//iniciamos la sedion para poder mostrar si el usuario esta autenticado, e incluimos los script necesarios
require_once __DIR__ . '/funciones/cookies.php';
require_once __DIR__ . '/funciones/dao.php';

// Obtenemos todos los géneros para el formulario
//devuelve un array con los generos que hay en la base de datos, que usaremos en los checbox
$generos_disponibles = obtenerGeneros();
// Procesamos Cookies, devolviendo null (sin cookie), false (manipulada), array (preferencias)
$generos_pref_cookie = obtenerGenerosPreferidosDeCookies();
// Creamos un array asociativo donde la llave es el ID y el valor es el nombre del género
$mapa_nombres = array_column($generos_disponibles, 'nombre', 'id');

// Creamos una lista de nombres basada en los IDs guardados en la cookie
$nombres_encontrados = [];
if (is_array($generos_pref_cookie)) {
    foreach ($generos_pref_cookie as $id) {
        // Solo añadimos si el ID existe en nuestra base de datos
        if (isset($mapa_nombres[$id])) {
            $nombres_encontrados[] = $mapa_nombres[$id];
        }
    }
}
//Detectamos si ha habido manipulacion en la cookie
$manipulacion_detectada = ($generos_pref_cookie === false);
// Obtenemos películas filtradas si hay un array válido, filtramos. Si es null o false, mostramos todas.
$filtro_ids = (is_array($generos_pref_cookie)) ? $generos_pref_cookie : null;
//Devuelve el listado de peliculas, si hay filtro las del filtro seleccionado y sino todas
$peliculas = obtenerPeliculasPorGeneros($filtro_ids);
// Calculamos el número de votos por película, para ello primero creamos un array vacio
$votos_por_pelicula = [];
//obtenemos el numero total de votos para cada pelicula
foreach ($peliculas as $p) {
    $votos_por_pelicula[$p['id']] = obtenerNumeroVotosPorPelicula($p['id']);
}
// Calculamos la puntuación media por película, al igual que antes, creamos un array vacion
$puntuacion_por_pelicula = [];
// y recorremos toda las peliculas devolviendo el valor de la funcion
foreach ($peliculas as $p) {
    $puntuacion_por_pelicula[$p['id']] = obtenerPuntuacionMediaPorPelicula($p['id']);
}

// Función auxiliar para marcar checkbox, devuelve un array si hay preferencias. 
function estaMarcado($id, $preferencias) {
    return (is_array($preferencias) && in_array($id, $preferencias));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de películas</title>
    <link href="codeparts/estilo.css" rel="stylesheet" type="text/css"/>
</head>

<body>

<h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
<?php if (!empty($_SESSION['mensaje_error'])): ?>
        <div class="mensaje-error">
            <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
        </div>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>
<?php if (isset($_SESSION['user'])): ?>
    <p>
        Sesión iniciada. Último acceso:
        <?= date('d/m/Y H:i:s', $_SESSION['user']['ultimoacceso']) ?>
    </p>

    <div class="acciones">
        <a href="login/cerrarsesion.php">Cerrar sesión</a>
    </div>
<?php else: ?>
    <div class="acciones">
        <a href="login/form-login.php">Iniciar sesión</a>
    </div>
<?php endif; ?>

<h2>Resultado de procesar las cookies recibidas:</h2>

<?php if ($manipulacion_detectada): ?>
    <div class="mensaje-error">
        Se ha detectado una manipulación en las cookies. Se han eliminado por seguridad.
    </div>
<?php elseif (is_array($generos_pref_cookie)): ?>
    <div class="mensaje-exito">
        Preferencias detectadas: <?= implode(", ", $nombres_encontrados); ?>
    </div>
<?php else: ?>
    <p>No hay preferencias almacenadas.</p>
<?php endif; ?>

<h2>Formulario para seleccionar preferencias (géneros) a almacenar en cookies:</h2>

<form action="preferencias/preferencia_generos.php" method="POST">

    <?php foreach ($generos_disponibles as $g): ?>
        <div>
            <input
                type="checkbox"
                name="generos[]"
                value="<?= htmlspecialchars($g['id']) ?>"
                <?php
                    if (is_array($generos_pref_cookie) && in_array($g['id'], $generos_pref_cookie)) {
                        echo "checked";
                    }
                ?>
            >
            <?= htmlspecialchars($g['nombre']) ?>
        </div>
    <?php endforeach; ?>

    <div>
        <input type="checkbox" name="generos[]" value="99999">
        <strong>ERROR1</strong> (Género inexistente)
    </div>

    <div>
        <input type="checkbox" name="generos[]" value="TEST">
        <strong>ERROR2</strong> (Género no numérico)
    </div>

    <input type="submit" value="Seleccionar preferencias">
</form>

<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Género</th>
            <th>Dirección</th>
            <th>Duración</th>
            <th>Año</th>
            <th>Votos</th>
            <th>Puntuación</th>
            <th>Votar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($peliculas as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['titulo']) ?></td>
                <td><?= htmlspecialchars($p['nombre_genero']) ?></td>
                <td><?= htmlspecialchars($p['direccion']) ?></td>
                <td><?= $p['duracion'] ?></td>
                <td><?= $p['anio'] ?></td>
                <td>
                    <a href="privado/vervotaciones.php?id=<?= $p['id'] ?>">
                        <?= $votos_por_pelicula[$p['id']] ?>
                    </a>
                </td>
                <td>
                    <?= $puntuacion_por_pelicula[$p['id']] !== null
                        ? $puntuacion_por_pelicula[$p['id']]
                        : '—'
                    ?>
                </td>
                <td>
                    <form action="privado/form-nuevo-voto.php" method="post">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <input type="submit" value="Votar">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
