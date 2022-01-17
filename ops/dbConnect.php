<?php

$db = parse_url(getenv("CLEARDB_DATABASE_URL"));

$pdo = new PDO(
    "mysql:" . sprintf(
        "host=%s;port=%s;user=%s;password=%s;dbname=%s;sslmode=require",
        $db["host"],
        $db["port"] ?? 3306,
        $db["user"],
        $db["pass"],
        ltrim($db["path"], "/")
    ),
    $db['user'],
    $db["pass"]
);

return $pdo;
