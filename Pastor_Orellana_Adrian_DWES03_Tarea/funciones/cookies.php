<?php
//incluimos el archivo correspondiente para el "salteado"
require_once __DIR__ . '/../conf/cookies-config.php';

// Constantes obligatorias según enunciado
const COOKIE_GENEROS = 'generos_604';//cookie con los generos
const COOKIE_HASH = 'hash_generos_604';//cookie de seguridad
const COOKIE_EXPIRE = 3600; // 1 hora
const COOKIE_PATH = '/';//disponible para todo el proyecto.

/**
 * Crea las cookies con los géneros serializados y su firma hash.
 * Recibe un array de generos valido y crea las dos cookies (seguridad y generos)
 */

function enviarCookiesGenerosPreferidos(array $generos): void {
    // Serializamos los datos del array recibido
    $datosSerializados = serialize($generos);
    // Creamos firma de seguridad: sha256(datos + SALT)
    // Es decir, concatemaos datos reales con un texto "secreto" y ademas usamos el hash sha256
    // De esta forma si alguien modifica la cookie el hash ya no coincide y se detecta manipulación.
    $hash = hash('sha256', $datosSerializados . COOKIE_SALT);
     //Enviámos ambas cookies y le añadimos el tiempo de expiración y la accesibilidad anteriormente definidas
    setcookie(COOKIE_GENEROS, $datosSerializados, time() + COOKIE_EXPIRE, COOKIE_PATH);
    setcookie(COOKIE_HASH, $hash, time() + COOKIE_EXPIRE, COOKIE_PATH);
}

/**
 * Borra las cookies cuando se desmarca todo o se detecta manipulacion. 
 */
function forzarEliminacionCookies(): void {
    // enviar una cookie con fecha pasada fuerza su borrado
    setcookie(COOKIE_GENEROS, '', time() - 3600, COOKIE_PATH);
    setcookie(COOKIE_HASH, '', time() - 3600, COOKIE_PATH);
    
    // Limpiamos también el array superglobal actual por si se consulta en el mismo script
    unset($_COOKIE[COOKIE_GENEROS]);
    unset($_COOKIE[COOKIE_HASH]);
}

/**
 * Verifica integridad y devuelve el array de géneros o false/null.
 * Retorna:
 * - null: No hay cookies.
 * - false: Manipulación detectada (hash incorrecto o error deserialización).
 * - array: Los géneros válidos.
 */
function obtenerGenerosPreferidosDeCookies() {
    // primero se verifica la  existencia, si no hay cookies es usuario nuevo, devolviendo null(sin preferencias)
    if (!isset($_COOKIE[COOKIE_GENEROS]) || !isset($_COOKIE[COOKIE_HASH])) {
        return null;
    }
//en caso de que si existan las guardamos en una variable
    $recibidoSerializado = $_COOKIE[COOKIE_GENEROS];
    $recibidoHash = $_COOKIE[COOKIE_HASH];

    // Se Recalcula el hash para verificar integridad
    $hashCalculado = hash('sha256', $recibidoSerializado . COOKIE_SALT);

    // hash_equals previene ataques de tiempo, si no son iguales, el recibido que el calculado indica manipulacion
    if (!hash_equals($hashCalculado, $recibidoHash)) {
        // La cookie fue modificada manualmente por el usuario
        forzarEliminacionCookies();
        return false; 
    }

    // Deserializamos, el @ suprime warnings si el string está corrupto
    $datos = @unserialize($recibidoSerializado);
    //si el array devuelve false se elimina todo
    if ($datos === false && $recibidoSerializado !== serialize(false)) {
        // Error en deserialización
        forzarEliminacionCookies();
        return false;
    }
    if (!is_array($datos)) {
        // Esperábamos un array, si no lo fuese, se elimina todo
        forzarEliminacionCookies();
        return false;
    }
// en caso contrario devolvemos los datos
    return $datos;
}
?>