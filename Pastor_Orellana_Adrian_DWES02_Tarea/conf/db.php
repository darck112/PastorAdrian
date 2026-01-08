<?php
/*
 * Script de configuración de la conexión a la base de datos usando PDO
 */

// Parámetros de conexión

define ('DB_HOSTNAME','localhost');
define ('DB_PORT',3306);
define ('DB_USER','root');
define ('DB_PASSWORD','');
define ('DB_SCHEMA','25_dwes02');

// Construcción del DSN (Data Source Name)
define('DB_DSN', 'mysql:host=' . DB_HOSTNAME . ';port=' . DB_PORT . ';dbname=' . DB_SCHEMA );