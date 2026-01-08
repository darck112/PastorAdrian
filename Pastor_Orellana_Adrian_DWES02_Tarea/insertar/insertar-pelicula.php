<?php
/*
 * Procesa el POST de inserción de película:
 * - valida los campos recibidos del formulario
 * - si hay errores, incluye form-insertar-pelicula.php pasándole $valores y $errores
 * - si todo OK, llama a insertarPelicula() y muestra el resultado
 */
//importamos los archivos necesarios para conectar con la base de datos y poder usar las funciones
require_once __DIR__ . '/../funciones/connect-db.php';
require_once __DIR__ . '/../funciones/dao-peliculas.php';

//llamamos a la funcion conectar a la base de datos
$pdo = conectarBD();
// si no conectara pintamos un mensaje de error y terminamos la ejecuccion para no continuar sin la base de datos
if (!$pdo) {
    die("<h2 style='color:red;'>❌ Error al conectar con la base de datos.</h2>");
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
/*Con compact crearemos un array  asociativo con los datos ingresados en el formulario
 * De esta forma, podremos usarlo posteriormente para que si hay errores tras las validaciones
 * no se borren los datos ya introducidos en el mismo y podamos corregir aquellos que sean erroneos
 */
$valores = compact('titulo', 'genero', 'direccion', 'duracion', 'argumento', 'anio');

//Se realizan las validaciones oportunas para que lancen errore si no se cumplen, segun el enunciado serían:
/*
 Título película. Restricciones:
 * No puede estar vacío.
 * No puede superar la longitud máxima del registro.
 */
if (empty($titulo) || strlen($titulo) > 60){
    $errores[] = "El título no puede estar vacío ni superar los 60 caracteres.";
}
/*
 * Género película. Restricciones:
 * Número entero mayor de cero y que corresponderá a uno de los géneros almacenados en la base de datos.
 * Debe introducirse a través de un <SELECT...> que debemos rellenar con los nombres de los géneros almacenados
 *  en nuestra base de datos pero donde los values serán el id de cada género en la base de datos.
 * Ten en cuenta que en la tabla de películas almacenaremos el identificador del género, no su nombre ni su
 *  descripción.
 * Para lo anterior se hace lo siguiente: se llama a obtenerGeneros($pdo), obtiene array de géneros, y extrae la
 * columna id a un array sencillo $idsGeneros. Se usa para validar que el género enviado existe realmente.
 
 */
$idsGeneros = array_column(obtenerGeneros($pdo), 'id'); 
if (!$genero || !in_array($genero, $idsGeneros)){//Verifica que $genero sea un valor válido. Si no lo es, añade error.
    $errores[] = "Debe seleccionar un género válido.";
}
/*
 * Año. Restricciones:
 * Número entero mayor de 1960 y menor o igual al año actual.
 * Debe introducirse a través de un <INPUT type="text">.
 */
$anioActual = date("Y");//Obtiene el año actual como entero, para validar que el año de la película esté dentro de un rango válido.
if (!$anio || $anio <= 1960 || $anio > $anioActual){//si el año actual no esta en el rango permitido, agregamos error
    $errores[] = "El año debe estar entre 1960 y $anioActual.";
}
/*
 * Dirección. Restricciones:
 * No puede estar vacío.
 * No puede superar la longitud máxima del registro.
  */
if (empty($direccion) || strlen($direccion) > 100){
    $errores[] = "La dirección no puede estar vacía ni superar los 100 caracteres.";
}
/*
 * Duración. Restricciones.
 * Número entero mayor de 0 y menor de 500.
 * Debe introducirse a través de un <INPUT type="text">.
 */
if (!$duracion || $duracion <= 0 || $duracion >= 500){
    $errores[] = "La duración debe ser un número entre 1 y 499.";
   }
   /*
    * Argumento. Restricciones.
    * No puede estar vacío.
    * No puede superar la longitud máxima del registro.
    */
if (empty($argumento) || strlen($argumento) > 255){
    $errores[] = "El argumento no puede estar vacío ni superar los 255 caracteres.";
}
// comprobamos si existen errores
 if (!empty($errores)) {
        // Si hay errores, incluimos el formulario para que se muestre de nuevo
        // Las variables $errores y $valores estarán disponibles en form-insertar-pelicula.php
        include 'form-insertar-pelicula.php';
        exit(); // Detenemos la ejecución del script para no mostrar la parte de "Éxito"
    }

// y si no hubiera erroes insertamos la pelicula en $datos
$datos = [
    'titulo' => $titulo,
    'genero' => $genero,
    'anio' => $anio,
    'direccion' => $direccion,
    'duracion' => $duracion,
    'argumento' => $argumento
];
//Si todo ha ido correto, llamamos a la funcion insertarPelicula, y le pasamos la conexion y los datos
 
$idInsertado = insertarPelicula($pdo, $datos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado inserción</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <H1>Autor/a: Adrian Pastor Orellana - Ejercicio 3 - Tarea 2</H1>
    <hr>
    <h2>Resultado de la acción:</h2>

    <?php if ($idInsertado)://si existe una pelicula correcta, lanzamos mensaje de exito ?>
        <p class="exito">✅ La película <strong><?= htmlspecialchars($titulo) ?></strong> se ha insertado correctamente.</p>
        <p>ID generado: <strong><?= $idInsertado ?></strong></p>
     <?php if (!$idInsertado): //si hubiera errores de conexion a la base de datos lo lanzamos?>
    <p class="error">❌ Error al insertar la película. Inténtalo de nuevo.</p>
<?php endif; ?>   
    <?php endif; ?>

    <hr>
    <a href="../index.php">Volver a la página principal</a>
    <a href="../insertar/form-insertar-pelicula.php">Añadir otra pélicula</a>
</body>
</html>