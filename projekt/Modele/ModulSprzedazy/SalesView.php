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
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bezpieczny ERP - Moduł Sprzedaży</title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/sales.css">
</head>
<body>

    <div class="container">
        <h1>Bezpieczny ERP - Moduł Sprzedaży</h1>

        <?php if ($wiadomosc): ?>
            <div class="message"><?php echo $wiadomosc; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>1. Dodaj transakcję</h2>
            <form method="POST">
                <input type="hidden" name="akcja" value="dodaj">
                <input type="text" name="id_klienta" placeholder="ID Klienta" required>
                <input type="text" name="produkt" placeholder="Produkt" required>
                <input type="number" name="kwota" placeholder="Kwota" step="0.01" required>
                <input type="date" name="data" required>
                <button type="submit">Zapisz do pliku</button>
            </form>
        </div>

        <div class="section">
            <h2>2. Statystyki okresowe</h2>
            <form method="GET">
                <input type="date" name="poczatek" required>
                <input type="date" name="koniec" required>
                <button type="submit">Oblicz</button>
            </form>
            <?php if (isset($_GET['poczatek'])):
                $wynik = $kontroler->pobierzStatystykiMiedzyDatami($_GET['poczatek'], $_GET['koniec']); ?>
                <p>Wynik: Transakcji: <?= $wynik['count'] ?>, Suma: <?= $wynik['sum'] ?> PLN</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>3. Historia transakcji</h2>
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
    </div>

</body>
</html>
<!-- 
<!DOCTYPE html>
<html>
<head>
    <title>Bezpieczny ERP</title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/sales.css">
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
</html> -->
