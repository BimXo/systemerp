<?php
require_once 'SalesModel.php';
require_once 'SalesController.php';

$model = new SalesModel();
$controller = new SalesController($model);

// OBSŁUGA AKCJI (Zamiast switch w pętli)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $controller->model->create($_POST['cid'], $_POST['prod'], $_POST['amt'], $_POST['date']);
    }
}

$allSales = $controller->model->getAll();
$bestTx = $controller->getHighestRevenueTransaction();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Safe ERP Web</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; background: #f4f4f4; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f8f8; }
        input { padding: 8px; margin: 5px 0; width: 200px; display: block; }
        .stats { color: green; font-weight: bold; }
    </style>
</head>
<body>

    <h1>🛡️ Safe ERP - Moduł Sprzedaży</h1>

    <div class="card">
        <h3>1. Dodaj transakcję</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="cid" placeholder="ID Klienta" required>
            <input type="text" name="prod" placeholder="Produkt" required>
            <input type="number" name="amt" placeholder="Kwota" step="0.01" required>
            <input type="date" name="date" required>
            <button type="submit">Zapisz do pliku</button>
        </form>
    </div>

    <div class="card">
        <h3>2. Statystyki okresowe</h3>
        <form method="GET">
            <input type="date" name="start" required>
            <input type="date" name="end" required>
            <button type="submit">Oblicz</button>
        </form>
        <?php if (isset($_GET['start'])): 
            $res = $controller->getStatsBetweenDates($_GET['start'], $_GET['end']); ?>
            <p class="stats">Wynik: Transakcji: <?= $res['count'] ?>, Suma: <?= $res['sum'] ?> PLN</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>3. Historia transakcji</h3>
        <table>
            <tr><th>ID</th><th>Klient</th><th>Produkt</th><th>Cena</th><th>Data</th></tr>
            <?php foreach ($allSales as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s[0]) ?></td>
                    <td><?= htmlspecialchars($s[1]) ?></td>
                    <td><?= htmlspecialchars($s[2]) ?></td>
                    <td><?= htmlspecialchars($s[3]) ?></td>
                    <td><?= htmlspecialchars($s[4]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>