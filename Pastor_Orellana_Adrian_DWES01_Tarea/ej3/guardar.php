<?php
require_once('../funciones/cargadatos.php');
require_once('../funciones/guardardatos.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si se intenta acceder directamente al procesador sin enviar formulario
    header('Location: index.php');
    exit;
}
//vamos a crear un array para almacenar los errores que puedan ocurrir. 
$errores = [];
$datos = []; // Guardará los datos que sí son correctos para repintarlos
// Recogemos los datos del formulario de forma segura, para ello, primero comprobamos que exista cada campo
//con isset, el cual si existe, lo guardamos borrando los espcacios con trim, y sino asignamos cadena vacia
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$genero = isset($_POST['genero']) ? $_POST['genero'] : '';
$año = isset($_POST['año']) ? $_POST['año'] : '';
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
$duracion = isset($_POST['duracion']) ? $_POST['duracion'] : '';
$argumento = isset($_POST['argumento']) ? trim($_POST['argumento']) : '';

// --- Validaciones --- //
// si titulo estuviera vacio, añadimos un error
if ($titulo === '') {
    $errores[] = "El título no puede estar vacío.";
} else {
    $datos['titulo'] = $titulo;
}

// si el genero no esta el el array generos añadimos el error
if (!in_array($genero, GENEROS, true)) {
    $errores[] = "El género seleccionado no es válido.";
} else {
    $datos['genero'] = $genero;
}

// date('Y') devuelve el año actual, y comprobamos que el año sea un numero mayor de 1960 e inferior a año_actual
$año_actual = (int) date('Y');
if (!is_numeric($año) || (int) $año < 1960 || (int) $año > $año_actual) {
    $errores[] = "El año debe estar entre 1960 y $año_actual.";
} else {
    $datos['año'] = $año;
}
// al igual que titulo la direccion no puede estar vacia
    if ($direccion === '') {
        $errores[] = "La dirección no puede estar vacía.";
    } else {
        $datos['direccion'] = $direccion;
    }

// Duración
    $durInt = (int) $duracion; // conversión a entero explícita
    if (filter_var($durInt, FILTER_VALIDATE_INT) === false || (int) $durInt < 1 || (int) $durInt > 500) {
        $errores[] = "La duración debe ser un número entero entre 1 y 500.";
    } else {
        $datos['duracion'] = $duracion;
    }

// igual que casos anteriores, que no este vacia
    if ($argumento === '') {
        $errores[] = "El argumento no puede estar vacío.";
    } else {
        $datos['argumento'] = $argumento;
    }

//  Comprobar si la película ya existe (solo si no hay otros errores de validación)
    $rutaCSV = '../datos.csv';
    if (empty($errores)) {
    //cargamos solo los datos que nos interesan con list
        list(,, $peliculas_existentes) = cargarCSV($rutaCSV);
        foreach ($peliculas_existentes as $pelicula) {
            // Comparamos en minúsculas para evitar errores
            if (
                    strtolower($pelicula['título']) === strtolower($titulo) &&
                    (int) $pelicula['año'] === (int) $año &&
                    strtolower($pelicula['dirección']) === strtolower($direccion)
            ) {
                $errores['existente'] = "Ya existe una película con el mismo título, año y director/a.";
                break;
            }
        }
    }

// Decidir qué hacer: guardar o volver al formulario
    if (!empty($errores)) {
        // Si hay errores, incluimos el formulario para que se muestre de nuevo
        // Las variables $errores y $datos estarán disponibles en index.php
        include 'index.php';
        exit(); // Detenemos la ejecución del script para no mostrar la parte de "Éxito"
    }

// --- Si todo fue correcto, procedemos a guardar ---
    $nueva_pelicula = [$titulo, $genero, $año, $direccion, $duracion, $argumento];

// La función guardarCSV espera un array de películas
    $resultado = guardarCSV($rutaCSV, [$nueva_pelicula]);
//al devolver errores la funcion guardarCSV, si es  concluimos con que esta todo ok y lanzamos el mensaje de exito y un html
    if ($resultado === 0) {
        $mensaje_exito = "¡Película guardada correctamente!";
    } else {
        $mensaje_error = "Hubo un error al guardar la película en el archivo CSV.";
    }
?>
 <!DOCTYPE html>
 <html lang ="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultado del Guardado</title>
    </head>
    <body>
        <h1>Resultado del guardado</h1>

        <?php if (isset($mensaje_exito)): ?>
            <h2><?= htmlspecialchars($mensaje_exito) ?></h2>
            <p>Los datos guardados son:</p>
            <ul>
                <li><strong>Título:</strong> <?= htmlspecialchars($titulo) ?></li>
                <li><strong>Género:</strong> <?= htmlspecialchars($genero) ?></li>
                <li><strong>Año:</strong> <?= htmlspecialchars($año) ?></li>
                <li><strong>Dirección:</strong> <?= htmlspecialchars($direccion) ?></li>
                <li><strong>Duración:</strong> <?= htmlspecialchars($duracion) ?></li>
                <li><strong>Argumento:</strong> <?= htmlspecialchars($argumento) ?></li>
            </ul>
        <?php elseif (isset($mensaje_error)): ?>
            <h2 class="error"><?= htmlspecialchars($mensaje_error) ?></h2>
        <?php endif; ?>

        <p>
            <a href="index.php">Añadir otra película</a> | 
            <a href="../ej2/index.php">Ver el listado de películas</a> |
            <a href="../index.php">Volver a la página principal</a>
        </p>
    </body>
 </html>