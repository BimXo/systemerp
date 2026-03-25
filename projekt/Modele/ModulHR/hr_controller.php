<?php
require_once "hr_model.php";

$model     = new ModelHR();
$wiadomosc = '';

// ── OBSŁUGA AKCJI POST ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    switch ($akcja) {
        case 'dodaj':
            $id = $model->utworz(
                trim($_POST['imie']),
                $_POST['dataUrodzenia'],
                trim($_POST['dzial']),
                (int)$_POST['poziom']
            );
            $wiadomosc = "✅ Pracownik #$id został dodany.";
            break;

        case 'edytuj':
            $ok = $model->aktualizuj(
                (int)$_POST['id'],
                trim($_POST['imie']),
                $_POST['dataUrodzenia'],
                trim($_POST['dzial']),
                (int)$_POST['poziom']
            );
            $wiadomosc = $ok
                ? "✅ Dane pracownika #{$_POST['id']} zaktualizowane."
                : "❌ Nie znaleziono pracownika o ID {$_POST['id']}.";
            break;

        case 'usun':
            $ok = $model->usun((int)$_POST['id']);
            $wiadomosc = $ok
                ? "✅ Pracownik #{$_POST['id']} został usunięty."
                : "❌ Nie znaleziono pracownika o ID {$_POST['id']}.";
            break;
    }
}

// ── DANE DO WIDOKU ──────────────────────────────────────────────────────────
$wszyscyPracownicy    = $model->pobierzWszystkich();
$najstarszyNajmlodszy = $model->pobierzNajstarszegoINajmlodszego();
$sredniWiek           = $model->pobierzSredniWiek();
$liczbaPoDzialach     = $model->policzPoDzialach();

// Tryb edycji – załaduj dane pracownika do formularza
$edytowany = null;
if (isset($_GET['edytuj'])) {
    $edytowany = $model->pobierzPoId((int)$_GET['edytuj']);
}

// Urodziny – obsługa przez GET, żeby nie kolidowała z POST
$urodziny = [];
if (isset($_GET['data_urodzin']) && $_GET['data_urodzin'] !== '') {
    $urodziny = $model->pobierzNadchodzaceUrodziny($_GET['data_urodzin']);
}

