CREATE TABLE IF NOT EXISTS `#__xws_property_records` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`parish` VARCHAR(255)  NULL  DEFAULT "",
`houseno` INT(3)  NULL  DEFAULT 0,
`housename` VARCHAR(255)  NULL  DEFAULT "",
`streetname` VARCHAR(255)  NULL  DEFAULT "",
`streetname2` VARCHAR(255)  NULL  DEFAULT "",
`town` INT(10)  NULL  DEFAULT 0,
`postcode` VARCHAR(255)  NULL  DEFAULT "",
`marketvalue` DECIMAL(15,2)  NULL  DEFAULT 0,
`saleprice` DECIMAL(15,2)  NULL  DEFAULT 0,
`aquireddate` DATETIME NULL  DEFAULT NULL ,
`completeddate` DATETIME NULL  DEFAULT NULL ,
`hash` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xws_property_towns` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xws_property_parishes` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

