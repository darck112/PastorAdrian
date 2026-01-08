<?php
session_start();
require_once __DIR__ . '/accesoareaprivada.php';


 //Eliminamos la votación en curso, si existía. Esto
 // permite al usuario volver al listado sin que quede
 // información residual en la sesión.

unset($_SESSION['voto_en_curso']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100%, initial-scale=1.0">
      <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
    <title>Proceso de votación</title>
</head>
<body>
    <h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
    <h1>Proceso de votación</h1>

<p class="mensaje-exito">
    La votación en curso ha sido descartada correctamente.
</p>
<div class="acciones">
<a href="../index.php" class="boton-enlace">Volver a la página principal</a>
 </div>
</body>
</html>