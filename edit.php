<?php
require_once __DIR__ . '/functions.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$transaction = getTransactionById($pdo, $id);

if (!$transaction) {
    die('Запис не знайдено.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['transaction_date'] ?? '');

    if ($amount === '' || !is_numeric($amount)) {
        $errors[] = 'Сума має бути числом (додатним для доходу, від\'ємним для витрати).';
    }
    if ($category === '') {
        $errors[] = 'Вкажіть категорію.';
    }
    if ($date === '' || !DateTime::createFromFormat('Y-m-d', $date)) {
        $errors[] = 'Вкажіть коректну дату.';
    }

    if (empty($errors)) {
        updateTransaction($pdo, $id, (float) $amount, $category, $date);
        header('Location: index.php');
        exit;
    }

    // якщо є помилки - показати форму зі щойно введеними даними
    $transaction = [
        'id' => $id,
        'amount' => $amount,
        'category' => $category,
        'transaction_date' => $date,
    ];
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати операцію</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 30px auto; padding: 0 15px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
        button { margin-top: 18px; padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .error { color: #c0392b; margin-top: 10px; }
        a.back { display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

<h1>Редагувати операцію #<?= (int) $transaction['id'] ?></h1>

<?php foreach ($errors as $error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="post" action="edit.php">
    <input type="hidden" name="id" value="<?= (int) $transaction['id'] ?>">

    <label for="amount">Сума (грн, від'ємна = витрата)</label>
    <input type="number" step="0.01" id="amount" name="amount"
           value="<?= htmlspecialchars((string) $transaction['amount']) ?>" required>

    <label for="category">Категорія</label>
    <input type="text" id="category" name="category"
           value="<?= htmlspecialchars($transaction['category']) ?>" required>

    <label for="transaction_date">Дата</label>
    <input type="date" id="transaction_date" name="transaction_date"
           value="<?= htmlspecialchars($transaction['transaction_date']) ?>" required>

    <button type="submit">Зберегти зміни</button>
</form>

<a class="back" href="index.php">&larr; Назад до списку</a>

</body>
</html>