DROP TABLE IF EXISTS `criticas` ;
DROP TABLE IF EXISTS `usuarios` ;
DROP TABLE IF EXISTS `peliculas` ;
DROP TABLE IF EXISTS `generos` ;

CREATE TABLE IF NOT EXISTS `generos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion` TINYTEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `peliculas` (
  `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(60) NOT NULL,
  `genero` INT,
  `direccion` VARCHAR(100) NOT NULL,
  `duracion` INT NOT NULL CHECK (duracion>0 AND duracion<500),
  `argumento` TINYTEXT NOT NULL,
  `anio` INT NOT NULL CHECK (anio>1900),
  INDEX `fk_peliculas_generos1_idx` (`genero` ASC),
  CONSTRAINT `fk_peliculas_generos1`
    FOREIGN KEY (`genero`)
    REFERENCES `generos` (`id`)
    ON DELETE SET NULL
    ON UPDATE SET NULL )
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(45) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `lastlogin` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `criticas` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `valoracion` INT NOT NULL,
  `comentario` TINYTEXT NOT NULL,
  `pelicula` BIGINT NOT NULL,
  `usuario` BIGINT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_critica_peliculas1`
    FOREIGN KEY (`pelicula`)
    REFERENCES `peliculas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_critica_usuarios1`
    FOREIGN KEY (`usuario`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;