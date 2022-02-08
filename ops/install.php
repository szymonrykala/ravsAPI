<?php

$pdo = require_once(__DIR__ . '/dbConnect.php');

$adminPassword = getenv('ADMIN_PASSWORD');
$adminEmail = getenv('ADMIN_EMAIL');

if (empty($adminPassword)) {
    throw new Exception('You must specify "ADMIN_PASSWORD" to proceed.');
}

if (empty($adminEmail)) {
    throw new Exception('You must specify "ADMIN_EMAIL" to proceed.');
}

$foot = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

$pdo->exec(
    "DROP TABLE IF EXISTS 
        configuration,
        reservation,
        user,
        access, 
        room,
        building,
        address,
        request,
        image
        "
);


$pdo->exec("SET time_zone='Europe/Warsaw';");

/* #### T A B L E S #### */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS configuration (
        `key` VARCHAR(255) NOT NULL,
        `value` int(11) NOT NULL,

        UNIQUE KEY `unique_config_key` (`key`)
    ) $foot;"
);

$pdo->exec(
    "INSERT INTO
        configuration (`key`, `value`)
    VALUES
        ('DEFAULT_USER_ACCESS', 2),
        ('MAX_IMAGE_SIZE', 500000),
        ('MAX_RESERVATION_TIME', 240),
        ('MIN_RESERVATION_TIME', 20),
        ('REQUEST_HISTORY', 50),
        ('RESERVATION_HISTORY', 360)"
);



$pdo->exec(
    "CREATE TABLE IF NOT EXISTS access (
        `id` int(11) NOT NULL auto_increment, 
        `name` VARCHAR(255) NOT NULL,
        `owner` boolean NOT NULL DEFAULT FALSE,
        `access_admin` boolean NOT NULL DEFAULT FALSE,
        `premises_admin` boolean NOT NULL DEFAULT FALSE,
        `keys_admin` boolean NOT NULL DEFAULT FALSE,
        `reservations_admin` boolean NOT NULL DEFAULT FALSE,
        `reservations_ability` boolean NOT NULL DEFAULT FALSE,
        `logs_admin` boolean NOT NULL DEFAULT FALSE,
        `stats_viewer` boolean NOT NULL DEFAULT FALSE,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_access_name`(`name`)
    ) $foot;"
);


$pdo->exec(
    "INSERT INTO
    access (
        id,
        name,
        owner,
        access_admin,
        premises_admin,
        keys_admin,
        reservations_admin,
        reservations_ability,
        logs_admin,
        stats_viewer
    )
    VALUES
    (
        1,
        'Predefined Owner',
        TRUE,
        TRUE,
        TRUE,
        TRUE,
        TRUE,
        TRUE,
        TRUE,
        TRUE
        ),(
        2,
        'Default',
        FALSE,
        FALSE,
        FALSE,
        FALSE,
        FALSE,
        FALSE,
        FALSE,
        FALSE
    );"
);


/* IMAGE */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS image (
        `id` int(11) NOT NULL auto_increment,
        `public_id` tinytext NOT NULL,
        `size` int(11) NOT NULL,
        `url` tinytext DEFAULT NULL,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;"
);

$pdo->exec(
    "INSERT INTO
        image ( id, public_id, size )
    VALUES
        (1, 'user.png', 31910),
        (2, 'room.jpg', 291000),
        (3, 'building.jpg', 280000)
    ;"
);



/* REQUEST */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS request (
        `id` int(11) NOT NULL auto_increment,
        `method` tinytext NOT NULL,
        `endpoint` tinytext NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `payload` mediumtext,
        `time` float DEFAULT 1,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`)
    ) $foot;"
);


/* USER */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS user (
        `id` int(11) NOT NULL auto_increment,
        `access` int(11) NOT NULL,
        `image` int(11) DEFAULT 1,
        `name` tinytext NOT NULL,
        `surname` tinytext NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `password` TEXT NOT NULL,
        `activated` boolean NOT NULL DEFAULT FALSE,
        `login_fails` int(11) NOT NULL DEFAULT 0,
        `blocked` boolean NOT NULL DEFAULT FALSE,
        `deleted` boolean NOT NULL DEFAULT FALSE,
        `unique_key` tinytext DEFAULT NULL,
        `last_generated_key_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `metadata` mediumtext,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_user_email`(`email`),
        
        CONSTRAINT `user_image` FOREIGN key (`image`) REFERENCES `image`(`id`)
            ON UPDATE CASCADE 
            ON DELETE SET DEFAULT, /* set default image when deleting old one */
        
        CONSTRAINT `user_acceses` FOREIGN KEY (`access`) REFERENCES `access`(`id`)
            ON UPDATE cascade 
            ON DELETE RESTRICT -- do not let to delete access when users are still there
    ) $foot;"
);


$sth = $pdo->prepare(
    "INSERT INTO user (
        id,
        access, 
        name, 
        surname, 
        email, 
        password, 
        activated,
        metadata
        ) VALUES(
            1,
            1,
            'Ravs', 
            'Admin', 
            :email,
            :password,
            TRUE,
            '{}'
        );
    "
);

