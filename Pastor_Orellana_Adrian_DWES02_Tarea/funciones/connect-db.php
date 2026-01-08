<?php
/*
 * Author: Adrían Pastor Orellana
 * Función para conectar con la base de datos usando PDO
 * Importamos el archivo de configuración para usar las constantes de conexión
 * Usamos __DIR__ para obtener el directorio actual de conexion.php y acceder correctamente a conf.php
 */
require_once __DIR__ . '/../conf/db.php';

function conectarBD() {
    try {
           $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD,
           array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//hacemos que cualquier error lance una excepcion
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC)//configuramos que los resultados de las consultas devuelvan arrays asociativos
            );
        return $pdo; //retorna la conexion
    } catch (PDOException $ex) {
        // Si hay un error al conectar, lo registramos o mostramos un mensaje
        error_log("Error al conectar con la base de datos: " . $ex->getMessage());
        return false; // retorna false si no fue posible conectar
    }
}