<?php
// Incluir el archivo con la función cargarCSV
// Con si utilzamos include, si el archivo no esta se lanza una alerta y el script sigue
// Con require si el archivo no esta el script no continua
// con _once nos evitamos el problema de que pudieramos cargar el archivo mas de una vez (evitamos duplicidad)
require_once("../funciones/cargadatos.php");
require_once("../funciones/filtradatos.php");
/* Usa la variable $este_script para los enlaces a este mismo script
  Ejemplo: <a href="<?=$este_script?>">...</a>
 */
$este_script = $_SERVER['PHP_SELF'];

//cargamos el archivo csv en la variable datos. 
//si cargamos los datos directamente, cuando vayamos a realizar la tabla daría errores, pues la funcion
//devuelve claramente 3 parametros, por ello utilizaremos la funcion list para utilizar los mismos parametros 
//$datos= cargarCSV("../datos.csv");
list($filas_correctas, $filas_incorrectas, $datos) = cargarCSV("../datos.csv");
//con var_dump comprobamos los datos que se han cargado (para depurar)
//var_dump($datos);
// Pasamos a los apartados 4 y 5, primero se obtienen los años del conjunto de datos COMPLETO,
// antes de realizar cualquier filtrado. Esto asegura que los enlaces de filtro siempre estén todos disponibles.
$años_disponibles = []; //iniciamos un array vacio, que nos servirá para los años que esten disponibles
// si la variable $datos que recoge el CSV no esta vacia, unificamos todos los años de la columna año y los ordenamos
// almacenandolos dentro de un array años_disponibles. 
//array column coge la columna y array unique unifica los duplicados. 
if (!empty($datos)) {
    $años_disponibles = array_unique(array_column($datos, 'año'));
    sort($años_disponibles);
}
$mensaje_filtrado = ""; // Variable para mostrar un mensaje al usuario.
// Ahora pasaremos a comprobar si hemos pinchado en algun hipervinculo para filtrar. 
// con isset comprobamos que el parametro filtro esté recogido dentro de $_GET
if (isset($_GET['filtro'])) {
    $año_filtrar = (int) $_GET['filtro']; //guardamos como int, el año a filtrar en una variable
    // Se valida que el año del filtro sea uno de los años válidos.
    // Es decir, si año_filtra existe o coincide con el array años_disponibles.
    if (in_array($año_filtrar, $años_disponibles)) {

        // Si es válido, se llama a la función de filtrado.
        // La función modifica directamente la variable $datos al estar pasado por referencia
        $eliminados = filtraDatos($año_filtrar, $datos);
        //guardamos el mensaje a mostrar si hubiera exito
        $mensaje_filtrado = "<p><strong>Mostrando películas del año $año_filtrar. Se han descartado $eliminados registros.</strong></p>";
    } else {
        // y recogemos otro mensaje por si la logica anterior fallase. 
        $mensaje_filtrado = "<p><strong>El año '$año_filtrar' no es válido. Mostrando todos los registros.</strong></p>";
    }
}