$sth->execute([
    ':password' => password_hash($adminPassword, PASSWORD_BCRYPT),
    ':email' => $adminEmail
]);


/* ADDRESS */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS address (
        `id` int(11) NOT NULL auto_increment,
        `country` VARCHAR(255) NOT NULL,
        `town` VARCHAR(150) NOT NULL,
        `postal_code` VARCHAR(25) NOT NULL,
        `street` VARCHAR(150) NOT NULL,
        `number` VARCHAR(25) NOT NULL,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_address`(
            `country`,
            `town`,
            `postal_code`,
            `street`,
            `number`
        )
    ) $foot;"
);


$pdo->exec(
    "CREATE TABLE IF NOT EXISTS building (
        `id` int(11) NOT NULL auto_increment,
        `name` VARCHAR(150) NOT NULL,
        `image` int(11) NOT NULL DEFAULT 3,
        `address` int(11) NOT NULL,
        `open_time` time NOT NULL,
        `close_time` time NOT NULL,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_building_in_address` (`name`, `address`),

        CONSTRAINT `building_image` FOREIGN KEY (`image`) REFERENCES `image`(`id`)
            ON UPDATE CASCADE
            ON DELETE SET DEFAULT, /* set default image when deleting old one */

        CONSTRAINT `buildings_to_addresses` FOREIGN KEY (`address`) REFERENCES `address`(`id`)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
    ) $foot;"
);


$pdo->exec(
    "CREATE TABLE IF NOT EXISTS room (
        `id` int(11) auto_increment PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `image` int(11) NOT NULL DEFAULT 2,
        `rfid` tinytext DEFAULT NULL,
        `building` int(11) NOT NULL,
        `room_type` tinytext NOT NULL,
        `seats_count` int(11) NOT NULL,
        `floor` int(11) NOT NULL,
        `blocked` boolean NOT NULL DEFAULT TRUE,
        `occupied` boolean NOT NULL DEFAULT FALSE,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        CONSTRAINT unique_room_in_building UNIQUE KEY (`name`, `building`, `floor`),

        CONSTRAINT `room_image` FOREIGN KEY (`image`) REFERENCES `image`(`id`) 
            ON UPDATE CASCADE 
            ON DELETE SET DEFAULT, /* set default image when deleting old one */

        CONSTRAINT `rooms_to_buildings` FOREIGN KEY (`building`) REFERENCES `building`(`id`) 
            ON UPDATE CASCADE
            ON DELETE RESTRICT
    ) $foot;"
);


$pdo->exec(
    "CREATE TABLE IF NOT EXISTS reservation (
        `id` int(11) auto_increment PRIMARY KEY,
        `title` mediumtext NOT NULL,
        `description` mediumtext,
        `room` int(11) NOT NULL,
        `user` int(11) NOT NULL,
        `planned_start` timestamp NOT NULL,
        `planned_end` timestamp NOT NULL DEFAULT NOW(),
        `actual_start` timestamp DEFAULT NULL NULL,
        `actual_end` timestamp DEFAULT NULL NULL,
        `created` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated` timestamp DEFAULT CURRENT_TIMESTAMP,

        CONSTRAINT unique_room_reservation_start UNIQUE KEY (`room`, `actual_start`),

        KEY `reserved_room` (`room`),
        CONSTRAINT `reserved_room` FOREIGN KEY (`room`) REFERENCES `room`(`id`) 
            ON UPDATE CASCADE 
            ON DELETE CASCADE,

        KEY `reserving_user` (`user`),
        CONSTRAINT `reserving_user` FOREIGN KEY (`user`) REFERENCES `user`(`id`) 
            ON UPDATE CASCADE 
            ON DELETE CASCADE
    ) $foot;"
);


/* #### P R O C E D U R E S #### */

$pdo->exec("DROP PROCEDURE IF EXISTS clean_requests;");
$pdo->exec(
    "CREATE PROCEDURE clean_requests()
    BEGIN
	    DELETE FROM
                request
            where
                ( TIME_TO_SEC( TIMEDIFF( NOW(), `created`) ) / 86400) > (SELECT `value` FROM configuration WHERE `key` = 'REQUEST_HISTORY');
    END;"
);


$pdo->exec("DROP PROCEDURE IF EXISTS clean_reservations;");
$pdo->exec(
    "CREATE PROCEDURE clean_reservations()
    BEGIN
        DELETE FROM
                reservation
            WHERE
                (
                    `actual_end` < NOW() AND
                    ( TIME_TO_SEC( TIMEDIFF( NOW(), `actual_end`) ) / 86400) > (SELECT `value` FROM configuration WHERE `key` = 'RESERVATION_HISTORY')
                ) OR (
                    `planned_end`  < NOW() AND
                            ( TIME_TO_SEC( TIMEDIFF( NOW(), `planned_end`) ) / 86400) > (SELECT `value` FROM configuration WHERE `key` = 'RESERVATION_HISTORY')
                );
    END;"
);


$pdo->exec("DROP PROCEDURE IF EXISTS cleaning;");
$pdo->exec(
    "CREATE PROCEDURE cleaning()
    BEGIN
        CALL clean_requests();
        CALL clean_reservations();
    END;"
);
