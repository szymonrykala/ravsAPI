<?php 

$pdo = require_once(__DIR__ . '/dbConnect.php');

$pdo->query("CALL cleaning();");
