<?php
/*
 * Muestra el formulario de modificación de película:
 * - obtiene la película por su ID recibido vía POST
 * - si el ID o la película no son válidos, muestra un error
 * - recupera los géneros para rellenar el select
 * - si hay errores previos, los muestra junto con los datos introducidos
 * - envía los datos a modificar-pelicula.php para su validación y actualización
 */
require_once __DIR__ . '/../funciones/connect-db.php';
require_once __DIR__ . '/../funciones/dao-peliculas.php';

//llamamos a la funcion conectar a la base de datos
$pdo = conectarBD();
// si no conectara pintamos un mensaje de error y terminamos la ejecuccion para no continuar sin la base de datos
if (!$pdo) {
    die("<h2 style='color:red;'>❌ Error al conectar con la base de datos.</h2>");
}
// obtenemos el ID de la película enviado vía POST y validamos que sea un entero
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
//Si no existe o no es valido lanzamos error
if (!$id) {
    die("<h2 style='color:red;'>❌ ID de película no válido.</h2><a href='../index.php'>Volver al listado</a>");
}
// llamamos a la funcion para obtener las peliculas por el id que devuelve un array con los datos de la pelicula
$pelicula = obtenerPeliculaPorId($pdo, $id);
//si no existe devolveria false y lanzariamos un mensaje de error
if (!$pelicula) {
    die("<h2 style='color:red;'>❌ La película no existe o el ID no es válido.</h2><a href='../index.php'>Volver al listado</a>");
}
//vamos a obtener de nuevo todos los generos para utilizarlos mas adelante en el formulario
$generos = obtenerGeneros($pdo);
// inicializamos arrays de errores y valores
$errores = $errores ?? [];
$valores = $valores ?? $pelicula; // si venimos de modificar-pelicula.php ya estarán 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar película</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <h1>Autor/a: Adrian Pastor Orellana - Ejercicio 4 - Tarea 2</h1>
    <h2>Modificar película: <?= htmlspecialchars($pelicula['titulo']) ?></h2>
    <a href="../index.php">⬅ Ir al listado |</a>
    <a href="./insertar/form-insertar-pelicula.php"> Ir a formulario para insertar película </a>
    <hr>

    <?php if (!empty($errores)): ?>
        <div class="error-box">
            <h3>⚠ Se han encontrado errores:</h3>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="modificar-pelicula.php" method="post" class="form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($pelicula['id']) ?>">

        <label>Título:
            <input type="text" name="titulo" maxlength="60"
                   value="<?= htmlspecialchars($valores['titulo'] ?? '') ?>" required>
        </label><br>

        <label>Género:
            <select name="genero" required>
                <option value="">-- Selecciona un género --</option>
                <?php foreach ($generos as $g): ?>
                    <option value="<?= $g['id'] ?>"
                        <?= (isset($valores['genero']) && $valores['genero'] == $g['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Dirección:
            <input type="text" name="direccion" maxlength="100"
                   value="<?= htmlspecialchars($valores['direccion'] ?? '') ?>" required>
        </label><br>

        <label>Duración (minutos):
            <input type="text" name="duracion" maxlength="3"
                   value="<?= htmlspecialchars($valores['duracion'] ?? '') ?>" required>
        </label><br>

        <label>Argumento:
            <input type="text" name="argumento" maxlength="255"
                   value="<?= htmlspecialchars($valores['argumento'] ?? '') ?>" required>
        </label><br>

        <label>Año:
            <input type="text" name="anio" maxlength="4"
                   value="<?= htmlspecialchars($valores['anio'] ?? '') ?>" required>
        </label><br>

        <input type="submit" value="Guardar cambios">
    </form>
</body>
</html>
