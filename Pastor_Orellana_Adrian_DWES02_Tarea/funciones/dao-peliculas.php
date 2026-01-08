<?php
// Author: Adrián Pastor Orellana
// DAO - funciones para gestionar películas y géneros (PDO)

/**
 * Obtiene una lista de películas. Si se especifica un año, se filtra por ese año.
 *
 * @param PDO $pdo Conexión PDO activa.
 * @param int|null $anio Año por el que filtrar (opcional).
 * @return array|false Array de películas o false en caso de error.
 */
function obtenerPeliculas(PDO $pdo, $anio = null) {
    try {
        if ($anio !== null && (!is_numeric($anio) || $anio < 1900 || $anio > intval(date('Y')) + 1)) {
            return [];
        }//usa COALESCE para que, si no existe género relacionado (g.nombre es NULL), devuelva la cadena 'Sin género' como valor de la columna genero.
        $sql = "SELECT p.id, p.titulo, COALESCE(g.nombre, 'Sin género') AS genero, p.direccion, p.duracion, p.argumento, p.anio
                FROM peliculas p
                LEFT JOIN generos g ON p.genero = g.id";//LEFT JOIN para incluir películas que no tengan género

        if ($anio !== null) {
            $sql .= " WHERE p.anio = :anio";
        }

        $sql .= " ORDER BY p.anio DESC, p.titulo ASC";

        $stmt = $pdo->prepare($sql);

        if ($anio !== null) {
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $ex) {
        error_log("Error al obtener películas: " . $ex->getMessage());
        return false;
    }
}

/**
 * Obtiene la lista de géneros.
 *
 * @param PDO $pdo Conexión PDO activa.
 * @return array|false Array de géneros o false en caso de error.
 */
function obtenerGeneros(PDO $pdo) {
    try {
        $sql = "SELECT id, nombre, descripcion FROM generos ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        error_log("Error al obtener géneros: " . $ex->getMessage());
        return false;
    }
}

/**
 * Inserta una nueva película en la base de datos.
 *
 * @param PDO $pdo Conexión PDO activa.
 * @param array $datos Array con claves: titulo,genero,anio,direccion,duracion,argumento
 * @return int|false ID insertado o false si falla.
 */
function insertarPelicula(PDO $pdo, array $datos) {
    try {
        $sql = "INSERT INTO peliculas (titulo, genero, anio, direccion, duracion, argumento)
                VALUES (:titulo, :genero, :anio, :direccion, :duracion, :argumento)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':genero', $datos['genero'], PDO::PARAM_INT);
        $stmt->bindParam(':anio', $datos['anio'], PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(':duracion', $datos['duracion'], PDO::PARAM_INT);
        $stmt->bindParam(':argumento', $datos['argumento'], PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return (int)$pdo->lastInsertId();
        } else {
            return false;
        }
    } catch (PDOException $ex) {
        error_log("Error al insertar película: " . $ex->getMessage());
        return false;
    }
}

/**
 * Obtiene los datos de una película por su ID.
 *
 * @param PDO $pdo Conexión PDO válida.
 * @param int $id ID de la película.
 * @return array|false Array asociativo con datos (id,titulo,genero,direccion,duracion,argumento,anio) o false.
 */
function obtenerPeliculaPorId(PDO $pdo, int $id) {
    try {
        $sql = "SELECT id, titulo, genero, direccion, duracion, argumento, anio
                FROM peliculas
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: false;
    } catch (PDOException $ex) {
        error_log("Error al obtener película por id: " . $ex->getMessage());
        return false;
    }
}

/**
 * Actualiza una película existente.
 *
 * @param PDO $pdo Conexión PDO válida.
 * @param int $id ID de la película a actualizar.
 * @param array $datos Array con claves: titulo,genero,direccion,duracion,argumento,anio
 * @return int|false Número de registros modificados (>=1) o false si no se modificó nada o hubo error.
 */
function actualizarPelicula(PDO $pdo, int $id, array $datos) {
    try {
        $sql = "UPDATE peliculas
                SET titulo = :titulo,
                    genero = :genero,
                    direccion = :direccion,
                    duracion = :duracion,
                    argumento = :argumento,
                    anio = :anio
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':genero', $datos['genero'], PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(':duracion', $datos['duracion'], PDO::PARAM_INT);
        $stmt->bindParam(':argumento', $datos['argumento'], PDO::PARAM_STR);
        $stmt->bindParam(':anio', $datos['anio'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // rowCount indica cuántas filas fueron afectadas por la operación
        $n = $stmt->rowCount();
        return ($n > 0) ? $n : false;
    } catch (PDOException $ex) {
        error_log("Error al actualizar película: " . $ex->getMessage());
        return false;
    }
}
/*
 * Elimina una película de la base de datos por su ID.
 *
 * @param PDO $pdo Instancia PDO con conexión válida.
 * @param int $id  ID de la película a eliminar.
 * @return int|false Número de registros eliminados (>=1) o false si no se eliminó nada o hubo error.
 */
function eliminarPelicula(PDO $pdo, int $id){
    try {
        // Preparamos la consulta DELETE con marcador nombrado
        $sql = "DELETE FROM peliculas WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Vinculamos el parámetro con bindParam (buena práctica para tipos)
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutamos la sentencia
        $stmt->execute();

        // Comprobamos cuántas filas fueron afectadas
        $filas = $stmt->rowCount();

        // Si se eliminó al menos una fila, devolvemos el número, si no devolvemos false
        return ($filas > 0) ? $filas : false;
    } catch (PDOException $ex) {
        // Registramos el error en el log de PHP y devolvemos false
        error_log("Error al eliminar película (ID: $id): " . $ex->getMessage());
        return false;
    }
}