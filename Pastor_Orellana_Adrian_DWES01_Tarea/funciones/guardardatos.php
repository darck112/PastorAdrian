<?php


function guardarCSV (string $archivo,array $peliculas): int
{    
    $archivo=fopen($archivo,'r+');
    $errors=0;
    array_walk($peliculas, fn($pelicula)=>$errors+=(fputcsv($archivo,$pelicula)===false?1:0));
    fclose($archivo);
    return $errors;
}