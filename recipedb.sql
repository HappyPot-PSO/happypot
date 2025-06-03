-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema recipedb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema recipedb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `recipedb` DEFAULT CHARACTER SET utf8 ;
USE `recipedb` ;

-- -----------------------------------------------------
-- Table `recipedb`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recipedb`.`user` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `fname` VARCHAR(12) NOT NULL,
  `lname` VARCHAR(12) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recipedb`.`recipe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recipedb`.`recipe` (
  `idrec` INT(10) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(20) NOT NULL,
  `img` VARCHAR(100) NOT NULL,
  `time` INT(3) NOT NULL,
  `ingredients` VARCHAR(500) NOT NULL,
  `instructions` VARCHAR(1000) NOT NULL,
  `user_id` INT(10) NOT NULL,
  PRIMARY KEY (`idrec`),
  INDEX `fk_recipe_user_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_recipe_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `recipedb`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recipedb`.`comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recipedb`.`comment` (
  `idcomment` INT NOT NULL AUTO_INCREMENT,
  `comm` VARCHAR(150) NOT NULL,
  `recipe_idrec` INT(10) NOT NULL,
  `user_id` INT(10) NOT NULL,
  PRIMARY KEY (`idcomment`),
  UNIQUE INDEX `idcomment_UNIQUE` (`idcomment` ASC) VISIBLE,
  INDEX `fk_comment_recipe1_idx` (`recipe_idrec` ASC) VISIBLE,
  INDEX `fk_comment_user1_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_comment_recipe1`
    FOREIGN KEY (`recipe_idrec`)
    REFERENCES `recipedb`.`recipe` (`idrec`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `recipedb`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
