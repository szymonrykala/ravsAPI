<?php 

$pdo = require_once('./dbConnect.php');

$pdo->query("CALL cleaning();");
