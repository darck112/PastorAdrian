<?php
/*
 * Procesa el POST de modificación:
 * - valida id y campos (como en insert)
 * - si hay errores, incluye form-modificar-pelicula.php pasándole $valores y $errores
 * - si todo OK, llama a actualizarPelicula() y muestra resultado
 */

require_once __DIR__ . '/../funciones/connect-db.php';
require_once __DIR__ . '/../funciones/dao-peliculas.php';

//llamamos a la funcion conectar a la base de datos
$pdo = conectarBD();
// si no conectara pintamos un mensaje de error y terminamos la ejecuccion para no continuar sin la base de datos
if (!$pdo) {
    die("<h2 style='color:red;'>❌ Error al conectar con la base de datos.</h2>");
}

/* Recogemos los datos y los preparamos
 * con trim eliminamos huecos antes y despues, el filtro FILTER_SANITIZE_STRING esta obosoleto
 * por ello utilizamos directamente el campo titulo desde $_POST sumado posteriormente a htmlspecialchar
 * si no existiera directamente se asigna una cadena vacia
 * el resto de datos se utiliza validate int, para comprobobar que efectivamente sea un numero.
 * si no lo es devolveria false
 */
// obtenemos el ID de la película enviado vía POST y validamos que sea un entero
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
//Si no existe o no es valido lanzamos error
if (!$id) {
    die("<h2 style='color:red;'>❌ ID de película no válido.</h2>");
}
/* Recogemos los datos y los preparamos
 * con trim eliminamos huecos antes y despues, el filtro FILTER_SANITIZE_STRING esta obosoleto, por ello para los string
 * utilizamos directamente el campo desde $_POST, para recoger directamente el dato en bruto, y puesto que si existe
 * retornara el campo como string y si no existe se le asigna una cadena vacia, por tanto trim es perfectamente valido
 * y no retornara null. Posteriormente se hacen las validacines oportunas para comprobarque cumple con los requisitos
 * y por ultimo, se le pasa por htmlspecialchart en la salida para evitar cualquier tipo de ataqaue malicioso. 
 * de esta forma evitamos el uso del trim con el sanitaze y el posible error de devolver null
 * el resto de datos se utiliza validate int, para comprobobar que efectivamente sea un numero.
 * si no lo es devolveria false
 */
$titulo = trim($_POST['titulo'] ?? '');
$genero = filter_input(INPUT_POST, 'genero', FILTER_VALIDATE_INT);
$direccion = trim($_POST['direccion'] ?? '');
$duracion = filter_input(INPUT_POST, 'duracion', FILTER_VALIDATE_INT);
$argumento = trim($_POST['argumento'] ?? '');
$anio = filter_input(INPUT_POST, 'anio', FILTER_VALIDATE_INT);

//Creamos el array vacion de errores para utilizarlo posteriormente
$errores = [];


//utilizamos mismos criterios de validacion que en insertar-peliculas.php para todos los campos
if (empty($titulo) || strlen($titulo) > 60) {
    $errores[] = "El título no puede estar vacío ni superar los 60 caracteres.";
}
$idsGeneros = array_column(obtenerGeneros($pdo), 'id'); 
if (!$genero || !in_array($genero, $idsGeneros)){//Verifica que $genero sea un valor válido. Si no lo es, añade error.
    $errores[] = "Debe seleccionar un género válido.";
}

if (empty($direccion) || strlen($direccion) > 100) {
    $errores[] = "La dirección no puede estar vacía ni superar los 100 caracteres.";
}
if (!$duracion || $duracion <= 0 || $duracion >= 500) {
    $errores[] = "La duración debe ser un número entre 1 y 499.";
}
if (empty($argumento) || strlen($argumento) > 255) {
    $errores[] = "El argumento no puede estar vacío ni superar los 255 caracteres.";
}
//Obtiene el año actual como entero, para validar que el año de la película esté dentro de un rango válido.
$anioActual = (int)date("Y");
if (!$anio || $anio <= 1960 || $anio > $anioActual) {
    $errores[] = "El año debe estar entre 1960 y $anioActual.";
}

//si hay errores añadimos aquellos valores que ya existan y devolvemos el formulario para poder corregir y mostrar lo incorrecto
if (!empty($errores)) {
    $valores = [
        'id' => $id,
        'titulo' => $titulo,
        'genero' => $genero,
        'direccion' => $direccion,
        'duracion' => $duracion,
        'argumento' => $argumento,
        'anio' => $anio
    ];
    include 'form-modificar-pelicula.php';
    exit();
}

// Con compact creamos un array asociativo con los datos de la pelicula que se van a actualizar
$datos = compact('titulo', 'genero', 'direccion', 'duracion', 'argumento', 'anio');
//llamamos a la funcion para actualizar la tabla
$resultado = actualizarPelicula($pdo, $id, $datos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado modificación película</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <H1>Autor/a: Adrian Pastor Orellana - Ejercicio 4 - Tarea 2 </H1>
    <hr>
    <?php if ($resultado): ?>
        <p class="exito">✅ Película modificada correctamente (<?= htmlspecialchars($resultado) ?> registro(s) actualizado(s)).</p>
    <?php else: ?>
        <p class="error">⚠ No se modificó ninguna película. Puede que los datos sean iguales o el ID no exista.</p>
    <?php endif; ?>
    <hr>
    <a href="../index.php">Volver al listado</a>
  </body>
</html>
