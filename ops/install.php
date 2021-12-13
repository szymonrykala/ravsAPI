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


$pdo->exec(
    "DROP TABLE IF EXISTS 
        configuration,
        access, 
        image,
        request,
        \"user\",
        address,
        building,
        room,
        reservation"
);


$pdo->exec("SET TIMEZONE='Europe/Warsaw';");

/* #### T A B L E S #### */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS configuration (
        key TEXT NOT NULL,
        value int NOT NULL,

        PRIMARY KEY (key)
    );"
);

$pdo->exec(
    "INSERT INTO
        configuration (key, value)
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
        id SERIAL PRIMARY KEY, 
        name TEXT NOT NULL,
        owner BOOLEAN NOT NULL DEFAULT FALSE,
        access_admin BOOLEAN NOT NULL DEFAULT FALSE,
        premises_admin BOOLEAN NOT NULL DEFAULT FALSE,
        keys_admin BOOLEAN NOT NULL DEFAULT FALSE,
        reservations_admin BOOLEAN NOT NULL DEFAULT FALSE,
        reservations_ability BOOLEAN NOT NULL DEFAULT FALSE,
        logs_admin BOOLEAN NOT NULL DEFAULT FALSE,
        stats_viewer BOOLEAN NOT NULL DEFAULT FALSE,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(name)
    );"
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
        'Admin',
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
$pdo->exec("SELECT setval('access_id_seq', 3);");


/* IMAGE */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS image (
        id SERIAL PRIMARY KEY,
        public_id TEXT NOT NULL,
        size INT NOT NULL,
        url TEXT DEFAULT NULL,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );"
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

$pdo->exec("SELECT setval('image_id_seq', 3);");


/* REQUEST */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS request (
        id SERIAL PRIMARY KEY,
        method TEXT NOT NULL,
        endpoint TEXT NOT NULL,
        user_id INT DEFAULT NULL,
        payload TEXT NOT NULL DEFAULT '{}',
        time float DEFAULT 1,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );"
);


/* USER */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS \"user\" (
        id SERIAL PRIMARY KEY,
        access INT NOT NULL,
        image INT DEFAULT 1,
        name TEXT NOT NULL,
        surname TEXT NOT NULL,
        email TEXT NOT NULL,
        password TEXT NOT NULL,
        activated BOOLEAN NOT NULL DEFAULT FALSE,
        login_fails INT NOT NULL DEFAULT 0,
        blocked BOOLEAN NOT NULL DEFAULT FALSE,
        deleted BOOLEAN NOT NULL DEFAULT FALSE,
        unique_key TEXT DEFAULT NULL,
        last_generated_key_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        metadata TEXT NOT NULL DEFAULT '{}'::TEXT,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_activity TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(email),

        CONSTRAINT user_image FOREIGN key (image) REFERENCES image(id) 
            ON UPDATE CASCADE 
            ON DELETE SET DEFAULT, /* set default image when deleting old one */

        CONSTRAINT user_acceses FOREIGN KEY (access) REFERENCES access(id) 
            ON UPDATE cascade 
            ON DELETE RESTRICT -- do not let to delete access when users are still there
    );"
);


$sth = $pdo->prepare(
    "INSERT INTO \"user\" (
        id,
        access, 
        name, 
        surname, 
        email, 
        password, 
        activated
        ) VALUES(
            1,
            1,
            'Ravs', 
            'Admin', 
            :email,
            :password,
            TRUE
        );
    "
);

$sth->execute([
    ':password' => password_hash($adminPassword, PASSWORD_BCRYPT),
    ':email' => $adminEmail
]);

$pdo->exec("SELECT setval('user_id_seq', 2);");


/* ADDRESS */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS address (
        id SERIAL PRIMARY KEY,
        country TEXT NOT NULL,
        town TEXT NOT NULL,
        postal_code TEXT NOT NULL,
        street TEXT NOT NULL,
        number TEXT NOT NULL,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(
            country,
            town,
            postal_code,
            street,
            number
        )
    );"
);


$pdo->exec(
    "CREATE TABLE IF NOT EXISTS building (
        id SERIAL PRIMARY KEY,
        name TEXT NOT NULL,
        image INT NOT NULL DEFAULT 3,
        address INT NOT NULL,
        open_time time NOT NULL,
        close_time time NOT NULL,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(name, address),

        CONSTRAINT building_image FOREIGN KEY (image) REFERENCES image(id) 
            ON UPDATE CASCADE 
            ON DELETE SET DEFAULT,

        CONSTRAINT buildings_to_addresses FOREIGN KEY (address) REFERENCES address(id) 
            ON UPDATE CASCADE 
            ON DELETE RESTRICT
    );"
);


$pdo->exec(
    "CREATE TABLE IF NOT EXISTS room (
        id SERIAL PRIMARY KEY,
        name TEXT NOT NULL,
        image INT NOT NULL DEFAULT 2,
        rfid TEXT DEFAULT NULL,
        building INT NOT NULL,
        room_type TEXT NOT NULL,
        seats_count INT NOT NULL,
        floor INT NOT NULL,
        blocked BOOLEAN NOT NULL DEFAULT TRUE,
        occupied BOOLEAN NOT NULL DEFAULT FALSE,
        updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(name, building, floor),

        CONSTRAINT room_image FOREIGN KEY (image) REFERENCES image(id) 
            ON UPDATE CASCADE 
            ON DELETE SET DEFAULT,

        CONSTRAINT rooms_to_buildings FOREIGN KEY (building) REFERENCES building(id) 
            ON UPDATE CASCADE
            ON DELETE RESTRICT
    );"
);


$pdo->exec(
    "CREATE TABLE IF NOT EXISTS reservation (
        id SERIAL PRIMARY KEY,
        title TEXT NOT NULL,
        description TEXT NOT NULL DEFAULT 'Brak opisu.'::TEXT,
        room INT NOT NULL,
        \"user\" INT NOT NULL,
        planned_start TIMESTAMP NOT NULL,
        planned_end TIMESTAMP NOT NULL,
        actual_start TIMESTAMP DEFAULT NULL,
        actual_end TIMESTAMP DEFAULT NULL,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(room, actual_start),

        CONSTRAINT reserved_room FOREIGN KEY (room) REFERENCES room(id) 
            ON UPDATE CASCADE 
            ON DELETE CASCADE,

        CONSTRAINT reserving_user FOREIGN KEY (\"user\") REFERENCES \"user\"(id) 
            ON UPDATE CASCADE 
            ON DELETE CASCADE
    );"
);


/* #### P R O C E D U R E S #### */
$pdo->exec(
    "CREATE OR REPLACE PROCEDURE clean_requests() LANGUAGE SQL AS $$
    DELETE FROM
        request
    where
        ( EXTRACT( EPOCH FROM (CURRENT_TIMESTAMP - created) ) / 60 ) > (SELECT value FROM configuration WHERE key = 'REQUEST_HISTORY');
    $$;"
);


$pdo->exec(
    "CREATE OR REPLACE PROCEDURE clean_reservations() LANGUAGE SQL AS $$
    DELETE FROM
        reservation
    WHERE
        (EXTRACT( EPOCH FROM (CURRENT_TIMESTAMP - created) ) / 60) > (SELECT value FROM configuration WHERE key = 'RESERVATION_HISTORY');
    $$;"
);


$pdo->exec(
    "CREATE OR REPLACE PROCEDURE cleaning() LANGUAGE SQL AS $$ 
        CALL clean_requests();
        CALL clean_reservations();
    $$;"
);
