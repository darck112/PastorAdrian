<?php
// Incluimos el archivo para poder conectar con la base de datos, 
//__DIR__ garantiza que la ruta sea correcta independientemente del directorio desde el que se invoque.
require_once __DIR__ . '/dbconn.php';

/**
 * Obtiene todos los géneros existentes en la base de datos.
 * 
 * Se utiliza para mostrar los checkboxes de selección de géneros
 * en el formulario de filtrado.
 *
 * @return array Array asociativo con id, nombre y descripción de cada género
 */
function obtenerGeneros(): array {//devuelve un array con los generos
    $pdo = Conectar();//llamamos a la funcion conectar (aplicable al resto de funciones)
    //seleccionamos id, nombre y descripcion de la tabla generos, ordenados por nombre
    $stmt = $pdo->query('SELECT id, nombre, descripcion FROM generos ORDER BY nombre');
    //si no hubiera resultados, devolveriamos un array vacio. 
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Devuelve películas. Si $generos es null, devuelve todas.
 * Si es un array de IDs, filtra usando la cláusula IN.
 * den entrada reribe null para postrar todas las peliculas
 * si recibe un array es para filtrar los generos
 * ?array significa que puede ser null.
 * @param array|null $generos Array de IDs de géneros o null
 * @return array Lista de películas
 */
function obtenerPeliculasPorGeneros(?array $generos = null): array {
    $pdo = Conectar();
    // Si no hay filtro o el array está vacío, devolver todo
    if (empty($generos)) {//seleccionamos todos los campos de la tabla peliculas con p.*
        //el nombre lo cogemos de la tabla generos y lo ponemos como nombre_genero para usarlo en el html.
        $sql = "SELECT p.*, g.nombre AS nombre_genero
                FROM peliculas p
                LEFT JOIN generos g ON p.genero = g.id
                ORDER BY p.titulo";
        //realiazamos LEFT JOIN que nos permite que una película sin género asignado siga apareciendo.
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    
    //En el supuesto de que ya existan datos, los transformamosa enteros para evitar inyecciones, aunque usaremos prepare
    $ids = array_map('intval', $generos);
    
    // Crear placeholders (?,?,?) dinámicamente según la cantidad de géneros
    // count(ids) cuenta los generos seleccionados por el usuario
    //array_fill crea un array lleno de ?
    //convertimos este array en un string separado por comas
    //de esta forma podemos usarlo en la consulta sql independientemente de los generos seleccionados por el usuario
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $sql = "SELECT p.*, g.nombre AS nombre_genero
            FROM peliculas p
            LEFT JOIN generos g ON p.genero = g.id
            WHERE p.genero IN ($placeholders)
            ORDER BY p.titulo";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($ids));
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}


/**
 * Valida los IDs de géneros recibidos desde el formulario.
 * 
 * Devuelve únicamente aquellos IDs que existen realmente
 * en la base de datos.
 *
 * Se utiliza para evitar manipulaciones del formulario
 * (por ejemplo ERROR1, ERROR2).
 *
 * @param array $ids IDs recibidos
 * @return array IDs válidos
 */
function validarIdsGenerosExistentes(array $ids): array {
    if (empty($ids)) return [];
    
    $pdo = Conectar();
    // Convertir a enteros y eliminamos duplicados para sanear entrada básica
    $intIds = array_unique(array_map('intval', $ids));
    
    if (empty($intIds)) return [];

    $placeholders = implode(',', array_fill(0, count($intIds), '?'));
    $sql = "SELECT id FROM generos WHERE id IN ($placeholders)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($intIds));
    
    // Devuelve un array plano con los IDs válidos
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
}

/**
 * Autentica un usuario contra la base de datos.
 *
 * @param PDO    $pdo        Conexión PDO activa
 * @param string $login      Nombre de usuario
 * @param string $password   Contraseña en texto plano
 *
 * @return int|false
 *   - Devuelve el ID del usuario si la autenticación es correcta
 *   - Devuelve false si el usuario no existe o la contraseña es incorrecta
 * 
 * Se prepara la consulta para buscar el usuario, posteriormente se prpara la consulta, pasamos el valor de
 * login y obtenemos los resultados 
 */
function autenticarUsuario(PDO $pdo, string $login, string $password) {

    //  Preparar la consulta para buscar al usuario
    $sql = "SELECT id, password FROM usuarios WHERE login = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

   // Si no existe el usuario, fallamos autenticación
    if (!$usuario) {
        return false;
    }

    // Seguimos la lógica que utiliza la base de datos para guardar la contraseña, para poder comparar
    // calculamos el hash de la concatenacion de revertir el usuario y la contraseña
    $hash_calculado = hash('sha256',  strrev($login) . strrev($password)
    );

    //Comparar hashes para comprobar si es correcta o no la contraseña.
    if (!hash_equals($usuario['password'], $hash_calculado)) {
        return false;
    }

    // Devolvemos el ID del usuario si es correcta
    return (int)$usuario['id'];
}
/**
 * Obtiene los datos de una película por su ID.
 *
 * @param int $id  Identificador de la película
 *
 * @return array|null
 *   - array asociativo con los datos de la película si existe
 *   - null si no existe
 */
function obtenerPeliculaPorId(int $id): ?array {
    $pdo = Conectar();
    $sql = "SELECT p.*, g.nombre AS nombre_genero
        FROM peliculas p
        LEFT JOIN generos g ON p.genero = g.id
        WHERE p.id = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);
    return $pelicula ?: null;
}
/**
 * Obtiene los votos y críticas de una película.
 *
 * @param int $id_pelicula
 *
 * @return array Lista de votos y comentarios
 */
function obtenerVotosPorPelicula(int $id_pelicula): array {
    $pdo = Conectar();

    $sql = "
        SELECT 
            c.id,
            c.usuario AS usuario_id,
            u.login,
            c.valoracion AS voto,
            c.comentario AS critica
        FROM criticas c
        INNER JOIN usuarios u ON c.usuario = u.id
        WHERE c.pelicula = ?
        ORDER BY c.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pelicula]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Devuelve el número total de votos de una película.
 */
function obtenerNumeroVotosPorPelicula(int $id_pelicula): int {
    $pdo = Conectar();

    $sql = "SELECT COUNT(*) FROM criticas WHERE pelicula = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pelicula]);

    return (int)$stmt->fetchColumn();
}
/**
 * Obtiene la puntuación media de una película.
 *
 * @param int $id_pelicula
 * @return float|null  Media aritmética o null si no hay votos
 */
function obtenerPuntuacionMediaPorPelicula(int $id_pelicula): ?float {
    $pdo = Conectar();

    $sql = "
        SELECT AVG(valoracion) AS media
        FROM criticas
        WHERE pelicula = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pelicula]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return $resultado['media'] !== null
        ? round((float)$resultado['media'], 2)
        : null;
}
/**
 * Comprueba si un usuario ya ha votado una película.
 *
 * @return bool true si ya existe una crítica, false en caso contrario
 */