// Poziom – obsługa przez GET
$liczbaPoPoziomie = null;
if (isset($_GET['minimalny_poziom']) && $_GET['minimalny_poziom'] !== '') {
    $liczbaPoPoziomie = $model->policzPoPoziomie((int)$_GET['minimalny_poziom']);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moduł HR</title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/hr.css">
</head>
<body>
<div class="container">
    <h1>Moduł HR – Zarządzanie Pracownikami</h1>

    <?php if ($wiadomosc): ?>
        <div class="message"><?= htmlspecialchars($wiadomosc) ?></div>
    <?php endif; ?>

    <!-- ── 1. FORMULARZ DODAWANIA / EDYCJI (CREATE + UPDATE) ───────────── -->
    <div class="section">
        <h2><?= $edytowany
                ? 'Edytuj pracownika #' . htmlspecialchars($edytowany['id'])
                : '1. Dodaj pracownika' ?></h2>
        <form method="POST">
            <input type="hidden" name="akcja" value="<?= $edytowany ? 'edytuj' : 'dodaj' ?>">
            <?php if ($edytowany): ?>
                <input type="hidden" name="id" value="<?= (int)$edytowany['id'] ?>">
            <?php endif; ?>

            <input type="text"   name="imie"          placeholder="Imię"          required
                   value="<?= htmlspecialchars($edytowany['name']       ?? '') ?>">
            <input type="date"   name="dataUrodzenia"                              required
                   value="<?= htmlspecialchars($edytowany['birthdate']  ?? '') ?>">
            <input type="text"   name="dzial"          placeholder="Dział"         required
                   value="<?= htmlspecialchars($edytowany['department'] ?? '') ?>">
            <input type="number" name="poziom"         placeholder="Poziom"        required min="1"
                   value="<?= htmlspecialchars($edytowany['level']      ?? '') ?>">

            <button type="submit"><?= $edytowany ? 'Zapisz zmiany' : 'Dodaj pracownika' ?></button>
            <?php if ($edytowany): ?>
                <a href="hr_controller.php" class="btn-cancel">Anuluj</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ── 2. LISTA PRACOWNIKÓW (READ) ─────────────────────────────────── -->
    <div class="section">
        <h2>2. Lista pracowników</h2>
        <?php if (empty($wszyscyPracownicy)): ?>
            <p>Brak pracowników w bazie.</p>
        <?php else: ?>
        <table>
            <tr>
                <th>ID</th><th>Imię</th><th>Data urodzenia</th><th>Dział</th><th>Poziom</th><th>Akcje</th>
            </tr>
            <?php foreach ($wszyscyPracownicy as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['birthdate']) ?></td>
                <td><?= htmlspecialchars($p['department']) ?></td>
                <td><?= htmlspecialchars($p['level']) ?></td>
                <td class="akcje">
                    <a href="?edytuj=<?= (int)$p['id'] ?>" class="btn-edit">Edytuj</a>
                    <form method="POST" style="display:inline"
                          onsubmit="return confirm('Usunąć pracownika <?= htmlspecialchars(addslashes($p['name'])) ?>?')">
                        <input type="hidden" name="akcja" value="usun">
                        <input type="hidden" name="id"    value="<?= (int)$p['id'] ?>">
                        <button type="submit" class="btn-delete">Usuń</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <!-- ── 3. NAJSTARSZY I NAJMŁODSZY (punkt 2) ────────────────────────── -->
    <div class="section">
        <h2>3. Najstarszy i najmłodszy pracownik</h2>
        <p>Najstarszy: <strong><?= htmlspecialchars($najstarszyNajmlodszy['oldest']   ?? 'Brak danych') ?></strong></p>
        <p>Najmłodszy: <strong><?= htmlspecialchars($najstarszyNajmlodszy['youngest'] ?? 'Brak danych') ?></strong></p>
    </div>

    <!-- ── 4. ŚREDNI WIEK (punkt 3) ─────────────────────────────────────── -->
    <div class="section">
        <h2>4. Średni wiek pracowników</h2>
        <p>Średni wiek: <strong><?= number_format($sredniWiek, 2, ',', '') ?> lat</strong></p>
    </div>

    <!-- ── 5. URODZINY (punkt 4) ────────────────────────────────────────── -->
    <div class="section">
        <h2>5. Nadchodzące urodziny (±14 dni od daty)</h2>
        <form method="GET">
            <input type="date" name="data_urodzin"
                   value="<?= htmlspecialchars($_GET['data_urodzin'] ?? '') ?>">
            <button type="submit">Sprawdź</button>
        </form>
        <?php if (isset($_GET['data_urodzin'])): ?>
            <?php if (empty($urodziny)): ?>
                <p>Brak pracowników z urodzinami w tym okresie.</p>
            <?php else: ?>
                <table>
                    <tr><th>Imię</th><th>Data urodzenia</th><th>Dział</th></tr>
                    <?php foreach ($urodziny as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['birthdate']) ?></td>
                        <td><?= htmlspecialchars($p['department']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- ── 6. POZIOM UPRAWNIEŃ (punkt 5) ────────────────────────────────── -->
    <div class="section">
        <h2>6. Pracownicy z poziomem uprawnień ≥</h2>
        <form method="GET">
            <input type="number" name="minimalny_poziom" placeholder="Minimalny poziom" min="1"
                   value="<?= htmlspecialchars($_GET['minimalny_poziom'] ?? '') ?>">
            <button type="submit">Sprawdź</button>
        </form>
        <?php if ($liczbaPoPoziomie !== null): ?>
            <p>Liczba pracowników: <strong><?= $liczbaPoPoziomie ?></strong></p>
        <?php endif; ?>
    </div>

    <!-- ── 7. LICZBA NA DZIAŁ (punkt 6) ─────────────────────────────────── -->
    <div class="section">
        <h2>7. Liczba pracowników na dział</h2>
        <?php if (empty($liczbaPoDzialach)): ?>
            <p>Brak danych.</p>
        <?php else: ?>
        <table>
            <tr><th>Dział</th><th>Liczba pracowników</th></tr>
            <?php foreach ($liczbaPoDzialach as $dzial => $liczba): ?>
            <tr>
                <td><?= htmlspecialchars($dzial) ?></td>
                <td><?= (int)$liczba ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <div class="powrot">
        <a href="../../index.php">Powrót do menu</a>
    </div>
</div>
</body>
</html>
