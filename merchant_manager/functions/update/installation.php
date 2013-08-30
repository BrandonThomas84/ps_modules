<?php
////////////////////////////////////////////////////////////////////////
//Installation Functions
////////////////////////////////////////////////////////////////////////

//create user tables
$createTableUsers = "
CREATE TABLE `" . $schema . "`.`mc_members` (
  `id` INT NOT NULL AUTO_INCREMENT, 
  `username` VARCHAR(30) NOT NULL, 
  `email` VARCHAR(50) NOT NULL, 
  `password` CHAR(128) NOT NULL, 
  `salt` CHAR(128) NOT NULL,
   PRIMARY KEY (`id`)
);";
//create Brute Force Attack Tables
$createTableLoginAttempt = "
CREATE TABLE `" . $schema . "`.`mc_login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` VARCHAR(30) NOT NULL 
);";

//create merchant_center_select_config
$createTableFieldSettings = "
  CREATE  TABLE `" . $schema . "`.`merchant_center_select_config` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `merchant_id` VARCHAR(100) NOT NULL ,
  `table_name` VARCHAR(255) NOT NULL ,
  `database_field_name` VARCHAR(255) NOT NULL ,
  `report_field_name` VARCHAR(255) NOT NULL ,
  `description` BLOB NULL ,
  `static` BIT NULL DEFAULT b'0' ,
  `static_value` VARCHAR(255) NULL ,
  `custom_function` VARCHAR(255) NULL ,
  `editable` BIT NULL DEFAULT b'0' ,
  `enabled` BIT NULL DEFAULT b'0' ,
  `order` INT NULL,
  `required`  BIT NULL DEFAULT b'0' ,
  PRIMARY KEY (`merchant_id`, `table_name`, `database_field_name`, `report_field_name`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) );";

//create merchant center exclusion table 
$createTableExclusions = "
CREATE TABLE `" . $schema . "`.`merchant_exclusion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` varchar(45) NOT NULL,
  `exclusion` varchar(10) NOT NULL,
  PRIMARY KEY (`id`,`id_product`,`exclusion`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;";


//create google taxonomy table
$createTableGoogleTaxonomy = "
CREATE TABLE `" . $schema . "`.`mc_taxonomy` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `merchant_id` VARCHAR(255) NOT NULL,
  `level1` VARCHAR(255) NOT NULL,
  `level2` VARCHAR(255) NULL,
  `level3` VARCHAR(255) NULL,
  `level4` VARCHAR(255) NULL,
  `level5` VARCHAR(255) NULL,
  `level6` VARCHAR(255) NULL,
  `level7` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));";

//create taxonomy mapping table
$createTaxonomyMapping = "
CREATE TABLE `" . $schema . "`.`mc_cattax_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_string` varchar(255) NOT NULL,
  `cattax_id` int(11) DEFAULT NULL,
  `cattax_merchant_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`,`category_string`,`cattax_mechant_id`),
  UNIQUE KEY `idmc_cattax_conversion_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

"
?>