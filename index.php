<?php
require_once __DIR__ . '/functions.php';

$transactions = getAllTransactions($pdo);
$totalBalance = balance($pdo);

// фільтр за категорією (демонстрація findByCategory через параметризований запит)
$filterCategory = $_GET['category'] ?? '';
if ($filterCategory !== '') {
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE category = :category ORDER BY transaction_date DESC');
    $stmt->execute([':category' => $filterCategory]);
    $transactions = $stmt->fetchAll();
}

$categories = getCategories($pdo);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Трекер особистих витрат</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 15px; color: #222; }
        h1 { margin-bottom: 5px; }
        .balance { font-size: 1.3em; margin-bottom: 20px; }
        .balance.positive { color: #1a7f37; }
        .balance.negative { color: #c0392b; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        tr:nth-child(even) { background: #fafafa; }
        .amount-pos { color: #1a7f37; font-weight: bold; }
        .amount-neg { color: #c0392b; font-weight: bold; }
        .actions a { margin-right: 10px; text-decoration: none; }
        .actions a.delete { color: #c0392b; }
        .actions a.edit { color: #2563eb; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .btn-add { background: #2563eb; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; }
        form.filter { margin: 15px 0; }
        select, button { padding: 6px 10px; }
    </style>
</head>
<body>

<div class="top-bar">
    <h1>Трекер особистих витрат (варіант 13)</h1>
    <a class="btn-add" href="add.php">+ Додати операцію</a>
</div>

<p class="balance <?= $totalBalance >= 0 ? 'positive' : 'negative' ?>">
    Загальний баланс: <?= number_format($totalBalance, 2, ',', ' ') ?> грн
</p>

<form class="filter" method="get" action="index.php">
    <label for="category">Фільтр за категорією:</label>
    <select name="category" id="category" onchange="this.form.submit()">
        <option value="">— усі категорії —</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $filterCategory === $cat ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit">Застосувати</button></noscript>
</form>

<?php if (empty($transactions)): ?>
    <p>Записів поки немає.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Дата</th>
            <th>Категорія</th>
            <th>Сума, грн</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= (int) $t['id'] ?></td>
                <td><?= htmlspecialchars($t['transaction_date']) ?></td>
                <td><?= htmlspecialchars($t['category']) ?></td>
                <td class="<?= $t['amount'] >= 0 ? 'amount-pos' : 'amount-neg' ?>">
                    <?= number_format((float) $t['amount'], 2, ',', ' ') ?>
                </td>
                <td class="actions">
                    <a class="edit" href="edit.php?id=<?= (int) $t['id'] ?>">Редагувати</a>
                    <a class="delete" href="delete.php?id=<?= (int) $t['id'] ?>"
                       onclick="return confirm('Видалити цю операцію?');">Видалити</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>