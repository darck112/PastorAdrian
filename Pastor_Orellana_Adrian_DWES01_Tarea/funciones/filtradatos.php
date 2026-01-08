<?php
/**
 * filtraDatos
 * Funcion que filtra las peliculas por un año concreto.
 * @param int   $año        Año por el que se filtrarán las películas.
 * @param array &$peliculas Array de películas (por referencia). Se modificará
 * para dejar solo las que coincidan con el año.
 * @return int  Número de películas eliminadas (no coinciden con el año).
 */
//el & dentro de $peliculas indica que el parametro es pasado por referencia,
//es decir, se modifica el array original directamente
function filtraDatos(int $año, array &$peliculas): int
{
    // Contamos cuántas películas hay antes del filtrado y lo guardamos en una variable
    $totalAntes = count($peliculas);
    // Creamos un nuevo array con solo las pelis del año indicado
    $filtradas = [];//creamos e iniciamos el array vacio
    //posteriormente con el buble foreach, vamos a ir rellanando el array
    foreach ($peliculas as $peli) {
        // si el año de la pelicula coincide con el año seleccionado, lo añadimos al array
        // tambien comvertimos a int por seguridad antes de comparar
        if ((int)$peli['año'] === $año) {
            $filtradas[] = $peli;
        }
    }
    // Calculamos cuántas se eliminaron mediante direfencia entre el total que habiamos calculado
    //y las que hemos filtrado
    $eliminados = $totalAntes - count($filtradas);

    // Reemplazamos el array original con las filtradas, y como el array se paso por referencia, 
    // el cambio se verá reflejado directamente en index.php
    $peliculas = $filtradas;

    // Devolvemos el número de eliminados para que podamos recoger cuantas fueran eliminadas
    return $eliminados;
}