<?php

$db = parse_url(getenv("DATABASE_URL"));

$pdo = new PDO(
    "pgsql:" . sprintf(
        "host=%s;port=%s;user=%s;password=%s;dbname=%s;sslmode=require",
        $db["host"],
        $db["port"],
        $db["user"],
        $db["pass"],
        ltrim($db["path"], "/")
    ),
    $db['user'],
    $db["pass"]
);

return $pdo;
