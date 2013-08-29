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
$createTable1 = "
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
$createTable2 = "
CREATE  TABLE `" . $schema . "`.`merchant_exclusion` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `id_product` VARCHAR(45) NOT NULL ,
  `google_exclude` BIT(1) NULL ,
  `amazon_exclude` BIT(1) NULL ,
  `pricegrabber_exclude` BIT(1) NULL ,
  `ebay_exclude` BIT(1) NULL ,
  `bing_exclude` BIT(1) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `id_product_UNIQUE` (`id_product` ASC) );";
?>