// En esta parte se va a implementar parte del apartado 6 para la ordenacion de la tabla
// Almacenamos aquellos campos por los cuales posteriormente vamos a ordenar
$columnasOrdenables = ['título', 'género', 'año', 'dirección', 'duración'];
//si se ha pinchado en el enlace para ordenar y dicho enlace pertenece a las columnas ordenables
//se guardan los datos de la columna elegida y se define por defecto ascendente.
if (isset($_GET['orden']) && in_array($_GET['orden'], $columnasOrdenables, true)) {
    $columna = $_GET['orden'];
    $direccion = $_GET['dir'] ?? 'asc';
//usort va a reordenar el array &datos usando la funcion anonima la cual compara los elementos a y b
//utilizando columna y direccion
    usort($datos, function ($a, $b) use ($columna, $direccion) {

        //Si el valor es numérico se convierte a entero para comparar como número
        //y si no fuese numerico, se va a comparara como string en minusculas.
        //if ($valA == $valB) return 0, es decir, si son iguales —no importa tipo— no hay cambio de orden.
        //if ($direccion === 'desc') { return ($valA < $valB) ? 1 : -1; }
        //Si se pidiese desc se invierte la comparación: si valA es menor que valB devuelves 1 (porque en orden descendente
        // A debe ir después).
        //return ($valA < $valB) ? -1 : 1;
        //Caso asc por defecto: si valA < valB ponemos A antes.
        $valA = is_numeric($a[$columna]) ? (int) $a[$columna] : mb_strtolower($a[$columna]);
        $valB = is_numeric($b[$columna]) ? (int) $b[$columna] : mb_strtolower($b[$columna]);

        if ($valA == $valB)
            return 0;
        if ($direccion === 'desc') {
            return ($valA < $valB) ? 1 : -1;
        }
        return ($valA < $valB) ? -1 : 1;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tarea 1 - Ejercicio 2</title>
        <style>
            /*Añadimos un poco de estilo a la tabla*/
            body {
                font-family: sans-serif;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ccc;
                padding: 8px;
                text-align: left;
            }
            thead {
                background-color: coral;
            }
            tbody{
                background-color: antiquewhite;
            }
        </style>
    </head>
    <body>
        <H1>Autor/a: Adrian Pastor Orellana- Ejercicio 2 - Tarea 1 </H1>
        <h1>Listado de Películas</h1>
        <p>
            <a href="<?= $este_script ?>">Resetear Filtro</a> | 
            <a href="../ej3/index.php">Ir a formulario para insertar película</a> | 
            <a href="../index.php">Ir a la página principal</a>
        </p>
        <!-- Añadimos un mensaje con las filas correctas e incurrectas de cuando cargamos el csv -->
        <p>Se han cargado <strong><?= $filas_correctas ?></strong> registros válidos.</p>
        <p>Se han descartado <strong><?= $filas_incorrectas ?></strong> registros no válidos.</p>

        <div>
            <!-- Añadimos los link para cada año a filtrar -->
            <strong>Filtrar por año:</strong>
            <?php
            foreach ($años_disponibles as $año): //hacemos un bucle para recorrer cada año dentro del array
                // Con el cual construimosla URL para cada enlace de forma segura.
                //creando además el parametro filtro que nos que vamos a usar con $_get
                $url = $este_script . '?' . http_build_query(['filtro' => $año]);
                ?>
                <a href="<?= $url ?>"><?= $año ?></a> |
            <?php endforeach; ?>
        </div>
        <hr>

        <?= $mensaje_filtrado ?>

        <table>
            <thead>
                <tr>
                    <?php
                    // Encabezados con enlaces de ordenación ▲▼
                    $cabecera = ['título' => 'Título', 'género' => 'Género', 'año' => 'Año', 'dirección' => 'Dirección', 'duración' => 'Duración', 'argumento' => 'Argumento'];
                    //recorremos cada columna descartando argumento y generando los enlaces acendente y descendente
                    foreach ($cabecera as $campo => $nombre):
                        if ($campo !== 'argumento'): // argumento no lo ordenamos
                            //con array merge, podemos hacer respetar el filtro que hayamos seleccionado a la hora de ordenar. 
                            $ascUrl = $este_script . '?' . http_build_query(array_merge($_GET, ['orden' => $campo, 'dir' => 'asc']));
                            $descUrl = $este_script . '?' . http_build_query(array_merge($_GET, ['orden' => $campo, 'dir' => 'desc']));
                            ?>
                            <th>
                                <?= $nombre ?>
                                <a class="orden-link" href="<?= $ascUrl ?>">▲</a>
                                <a class="orden-link" href="<?= $descUrl ?>">▼</a>
                            </th>
                        <?php else: ?>
                            <th><?= $nombre ?></th>
                        <?php endif;
                    endforeach;
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos)): ?>
    <?php foreach ($datos as $pelicula): ?>
                        <tr>
                            <td><?= htmlspecialchars($pelicula['título']) ?></td>
                            <td><?= htmlspecialchars($pelicula['género']) ?></td>
                            <td><?= htmlspecialchars($pelicula['año']) ?></td>
                            <td><?= htmlspecialchars($pelicula['dirección']) ?></td>
                            <td><?= htmlspecialchars($pelicula['duración']) ?></td>
                            <td><?= htmlspecialchars($pelicula['argumento']) ?></td>
                        </tr>
                    <?php endforeach; ?>
<?php else: ?>
                    <tr>
                        <td colspan="6">No hay películas para mostrar.</td>
                    </tr>
<?php endif; ?>
            </tbody>
        </table>

    </body>
</html>