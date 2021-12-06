<?php

$db = parse_url(getenv("DATABASE_URL"));

$pdo = new PDO("pgsql:" . sprintf(
    "host=%s;port=%s;user=%s;password=%s;dbname=%s",
    $db["host"],
    $db["port"],
    $db["user"],
    $db["pass"],
    ltrim($db["path"], "/")
));

$adminPassword = getenv('ADMIN_PASSWORD');
$adminEmail = getenv('ADMIN_EMAIL');

if (empty($adminPassword)) {
    throw new Exception('You must specify "ADMIN_PASSWORD" to proceed.');
}

if (empty($adminEmail)) {
    throw new Exception('You must specify "ADMIN_EMAIL" to proceed.');
}


/* #### T A B L E S #### */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS configuration (
        key text NOT NULL,
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
        id serial PRIMARY KEY, 
        name text NOT NULL,
        owner boolean NOT NULL DEFAULT FALSE,
        access_admin boolean NOT NULL DEFAULT FALSE,
        premises_admin boolean NOT NULL DEFAULT FALSE,
        keys_admin boolean NOT NULL DEFAULT FALSE,
        reservations_admin boolean NOT NULL DEFAULT FALSE,
        reservations_ability boolean NOT NULL DEFAULT FALSE,
        logs_admin boolean NOT NULL DEFAULT FALSE,
        stats_viewer boolean NOT NULL DEFAULT FALSE,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

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


/* IMAGE */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS image (
        id serial PRIMARY KEY,
        public_id text NOT NULL,
        size int NOT NULL,
        url text DEFAULT NULL,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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


/* REQUEST */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS request (
        id serial PRIMARY KEY,
        method text NOT NULL,
        endpoint text NOT NULL,
        user_id int DEFAULT NULL,
        payload text NOT NULL DEFAULT '{}',
        time float DEFAULT 1,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    );"
);


/* USER */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS \"user\" (
        id serial PRIMARY KEY,
        access int NOT NULL,
        image int DEFAULT 1,
        name text NOT NULL,
        surname text NOT NULL,
        email text NOT NULL,
        password text NOT NULL,
        activated boolean NOT NULL DEFAULT FALSE,
        login_fails int NOT NULL DEFAULT 0,
        blocked boolean NOT NULL DEFAULT FALSE,
        deleted boolean NOT NULL DEFAULT FALSE,
        unique_key text DEFAULT NULL,
        last_generated_key_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        metadata text NOT NULL DEFAULT '{}'::text,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_activity timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

        UNIQUE(id, email),

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
        access, 
        name, 
        surname, 
        email, 
        password, 
        activated
        ) VALUES(
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


/* ADDRESS */
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS address (
        id serial PRIMARY KEY,
        country text NOT NULL,
        town text NOT NULL,
        postal_code text NOT NULL,
        street text NOT NULL,
        number text NOT NULL,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

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
        id serial PRIMARY KEY,
        name text NOT NULL,
        image int NOT NULL DEFAULT 3,
        address int NOT NULL,
        open_time time NOT NULL,
        close_time time NOT NULL,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

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
        id serial PRIMARY KEY,
        name text NOT NULL,
        image int NOT NULL DEFAULT 2,
        rfid text DEFAULT NULL,
        building int NOT NULL,
        room_type text NOT NULL,
        seats_count int NOT NULL,
        floor int NOT NULL,
        blocked boolean NOT NULL DEFAULT TRUE,
        occupied boolean NOT NULL DEFAULT FALSE,
        updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

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
        id serial PRIMARY KEY,
        title text NOT NULL,
        description text NOT NULL DEFAULT 'Brak opisu.'::text,
        room int NOT NULL,
        \"user\" int NOT NULL,
        planned_start timestamp NOT NULL,
        planned_end timestamp NOT NULL,
        actual_start timestamp DEFAULT NULL,
        actual_end timestamp DEFAULT NULL,
        created timestamp DEFAULT CURRENT_TIMESTAMP,
        updated timestamp DEFAULT CURRENT_TIMESTAMP,

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
