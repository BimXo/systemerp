<?php
require_once 'SalesModel.php';
require_once 'SalesController.php';

$model     = new ModelSprzedazy();
$kontroler = new KontrolerSprzedazy($model);
$wiadomosc = '';

// ── OBSŁUGA AKCJI POST ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'dodaj') {
        $id = $kontroler->model->utworz(
            trim($_POST['id_klienta']),
            trim($_POST['produkt']),
            $_POST['kwota'],
            $_POST['data']
        );
        $wiadomosc = "✅ Transakcja #$id została dodana.";

    } elseif ($akcja === 'edytuj') {
        $ok = $kontroler->model->aktualizuj(
            $_POST['id'],
            trim($_POST['id_klienta']),
            trim($_POST['produkt']),
            $_POST['kwota'],
            $_POST['data']
        );
        $wiadomosc = $ok ? "✅ Transakcja #{$_POST['id']} zaktualizowana." : "❌ Nie znaleziono transakcji.";

    } elseif ($akcja === 'usun') {
        $ok = $kontroler->model->usun($_POST['id']);
        $wiadomosc = $ok ? "✅ Transakcja #{$_POST['id']} usunięta." : "❌ Nie znaleziono transakcji.";
    }
}

// ── DANE DO WIDOKU ──────────────────────────────────────────────────────────
$wszystkieSprzedaze   = $kontroler->model->pobierzWszystkie();
$najwyzszaTransakcja  = $kontroler->pobierzNajwyzszaTransakcjaPrzychod();
$najlepszyProdukt     = $kontroler->pobierzNajwyzszyPrzychodProdukt();

// Tryb edycji – załaduj dane do formularza
$edytowana = null;
if (isset($_GET['edytuj'])) {
    $edytowana = $kontroler->model->pobierzPoId((int)$_GET['edytuj']);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bezpieczny ERP – Moduł Sprzedaży</title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/sales.css">
</head>
<body>
<div class="container">
    <h1>Bezpieczny ERP – Moduł Sprzedaży</h1>

    <?php if ($wiadomosc): ?>
        <div class="message"><?= htmlspecialchars($wiadomosc) ?></div>
    <?php endif; ?>

    <!-- ── 1. FORMULARZ DODAWANIA / EDYCJI ─────────────────────────────── -->
    <div class="section">
        <h2><?= $edytowana ? 'Edytuj transakcję #' . htmlspecialchars($edytowana[0]) : '1. Dodaj transakcję' ?></h2>
        <form method="POST">
            <input type="hidden" name="akcja" value="<?= $edytowana ? 'edytuj' : 'dodaj' ?>">
            <?php if ($edytowana): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($edytowana[0]) ?>">
            <?php endif; ?>

            <input type="text"   name="id_klienta" placeholder="ID Klienta" required
                   value="<?= htmlspecialchars($edytowana[1] ?? '') ?>">
            <input type="text"   name="produkt"    placeholder="Produkt"    required
                   value="<?= htmlspecialchars($edytowana[2] ?? '') ?>">
            <input type="number" name="kwota"      placeholder="Kwota"      step="0.01" required
                   value="<?= htmlspecialchars($edytowana[3] ?? '') ?>">
            <input type="date"   name="data"       required
                   value="<?= htmlspecialchars($edytowana[4] ?? '') ?>">

            <button type="submit"><?= $edytowana ? 'Zapisz zmiany' : 'Zapisz transakcję' ?></button>
            <?php if ($edytowana): ?>
                <a href="SalesView.php" class="btn-cancel">Anuluj</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ── 2. STATYSTYKI MIĘDZY DATAMI ─────────────────────────────────── -->
    <div class="section">
        <h2>2. Statystyki okresowe</h2>
        <form method="GET">
            <input type="date" name="poczatek" value="<?= htmlspecialchars($_GET['poczatek'] ?? '') ?>" required>
            <input type="date" name="koniec"   value="<?= htmlspecialchars($_GET['koniec']   ?? '') ?>" required>
            <button type="submit">Oblicz</button>
        </form>
        <?php if (isset($_GET['poczatek'], $_GET['koniec'])): ?>
            <?php $wynik = $kontroler->pobierzStatystykiMiedzyDatami($_GET['poczatek'], $_GET['koniec']); ?>
            <p class="stats">
                Liczba transakcji: <strong><?= $wynik['count'] ?></strong> &nbsp;|&nbsp;
                Suma przychodów: <strong><?= number_format($wynik['sum'], 2, ',', ' ') ?> PLN</strong>
            </p>
        <?php endif; ?>
    </div>

    <!-- ── 3. NAJWYŻSZA TRANSAKCJA ─────────────────────────────────────── -->
    <div class="section">
        <h2>3. Transakcja z największym przychodem</h2>
        <?php if ($najwyzszaTransakcja): ?>
            <p class="stats">
                ID: <strong><?= htmlspecialchars($najwyzszaTransakcja[0]) ?></strong> &nbsp;|&nbsp;
                Produkt: <strong><?= htmlspecialchars($najwyzszaTransakcja[2]) ?></strong> &nbsp;|&nbsp;
                Kwota: <strong><?= number_format((float)$najwyzszaTransakcja[3], 2, ',', ' ') ?> PLN</strong> &nbsp;|&nbsp;
                Data: <strong><?= htmlspecialchars($najwyzszaTransakcja[4]) ?></strong>
            </p>
        <?php else: ?>
            <p>Brak danych.</p>
        <?php endif; ?>
    </div>

    <!-- ── 4. NAJLEPSZY PRODUKT ─────────────────────────────────────────── -->
    <div class="section">
        <h2>4. Produkt z największym łącznym przychodem</h2>
        <?php if ($najlepszyProdukt): ?>
            <p class="stats">
                Produkt: <strong><?= htmlspecialchars($najlepszyProdukt['produkt']) ?></strong> &nbsp;|&nbsp;
                Łączny przychód: <strong><?= number_format($najlepszyProdukt['suma'], 2, ',', ' ') ?> PLN</strong>
            </p>
        <?php else: ?>
            <p>Brak danych.</p>
        <?php endif; ?>
    </div>

    <!-- ── 5. HISTORIA TRANSAKCJI (READ + UPDATE + DELETE) ─────────────── -->
    <div class="section">
        <h2>5. Historia transakcji</h2>
        <?php if (empty($wszystkieSprzedaze)): ?>
            <p>Brak transakcji w bazie.</p>
        <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>ID Klienta</th>
                <th>Produkt</th>
                <th>Kwota (PLN)</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
            <?php foreach ($wszystkieSprzedaze as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s[0]) ?></td>
                <td><?= htmlspecialchars($s[1]) ?></td>
                <td><?= htmlspecialchars($s[2]) ?></td>
                <td><?= number_format((float)$s[3], 2, ',', ' ') ?></td>
                <td><?= htmlspecialchars($s[4]) ?></td>
                <td class="akcje">
                    <a href="?edytuj=<?= (int)$s[0] ?>" class="btn-edit">Edytuj</a>
                    <form method="POST" style="display:inline"
                          onsubmit="return confirm('Usunąć transakcję #<?= (int)$s[0] ?>?')">
                        <input type="hidden" name="akcja" value="usun">
                        <input type="hidden" name="id"    value="<?= (int)$s[0] ?>">
                        <button type="submit" class="btn-delete">Usuń</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <div class="powrot">
        <a href="../../index.html">← Powrót do menu</a>
    </div>
</div>
</body>
</html>
