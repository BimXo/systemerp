<?php
require_once 'SalesModel.php';
require_once 'SalesController.php';

$model = new ModelSprzedazy();
$kontroler = new KontrolerSprzedazy($model);

// OBSŁUGA AKCJI (Zamiast switch w pętli)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akcja']) && $_POST['akcja'] === 'dodaj') {
        $kontroler->model->utworz($_POST['id_klienta'], $_POST['produkt'], $_POST['kwota'], $_POST['data']);
    }
}

$wszystkieSprzedaze = $kontroler->model->pobierzWszystkie();
$najwyzszaTransakcja = $kontroler->pobierzNajwyzszaTransakcjaPrzychod();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bezpieczny ERP</title>
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

    <h1>Bezpieczny ERP - Moduł Sprzedaży</h1>

    <div class="card">
        <h3>1. Dodaj transakcję</h3>
        <form method="POST">
            <input type="hidden" name="akcja" value="dodaj">
            <input type="text" name="id_klienta" placeholder="ID Klienta" required>
            <input type="text" name="produkt" placeholder="Produkt" required>
            <input type="number" name="kwota" placeholder="Kwota" step="0.01" required>
            <input type="date" name="data" required>
            <button type="submit">Zapisz do pliku</button>
        </form>
    </div>

    <div class="card">
        <h3>2. Statystyki okresowe</h3>
        <form method="GET">
            <input type="date" name="poczatek" required>
            <input type="date" name="koniec" required>
            <button type="submit">Oblicz</button>
        </form>
        <?php if (isset($_GET['poczatek'])): 
            $wynik = $kontroler->pobierzStatystykiMiedzyDatami($_GET['poczatek'], $_GET['koniec']); ?>
            <p class="stats">Wynik: Transakcji: <?= $wynik['count'] ?>, Suma: <?= $wynik['sum'] ?> PLN</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>3. Historia transakcji</h3>
        <table>
            <tr>
                <th>ID sprzedaży</th>
                <th>ID Klienta</th>
                <th>Produkt</th>
                <th>Cena</th>
                <th>Data</th>
            </tr>
            <?php foreach ($wszystkieSprzedaze as $sprzedaz): ?>
                <tr>
                    <td><?= htmlspecialchars($sprzedaz[0]) ?></td>
                    <td><?= htmlspecialchars($sprzedaz[1]) ?></td>
                    <td><?= htmlspecialchars($sprzedaz[2]) ?></td>
                    <td><?= htmlspecialchars($sprzedaz[3]) ?></td>
                    <td><?= htmlspecialchars($sprzedaz[4]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>


    <div class="powrot">
        <a href="../../index.html">Powrót do menu</a>
    </div>
</body>
</html>
