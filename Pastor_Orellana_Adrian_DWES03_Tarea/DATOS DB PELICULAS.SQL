
INSERT INTO `generos` (`id`, `nombre`, `descripcion`) VALUES
	(1,'animación', 'Películas de animación, tanto tradicionales como por computadora'),
	(2,'drama', 'Películas centradas en el desarrollo emocional y conflictos de los personajes'),
	(3,'ciencia ficción/fantasía', 'Películas con elementos tecnológicos, futuristas o fantásticos'),
	(4,'comedia', 'Películas destinadas a provocar risa y entretenimiento ligero'),
	(5,'thriller', 'Películas de suspense y tensión'),
	(6,'documental', 'Películas basadas en hechos reales y investigación');
	
INSERT INTO `peliculas` (`id`, `titulo`, `genero`, `direccion`, `duracion`, `argumento`, `anio`) VALUES
	(1, 'El viaje de Luma', 1, 'Carla Gómez', 95, 'Una joven descubre un mundo mágico durante un viaje inesperado.', 2018),
	(2, 'La caja de colores', 1, 'Miguel Ruiz', 82, 'Una aventura animada sobre amistad y creatividad.', 2015),
	(3, 'Bosque de papel', 1, 'Ana Torres', 100, 'Animación poética sobre un bosque que guarda secretos.', 2021),
	(4, 'Sombras en la ciudad', 2, 'Luis Martín', 120, 'Historia íntima de personajes cuyas vidas se entrelazan.', 2010),
	(5, 'Cartas que no llegaron', 2, 'María López', 110, 'Un drama sobre decisiones, pérdidas y segundas oportunidades.', 2017),
	(6, 'Día de otoño', 2, 'Pablo Herrera', 98, 'Retrato emocional de una familia en un día decisivo.', 2005),
	(7, 'Estrella de vidrio', 3, 'Elena Vega', 130, 'Ciencia ficción sobre un descubrimiento que cambia la humanidad.', 2023),
	(8, 'Aurora mecánica', 3, 'Diego Salas', 140, 'Una fusión de tecnología y mito en un futuro cercano.', 2020),
	(9, 'Los portales', 3, 'Nora Díaz', 105, 'Exploradores atraviesan mundos paralelos en busca de respuestas.', 2012),
	(10, 'Un café y dos risas', 4, 'Raúl Sánchez', 85, 'Comedia ligera sobre enredos entre amigos en una cafetería.', 2011),
	(11, 'Manual de excusas', 4, 'Sofía Ramos', 90, 'Situaciones cómicas derivadas de excusas cada vez más absurdas.', 2019),
	(12, 'Vecinos a destiempo', 4, 'Óscar Medina', 95, 'Comedia sobre la convivencia forzada entre vecinos opuestos.', 2008),
	(13, 'Silencio en la noche', 5, 'Carlos Núñez', 105, 'Un suspense tenso donde cada detalle cuenta.', 2016),
	(14, 'La huella', 5, 'Irene Paredes', 115, 'Thriller sobre una investigación que desentierra viejos secretos.', 2014),
	(15, 'Bajo la mira', 5, 'Fernando Cruz', 125, 'Persecución intelectual entre cazador y presa en clave psicológica.', 2022),
	(16, 'Raíces', 6, 'Pilar Ortiz', 72, 'Documental que explora tradiciones y memoria colectiva.', 2002),
	(17, 'Olas y gentes', 6, 'Andrés Molina', 80, 'Retrato documental de comunidades costeras y su relación con el mar.', 2015),
	(18, 'Archivos olvidados', 6, 'Lucía Blanco', 95, 'Investigación documental sobre archivos y testimonios del pasado.', 1999);

-- El campo `password` se genera como SHA256(CONCAT(REVERSE(login), REVERSE(password_plain)))
INSERT INTO `usuarios` (`id`, `login`, `password`, `lastlogin`) VALUES
	(1, 'maria', SHA2(CONCAT(REVERSE('maria'), REVERSE('123')), 256), CURRENT_TIMESTAMP),
	(2, 'juan', SHA2(CONCAT(REVERSE('juan'), REVERSE('abc')), 256), CURRENT_TIMESTAMP),
	(3, 'ana', SHA2(CONCAT(REVERSE('ana'), REVERSE('123')), 256), CURRENT_TIMESTAMP),
	(4, 'carlos', SHA2(CONCAT(REVERSE('carlos'), REVERSE('abc')), 256), CURRENT_TIMESTAMP);

INSERT INTO `criticas` (`id`, `valoracion`, `comentario`, `pelicula`, `usuario`) VALUES
	(1, 5, 'Magnífica, emocionante y visualmente hermosa.', 1, 1),
	(2, 4, 'Buena, aunque algo larga en partes.', 1, 2),
	(3, 4, 'Interesante pero con ritmo irregular.', 1, 3),
	(4, 4, 'Drama bien contado, actuaciones sólidas.', 2, 4),
	(5, 3, 'Correcta pero predecible.', 2, 1),
	(6, 4, 'Original y evocadora.', 3, 2),
	(7, 5, 'Muy divertida, nos reímos mucho.', 4, 3),
	(8, 3, 'No me convenció, se hace lenta en el tramo central.', 5, 4),
	(9, 4, 'Suspense bien llevado, muy recomendable.', 6, 1),
	(10, 4, 'Buena propuesta, ambiciosa pero entretenida.', 7, 2),
	(11, 3, 'Aceptable, con momentos destacables.', 5, 3),
	(12, 4, 'Entretenida y bien dirigida.', 8, 4);



