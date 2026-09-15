<?php

$dsn = 'mysql:host=localhost;dbname=practicum4;charset=utf8mb4';
$username = 'root';
$password = '14fidmacar'; 

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Помилка підключення до бази даних: ' . $e->getMessage());
}