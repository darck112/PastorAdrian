<?php
require_once('../funciones/cargadatos.php');

// Si las variables $errores y $datos no existen, las inicializamos como arrays vacíos.
// Esto ocurre cuando el usuario visita la página por primera vez.
// Si esta página es incluida desde guardar.php, estas variables ya existirán.
$errores = $errores ?? [];
$datos = $datos ?? [];

// Guardamos todos los géneros en una variable
$generos = GENEROS;
// Seleccionamos un indice aleatorio dentro del array generos
$indiceAleatorio = array_rand($generos);
//Luego utilizamos ese indice para guardar el genero y usarlo en el formulario
$generoAleatorio = $generos[$indiceAleatorio];

// Calculamos el año actual usando la función date()para usarla en el formulario
$año_actual = (int) date('Y');
//calculamos un año al azar entre 1960 y el año actual
$año_azar = rand(1960, $año_actual);
?>



<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Insertar nueva película</title>
    </head>
    <body>
        <H1>Autor/a: Adrian Pastor Orellana - Ejercicio 3 - Tarea 1 </H1>
        <h1>Formulario para insertar nueva película </h1>
        <a href="../ej2/index.php">Ir a listado de películas</a> | <a href="../index.php">Ir a la página principal</a><br><br>

     <?php if (!empty($errores)): //si el array errores no esta vacio, mostraremos todos los errores en pantalla?>
    <div style="color:red;">
    <p><strong>Por favor, corrige los siguientes errores:</strong></p>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
        <form action="guardar.php" method="POST"> <!<!-- indicamos el destino del formulario en guardar.php -->
            <!<!-- añadimos los nombres dentro de la etiqueta label con for= y el nombre y dentro de input con name sin 
            cambiar el type-->
            
            <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($datos['titulo'] ?? '') ?>"><br>
            <label for="genero">Género:</label>
            <select id="genero" name="genero">
                <option value="">-- Selecciona un género --</option>
                <!-- añadimos un bucle con los generos y lo imprimimos en pantalla -->
                <?php foreach ($generos as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>" <?= (isset($datos['genero']) && $datos['genero'] == $g) ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ucfirst($g)) ?>
                </option>
            <?php endforeach; ?>
                <!-- Segunda opción, utilizamos las variables declaradas al inicio del archivo
                para darle un valor, en pantalla solo veremos genero al azar, pero al seleccionarlo cogerá 
                uno de la lista aleatoriamente-->
                <option value="<?= htmlspecialchars($generoAleatorio) ?>"> Genero al azar  </option>
            </select><br>

           <label for="año">Año:</label>
        <select name="año" id="año">
            <option value="">-- Selecciona un año --</option>
            <?php for ($y = 1960; $y <= $año_actual; $y++): ?>
                <option value="<?= $y ?>" <?= (isset($datos['año']) && $datos['año'] == $y) ? 'selected' : '' ?>>
                    <?= $y ?>
                </option>
            <?php endfor; ?>
             <option value="<?= $año_azar ?>">Valor al azar</option>
        </select><br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>"><br>

        <label for="duracion">Duración (minutos):</label>
        <input type="text" name="duracion" id="duracion" value="<?= htmlspecialchars($datos['duracion'] ?? '') ?>"><br>

        <label for="argumento">Argumento:</label><br>
        <textarea id="argumento" name="argumento" rows="4" cols="50"><?= htmlspecialchars($datos['argumento'] ?? '') ?></textarea><br>

        <input type="submit" value="Guardar película">
    </form>
</body>
</html>
