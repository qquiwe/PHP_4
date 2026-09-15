<?php
require_once __DIR__ . '/functions.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    deleteTransaction($pdo, $id);
}

header('Location: index.php');
exit;