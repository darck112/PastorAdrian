<?php
/*
 * Autor: Adrián Pastor Orellana
 * Ejercicio 2 - Tarea 2
 */
require_once __DIR__ . '/funciones/connect-db.php';
require_once __DIR__ . '/funciones/dao-peliculas.php';
//llamamos a la funcion conectar a la base de datos
$pdo = conectarBD();
// si no conectara pintamos un mensaje de error y terminamos la ejecuccion para no continuar sin la base de datos
if (!$pdo) {
    die("<h2 style='color:red;'>❌ Error al conectar con la base de datos.</h2>");
}
// Procesar filtro por año 
$anio = filter_input(INPUT_GET, 'anio', FILTER_VALIDATE_INT);

// Obtener datos
$peliculas = obtenerPeliculas($pdo, $anio);
$generos = obtenerGeneros($pdo);

//Obtener lista de años disponibles (para los enlaces de filtrado)
$aniosDisponibles = [];
if (is_array($peliculas) && !empty($peliculas)) {
    // Cargamos los años de todas las películas (sin filtrar)
    $todasPeliculas = obtenerPeliculas($pdo, null);
    $aniosDisponibles = array_unique(array_column($todasPeliculas, 'anio'));
    rsort($aniosDisponibles); // orden descendente
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tarea 2 - Ejercicio 3</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <H1>Autor/a: Adrian Pastor Orellana - Ejercicio 2 - Tarea 2 </H1>
        <HR>
        <a href="<?= $_SERVER['PHP_SELF'] ?>"> Resetear  | </a>
        <a href="ejercicio1.html">Ir a respuestas ejercicio 1  | </a>
        <a href="./insertar/form-insertar-pelicula.php"> Ir a formulario para insertar película | </a>
         <HR>

            <!-- filtro por años -->
            <H1>Haz clic para filtrar por año:</H1>
            <div>
                <?php
                if (!empty($aniosDisponibles)) {
                    foreach ($aniosDisponibles as $a) {
                        $link = htmlspecialchars($_SERVER['PHP_SELF']) . "?anio=$a";
                        $estilo = ($anio == $a) ? "style='font-weight:bold; color:blue;'" : "";
                        echo "<a href='$link' $estilo>$a</a> | ";
                    }
                    echo "<a href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>Todos</a>";
                } else {
                    echo "<p>No hay años disponibles para filtrar.</p>";
                }
                ?>
            </div>

            <!-- Tabla de películas -->
            <?php
            if ($peliculas === false) {
                echo "<p style='color:red;'>❌ Error al recuperar las películas.</p>";
            } elseif (empty($peliculas)) {
                echo "<p>No hay películas registradas para el año indicado.</p>";
            } else {
                include __DIR__ . '/recuperar/cargar-peliculas.php';
            }
            ?>

    </body>
</html>

 