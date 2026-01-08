<?php
/*
 * Muestra el formulario de inserción de película:
 * - si hay errores previos, los muestra junto con los datos introducidos
 * - rellena el select de géneros desde la base de datos
 * - envía los datos a insertar-pelicula.php para su validación e inserción
 */
// importamos los archivos necesarios para conectar con la base de datos y poder usar las funciones
require_once __DIR__ . '/../funciones/connect-db.php';
require_once __DIR__ . '/../funciones/dao-peliculas.php';

// llamamos a la funcion conectar a la base de datos
$pdo = conectarBD();

// si no conectara pintamos un mensaje de error y terminamos la ejecuccion para no continuar sin la base de datos
if (!$pdo) {
    die("<h2 style='color:red;'>❌ Error al conectar con la base de datos.</h2>");
}

// recuperamos el array con los generos
$generos = obtenerGeneros($pdo);

// Si las variables $errores y $valores no existen, las inicializamos como arrays vacíos.
// Esto ocurre cuando el usuario visita la página por primera vez.
// Si esta página es incluida desde insertar-pelicula.php, estas variables ya existirán.
$errores = $errores ?? [];
$valores = $valores ?? [];
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Insertar nueva película</title>
        <link rel="stylesheet" href="../index.css">
    </head>
    <body>
        <h1>Autor/a: Adrian Pastor Orellana - Ejercicio 3 - Tarea 2</h1>
        <h2>Formulario para insertar nueva película</h2>
        <a href="../index.php">⬅ Ir a listado de películas</a>
        <hr>

        <?php if (!empty($errores)): // si hay errores los pintamos en pantalla ?>
            <div class="error-box">
                <h3>️Se han encontrado errores:</h3>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="insertar-pelicula.php" method="post" class="form">
            <label>
                Título:
                <input type="text" name="titulo" maxlength="60"
                       value="<?= htmlspecialchars($valores['titulo'] ?? '') ?>" required>
            </label><br>

            <label>
                Género:
                <select name="genero" required>
                    <option value="">-- Selecciona un género --</option>
                    <?php foreach ($generos as $g): ?>
                        <option 
                            value="<?= $g['id'] ?>" <?= (isset($valores['genero']) && $valores['genero'] == $g['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="9999999">GÉNERO NO EXISTENTE (TEST)</option>
                </select>
            </label><br>

            <label>
                Dirección:
                <input type="text" name="direccion" maxlength="100"
                       value="<?= htmlspecialchars($valores['direccion'] ?? '') ?>" required>
            </label><br>

            <label>
                Duración (minutos):
                <input type="text" name="duracion" maxlength="3" 
                       value="<?= htmlspecialchars($valores['duracion'] ?? '') ?>" required>
            </label><br>

            <label>
                Argumento:
                <input type="text" name="argumento" maxlength="255" 
                       value="<?= htmlspecialchars($valores['argumento'] ?? '') ?>" required>
            </label><br>

            <label>
                Año:
                <input type="text" name="anio" maxlength="4" 
                       value="<?= htmlspecialchars($valores['anio'] ?? '') ?>" required>
            </label><br>

            <input type="submit" value="Añadir nueva película">
        </form>
    </body>
</html>
