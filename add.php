<?php
require_once __DIR__ . '/functions.php';

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
        addTransaction($pdo, (float) $amount, $category, $date);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати операцію</title>
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

<h1>Додати операцію</h1>

<?php foreach ($errors as $error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="post" action="add.php">
    <label for="amount">Сума (грн, від'ємна = витрата)</label>
    <input type="number" step="0.01" id="amount" name="amount"
           value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>

    <label for="category">Категорія</label>
    <input type="text" id="category" name="category"
           value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" required>

    <label for="transaction_date">Дата</label>
    <input type="date" id="transaction_date" name="transaction_date"
           value="<?= htmlspecialchars($_POST['transaction_date'] ?? date('Y-m-d')) ?>" required>

    <button type="submit">Зберегти</button>
</form>

<a class="back" href="index.php">&larr; Назад до списку</a>

</body>
</html>