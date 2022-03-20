<?php 

$pdo = require_once(__DIR__ . '/dbConnect.php');

// cleaning database request and reservation tables
$pdo->query("CALL cleaning();");