function usuarioHaVotadoPelicula(int $idUsuario, int $idPelicula): bool {
    $pdo = Conectar();
    $sql = "SELECT COUNT(*) FROM criticas WHERE usuario = ? AND pelicula = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUsuario, $idPelicula]);
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Inserta un voto/critica en la BBDD.
*
 * @return int|false ID insertado o false si falla
 */
function insertarVoto(int $idPelicula, int $idUsuario, int $valoracion, string $comentario) {
    $pdo = Conectar();
    $sql = "INSERT INTO criticas (valoracion, comentario, pelicula, usuario) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$valoracion, $comentario, $idPelicula, $idUsuario]);
    if ($ok) {
        return (int)$pdo->lastInsertId();
    }
    return false;
}

/**
 * Obtiene una crítica por su id.
 * Devuelve array asociativo con los campos de criticas o null si no existe.
 */
function obtenerCriticaPorId(int $idCritica): ?array {
    $pdo = Conectar();
    $sql = "SELECT c.*, u.login FROM criticas c LEFT JOIN usuarios u ON c.usuario = u.id WHERE c.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idCritica]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    return $fila ?: null;
}

/**
 * Elimina una crítica por su id.
 *
 * @return bool true si se eliminó correctamente
 */
function eliminarCritica(int $idCritica): bool {
    $pdo = Conectar();
    $sql = "DELETE FROM criticas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idCritica]);
    return $stmt->rowCount() > 0;
}
?>