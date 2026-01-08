<?php

define('CABECERAS', ['título',
    'género',
    'año',
    'dirección',
    'duración',
    'argumento']);

define('GENEROS', ['animación', 'drama', 'ciencia ficción/fantasía', 'comedia']);
/*
 * funcion cargarCSV original, a continuacion se incorporara la modificacion para el apartado 3
  function cargarCSV (string $archivo): array
  {
  $datos=[];
  $archivo=fopen($archivo,'r');
  $filas_correctas=0;
  $filas_incorrectas=0;
  while (($fila=fgetcsv($archivo))!==false)
  {
  $datos[]=array_combine(CABECERAS,$fila);
  }
  return [$filas_correctas,$filas_incorrectas,$datos];
  }
 */

/**
 * Carga y valida los datos de un archivo CSV de películas.
 * @param string $archivo ruta al archivo CSV.
 * @return array devuelve un array con [filas_correctas, filas_incorrectas, datos].
 */
function cargarCSV(string $archivo): array {
    $datos = [];
    $filas_correctas = 0;
    $filas_incorrectas = 0;
    // Intentamos abrir el archivo CSV en modo lectura, de manera que si se abre se continue la ejecución
    // y si hubiera algun problema, se detuviera.
    if (($fh = fopen($archivo, 'r')) !== false) {
        // Leemos el archivo línea a línea
        while (($fila = fgetcsv($fh)) !== false) {
            // Limpiamos espacios en blanco alrededor de cada campo con el uso de trim para cada fila
            $filas = array_map('trim', $fila);
            // Cada fila corresponde con los datos de una película y debe haber exactamente 6 datos separados por comas.
            // es decir que cada fila debe tener exactamente el mismo nº de columnas que la constante CABECERAS
            //para ello usamos count, si no hay exactamente el mismo numero de columnas, aumentamos el contador de filas incorrectas
            if (count($filas) !== count(CABECERAS)) {
                $filas_incorrectas++;
                continue;
            }
            // // saltamos a la siguiente fila
            //La columna número 1 (empezamos a numerar por el cero), que corresponde con el género de la película,
            // debe ser uno de los valores establecidos en la constante GENEROS definida en el archivo dwes01/funciones/cargadatos.php.
            // Para ello usamos la funcion in_array, para comprobar que el genero este dentro de GENEROS, si no es true, aumentamos filas incorrectas
            if (!in_array($filas[1], GENEROS, true)) {
                $filas_incorrectas++;
                continue;
            }
            // La columna número 2, que corresponde con año, debe ser un dato numérico.
            // Para esta ocasion, el proceso es parecido al anterior pero con la funcion is_numeric
            if (filter_var($filas[2], FILTER_VALIDATE_INT) === false) {
                $filas_incorrectas++;
                continue;
            }
            // La columna número 4, que corresponde con duración, debe ser un número entero.
            // El valor de la columna 4 debe ser un entero válido
            if (filter_var($filas[4], FILTER_VALIDATE_INT) === false) {
                $filas_incorrectas++;
                continue;
            }
            //si todo lo anterior no es incorrecto, procedemos a crear el array asociativo
            //usando las CABECERAS como claves
            $datos[] = array_combine(CABECERAS, $filas);
            // Ý aumentamos el contador de correctas
            $filas_correctas++;
        }
        // Cerramos el archivo después de leerlo todo
        fclose($fh);
    } else {
        // Importante: Si fopen falla, el script original no devolvía nada, causando un error.
        // asi que con esto nos aseguramos de que siempre devuelva un array con valores por defecto.
        return [0, 0, []];
    }
    // Devolvemos los tres valores: correctas, incorrectas y datos válidos
    return [$filas_correctas, $filas_incorrectas, $datos];
}
