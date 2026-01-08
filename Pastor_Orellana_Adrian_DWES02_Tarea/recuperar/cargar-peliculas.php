<?php
/*
 * Fragmento HTML para mostrar la tabla de películas.
 * No realiza consultas a la base de datos.
 */
if (!isset($peliculas) || !is_array($peliculas)) {
    echo "<p class='error'>No hay datos de películas disponibles.</p>";
    return;
}
?>
<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Género</th>
            <th>Dirección</th>
            <th>Duración (min)</th>
            <th>Argumento</th>
            <th>Año</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($peliculas as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['titulo']) ?></td>
                <td><?= htmlspecialchars($p['genero']) ?></td>
                <td><?= htmlspecialchars($p['direccion']) ?></td>
                <td><?= htmlspecialchars($p['duracion']) ?></td>
                <td><?= htmlspecialchars($p['argumento']) ?></td>
                <td><?= htmlspecialchars($p['anio']) ?></td>
                <td>
                    <form action="modificar/form-modificar-pelicula.php" method="post" style="padding: 12px;" >
                        <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                        <input type="submit" value="Modificar">
                    </form>
                    <form action="eliminar/confirma-eliminar-pelicula.php" method="post" style="padding: 12px;">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                        <input type="submit" value="Eliminar">
                    </form>
                </td>
                
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
