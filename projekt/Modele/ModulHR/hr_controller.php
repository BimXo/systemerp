<?php
require_once "hr_model.php";

$model = new ModelHR();

// Obsługa akcji POST
$wiadomosc = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akcja'])) {
        switch ($_POST['akcja']) {
            case 'dodaj':
                $model->utworz($_POST['imie'], $_POST['dataUrodzenia'], $_POST['dzial'], $_POST['poziom']);
                $wiadomosc = "Pracownik dodany!";
                break;
            case 'usun':
                $model->usun($_POST['id']);
                $wiadomosc = "Pracownik usunięty!";
                break;
        }
    }
}

// Pobieranie danych do wyświetlenia
$wszyscyPracownicy = $model->pobierzWszystkich();
$najstarszyNajmlodszy = $model->pobierzNajstarszegoINajmlodszego();
$sredniWiek = $model->pobierzSredniWiek();
$liczbaPoDzialach = $model->policzPoDzialach();

// Obsługa urodzin
$urodziny = [];
if (isset($_POST['data_urodzin'])) {
    $urodziny = $model->pobierzNadchodzaceUrodziny($_POST['data_urodzin']);
}

// Obsługa poziomu
$liczbaPoPoziomie = "";
if (isset($_POST['minimalny_poziom'])) {
    $liczbaPoPoziomie = $model->policzPoPoziomie($_POST['minimalny_poziom']);
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
        <h1>Moduł HR - Zarządzanie Pracownikami</h1>

        <?php if ($wiadomosc): ?>
            <div class="message"><?php echo $wiadomosc; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>1. Dodaj pracownika</h2>
            <form method="POST">
                <input type="hidden" name="akcja" value="dodaj">
                <input type="text" name="imie" placeholder="Imię" required>
                <input type="date" name="dataUrodzenia" placeholder="Data urodzenia" required>
                <input type="text" name="dzial" placeholder="Dział" required>
                <input type="number" name="poziom" placeholder="Poziom" required>
                <button type="submit">Dodaj</button>
            </form>
        </div>

        <div class="section">
            <h2>2. Lista pracowników</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Data urodzenia</th>
                    <th>Dział</th>
                    <th>Poziom</th>
                </tr>
                <?php foreach ($wszyscyPracownicy as $pracownik): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pracownik['id']); ?></td>
                    <td><?php echo htmlspecialchars($pracownik['name']); ?></td>
                    <td><?php echo htmlspecialchars($pracownik['birthdate']); ?></td>
                    <td><?php echo htmlspecialchars($pracownik['department']); ?></td>
                    <td><?php echo htmlspecialchars($pracownik['level']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="section">
            <h2>3. Usuń pracownika</h2>
            <form method="POST">
                <input type="hidden" name="akcja" value="usun">
                <input type="text" name="id" placeholder="ID pracownika" required>
                <button type="submit">Usuń</button>
            </form>
        </div>

        <div class="section">
            <h2>4. Najstarszy i najmłodszy</h2>
            <p>Najstarszy: <?php echo htmlspecialchars($najstarszyNajmlodszy['oldest'] ?? 'Brak danych'); ?></p>
            <p>Najmłodszy: <?php echo htmlspecialchars($najstarszyNajmlodszy['youngest'] ?? 'Brak danych'); ?></p>
        </div>

        <div class="section">
            <h2>5. Średni wiek</h2>
            <p>Średni wiek: <?php echo round($sredniWiek, 2); ?> lat</p>
        </div>

        <div class="section">
            <h2>6. Urodziny (w ciągu 2 tygodni od podanej daty)</h2>
            <form method="POST">
                <input type="date" name="data_urodzin" required>
                <button type="submit">Sprawdź</button>
            </form>
            <?php if ($urodziny): ?>
                <table>
                    <tr>
                        <th>Imię</th>
                        <th>Data urodzenia</th>
                        <th>Dział</th>
                    </tr>
                    <?php foreach ($urodziny as $pracownik): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pracownik['name']); ?></td>
                        <td><?php echo htmlspecialchars($pracownik['birthdate']); ?></td>
                        <td><?php echo htmlspecialchars($pracownik['department']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>7. Liczba pracowników z poziomem >=</h2>
            <form method="POST">
                <input type="number" name="minimalny_poziom" placeholder="Minimalny poziom" required>
                <button type="submit">Sprawdź</button>
            </form>
            <?php if ($liczbaPoPoziomie !== ""): ?>
                <p>Liczba: <?php echo $liczbaPoPoziomie; ?></p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>8. Liczba na dział</h2>
            <table>
                <tr>
                    <th>Dział</th>
                    <th>Liczba pracowników</th>
                </tr>
                <?php foreach ($liczbaPoDzialach as $dzial => $liczba): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dzial); ?></td>
                    <td><?php echo $liczba; ?></td>
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