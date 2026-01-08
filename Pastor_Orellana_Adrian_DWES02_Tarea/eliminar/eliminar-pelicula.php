<?php
/*
 * Script: eliminar-pelicula.php
 * Procesa la petición de eliminación tras la confirmación.
 * Recibe por POST:
 *   - id (entero)
 *   - confirmar (checkbox, '1' si está marcado)
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
// obtenemos el ID de la película enviado vía POST y validamos que sea un entero
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
//Si no existe o no es valido lanzamos error
if (!$id) {
    die("<h2 style='color:red;'>❌ ID de película no válido.</h2><a href='../index.php'>Volver al listado</a>");
}
// Array para almacenar errores (si los hay) y variable para el resultado
$errores = [];
$resultadoEliminar = null;
// Comprobamos si el checkbox de confirmación fue marcado
$confirmar = filter_input(INPUT_POST, 'confirmar', FILTER_VALIDATE_INT); // devuelve 1 si marcado, false/null si no
// si no mostramos un mensaje de error 
 if (!$confirmar) {
        // No se marcó: añadimos un error y mostramos de nuevo el formulario
    $errores[] = "Debes marcar la casilla de confirmación para proceder con la eliminación.";

    // Recuperamos los datos de la película para volver a mostrar el formulario
    $pelicula = obtenerPeliculaPorId($pdo, $id);
    if ($pelicula) {
        $valores = $pelicula;
        include 'confirma-eliminar-pelicula.php';
        exit(); // detenemos ejecución
    } else {
        die("<h2 style='color:red;'>❌ La película no existe o el ID no es válido.</h2><a href='../index.php'>Volver al listado</a>");
    }
}

/*
 * Si las validaciones son correctas, se procede con la eliminación
 * Llamamos a la función eliminarPelicula
 */
$resultado = eliminarPelicula($pdo, $id);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Eliminar</title>    
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <H1>Autor/a: Adrian Pastor Orellana - Ejercicio 5 - Tarea 2 </H1>
    <hr>
    <h2>Resultado de la acción:</h2>

    <?php if ($resultado): // si se ha eliminado alguna película correctamente ?>
        <p class="exito">✅ Película eliminada correctamente. (<?= htmlspecialchars($resultado) ?> registro(s) eliminado(s))</p>
    <?php else: // si no se eliminó nada (id no encontrado o error en la operación) ?>
        <p class="error">❌ No se ha podido eliminar la película. Es posible que el registro no exista.</p>
    <?php endif; ?>

    <hr>
    <a href="../index.php">Volver al listado</a>
</body>
</html>

