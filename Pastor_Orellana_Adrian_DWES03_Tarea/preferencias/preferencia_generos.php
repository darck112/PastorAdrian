<?php
// Procesa el formulario de selección de géneros y crea/borra las cookies
// Inclimos los script que vamos a necesitar.
require_once __DIR__ . '/../funciones/cookies.php';
require_once __DIR__ . '/../funciones/dao.php';

// Inicializar variables para mostrar al usuario
$errores = [];//array para acumular errores y posteriormente mostrarlos
$accion = '';// se inicia vacia, guardara el valor de eliminadas o guardadas. 

// Comprobar método POST, para evitar acceso directo al script
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errores[] = 'Petición no válida: se esperaba POST.';// añadimos al array si se produce error.
} else {
    // Recogemos los datos de los checkboxes vienen como "generos[]", si no huibiera ninguno sería null.
    $generos_recibidos = $_POST['generos'] ?? null;

    // Si el usuario ha desmarcado todas las casillas recibimos NULL o array vacío.
    // en este caso se eliminan las cookies para mostrar todo el listado
    if ($generos_recibidos === null || (is_array($generos_recibidos) && count($generos_recibidos) === 0)) {
        // Forzar eliminación de cookies (usuario no quiere preferencias)
        forzarEliminacionCookies();
        $accion = 'eliminadas';
    } else {
        //Si exsisten valores, estos deben ser un array. Validar tipo.
        if (!is_array($generos_recibidos)) {
            $errores[] = 'Formato de datos incorrecto.';
        } else {
            // Comprobar que todos los elementos son numéricos (entero) y que existen en la BBDD.
            $tieneNoNumerico = false;
            foreach ($generos_recibidos as $valor) {
                // permitimos que el campo venga como string (p.e. "1"), pero rechazamos "abc"
                if (!is_numeric($valor)) {
                    $tieneNoNumerico = true;
                    break;
                }
            }
            if ($tieneNoNumerico) {
                $errores[] = 'Algunos valores no son numéricos. No se han guardado las preferencias.';
            } else {
                // Convertir a enteros y validar existencia en BBDD usando DAO
                $ids_posibles = array_map('intval', $generos_recibidos);
                $ids_existentes = validarIdsGenerosExistentes($ids_posibles); // devuelve sólo los ids que existen

                // Si hay algún id enviado que no existe, considerarlo error según enunciado
                // (el enunciado pide un checkbox de test para id no existente; si se marca, debe detectarse)
                // Comprobar si hay diferencias
                $no_existentes = array_diff($ids_posibles, $ids_existentes);
                if (!empty($no_existentes)) {
                    $errores[] = 'Hay géneros seleccionados que no existen en la base de datos: ' . implode(', ', $no_existentes) . '. No se han guardado las preferencias.';
                } else {
                    // Todo válido: enviar cookies
                    enviarCookiesGenerosPreferidos($ids_existentes);
                    $accion = 'guardadas';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resulado de guardar preferencias</title>
    <link href="../codeparts/estilo.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <h1>DWES 03. AUTOR: Adrián Pastor Orellana.</h1>
    <br>
    <h1>Preferencias de géneros</h1>

    <?php if (!empty($errores)): ?>
    <div class="mensaje-error">
        <h2>Se han detectado errores</h2>
        <ul>
            <?php foreach ($errores as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
        <p>No se han modificado las cookies de preferencias.</p>
    </div>
<?php else: ?>
    <div class="mensaje-exito">
        <?php if ($accion === 'eliminadas'): ?>
            <p>Se han eliminado las cookies de preferencias. Ahora se muestran todas las películas.</p>
        <?php elseif ($accion === 'guardadas'): ?>
            <p>Preferencias guardadas correctamente (cookie establecida durante 1 hora).</p>
        <?php else: ?>
            <p>Operación procesada correctamente.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="acciones">
<a href="../index.php" class="boton-enlace">Volver a la página principal</a>
 </div>
</body>
</html>