<?php
// funciones/dbconn.php
require_once __DIR__ . '/../conf/db-config.php';

/**
 * Crea y devuelve una conexión PDO a la base de datos.
 */
function Conectar(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
        } catch (PDOException $e) {
            // En producción no se debería mostrar el error directamente
            die("Error de conexión: " . $e->getMessage());
        }
    }
    return $pdo;
}
