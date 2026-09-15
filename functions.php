<?php

require_once __DIR__ . '/db.php';

// всі записи, найновіші зверху 
function getAllTransactions(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM transactions ORDER BY transaction_date DESC, id DESC');
    return $stmt->fetchAll();
}

// один запис за id (для форми редагування)
function getTransactionById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

// сума операцій за категорією 
function totalByCategory(PDO $pdo, string $category): float
{
    $stmt = $pdo->prepare('SELECT SUM(amount) AS total FROM transactions WHERE category = :category');
    $stmt->execute([':category' => $category]);
    $result = $stmt->fetch();
    return (float) ($result['total'] ?? 0);
}

// загальний баланс (сума)    
function balance(PDO $pdo): float
{
    $stmt = $pdo->query('SELECT SUM(amount) AS balance FROM transactions');
    $result = $stmt->fetch();
    return (float) ($result['balance'] ?? 0);
}

// список унікальних категорій (для випадаючого списку у формі) 
function getCategories(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT DISTINCT category FROM transactions ORDER BY category');
    return array_column($stmt->fetchAll(), 'category');
}

// додати новий запис
function addTransaction(PDO $pdo, float $amount, string $category, string $date): string
{
    $stmt = $pdo->prepare(
        'INSERT INTO transactions (amount, category, transaction_date) VALUES (:amount, :category, :date)'
    );
    $stmt->execute([
        ':amount'   => $amount,
        ':category' => $category,
        ':date'     => $date,
    ]);
    return $pdo->lastInsertId();
}

// оновити існуючий запис
function updateTransaction(PDO $pdo, int $id, float $amount, string $category, string $date): int
{
    $stmt = $pdo->prepare(
        'UPDATE transactions
         SET amount = :amount, category = :category, transaction_date = :date
         WHERE id = :id'
    );
    $stmt->execute([
        ':amount'   => $amount,
        ':category' => $category,
        ':date'     => $date,
        ':id'       => $id,
    ]);
    return $stmt->rowCount();
}

// видалити запис за id
function deleteTransaction(PDO $pdo, int $id): int
{
    $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}