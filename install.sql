CREATE DATABASE servmon;
GRANT ALL on servmon.* to 'servmon'@'localhost' identified by '3mdueLa0';
USE servmon;
CREATE TABLE host (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `url` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `email` VARCHAR(256) NOT NULL,
    `includestr` VARCHAR(255) NOT NULL,
    `lastprobe` INT(10) NOT NULL,
    `response_time` INT(10) NOT NULL,
    `last_status_change` INT(10) NOT NULL,
    PRIMARY KEY(`id`)
);
CREATE TABLE host_log (
    `id` INT(10) NOT NULL,
    `time` INT(10) NOT NULL,
    `status` INT(10) NOT NULL,
    `response_time` INT(10) NOT NULL,
    `detail` TEXT,
    KEY key_id (`id`)
);
CREATE TABLE sms_log (
    `id` INT(10) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `time` INT(10) NOT NULL,
    `msg` TEXT NOT NULL,
    `status` TINYINT(1) NOT NULL,
    KEY key_id (`id`)
);
