<?php
/*
 * Script: confirma-eliminar-pelicula.php
 * Muestra los datos de la película (solo lectura) y pide confirmación para eliminarla.
 * Recibe por POST: id (entero)
 *
 * Autor: Adrian Pastor Orellana
 */

require_once __DIR__ . '/../funciones/connect-db.php';
require_once __DIR__ . '/../funciones/dao-peliculas.php';

// llamamos a la funcion conectar a la base de datos
$pdo = conectarBD();

// si no conectara pintamos un mensaje de error y terminamos la ejecuccion para no continuar sin la base de datos
if (!$pdo) {
    die("<h2 style='color:red;'>❌ Error al conectar con la base de datos.</h2>");
}

// Recogemos y validamos el id recibido por POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Si no es un entero válido, mostramos mensaje y terminamos
if (!$id) {
    die("<p class='error'>❌ ID de película no válido.</p><a href='../index.php'>Volver al listado</a>");
}
// llamamos a la funcion para obtener las peliculas por el id que devuelve un array con los datos de la pelicula
$pelicula = obtenerPeliculaPorId($pdo, $id);
//si no existe devolveria false y lanzariamos un mensaje de error
if (!$pelicula) {
    die("<h2 style='color:red;'>❌ La película no existe o el ID no es válido.</h2><a href='../index.php'>Volver al listado</a>");
}
// inicializamos arrays de errores y valores
$errores = $errores ?? [];
$valores = $valores ?? $pelicula; // si venimos desde eliminar-pelicula.php ya estarán
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar una película</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <H1>Autor/a: Adrian Pastor Orellana - Ejercicio 5 - Tarea 2 </H1>
    <h1>Formulario para eliminar una película </h1>
   <a href="../index.php">Ir a la página principal  | </a>
   <a href="./insertar/form-insertar-pelicula.php"> Ir a formulario para insertar película | </a>
   
    <?php if (!empty($errores)): ?>
        <div class="error-box">
            <h3>Se han encontrado errores:</h3>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <!-- Formulario de confirmación: inputs en readonly para mostrar la info -->
    <form action="eliminar-pelicula.php" method="post" class="form">
         <input type="hidden" name="id" value="<?= htmlspecialchars($valores['id']) ?>">

        <label>Título:
            <input type="text" value="<?= htmlspecialchars($pelicula['titulo']) ?>" readonly>
        </label><br>

        <label>Género:
            <input type="text" value="<?= htmlspecialchars($pelicula['genero'] ?? 'Sin género') ?>" readonly>
        </label><br>

        <label>Dirección:
            <input type="text" value="<?= htmlspecialchars($pelicula['direccion']) ?>" readonly>
        </label><br>

        <label>Año:
            <input type="text" value="<?= htmlspecialchars($pelicula['anio']) ?>" readonly>
        </label><br>

        <!-- Checkbox de confirmación obligatorio -->
        <label>
            <input type="checkbox" name="confirmar" value="1"> He leído y confirmo la eliminación de esta película.
        </label><br><br>

        <!-- Enviamos el id como hidden para que eliminar-pelicula.php lo procese -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($pelicula['id']) ?>">
        <input type="submit" value="Confirmar eliminación" class="btn-eliminar">
    </form>
</body>
</html>
    