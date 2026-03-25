
<?php
require_once 'functions.php';

// -------------------------------------------------------
// Obsługa akcji POST / GET przed wyjściem HTML
// -------------------------------------------------------

$wiadomosc      = '';
$messageClass   = '';
$wynikEdycji    = null;   // rekord znaleziony do edycji (opcja 3 krok 1)

// Eksport emaili musi nastąpić PRZED jakimkolwiek HTML
if (isset($_POST['akcja']) && $_POST['akcja'] === 'eksport') {
    crm_eksportEmaili(); // wysyła plik i exit
}

// Opcja 1 – Dodaj
if (isset($_POST['akcja']) && $_POST['akcja'] === 'dodaj') {
    $imie        = trim($_POST['imie'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $subskrypcje = $_POST['sub'] ?? [];
    $wynik       = crm_dodaj($imie, $email, $subskrypcje);
    $wiadomosc   = $wynik['wiadomosc'];
    $messageClass = $wynik['sukces'] ? 'success' : 'error';
}

// Opcja 3 krok 1 – Wyszukaj rekord do edycji
if (isset($_POST['akcja']) && $_POST['akcja'] === 'szukaj_edycja') {
    $szukajId    = trim($_POST['id_szukaj'] ?? '');
    if (!is_numeric($szukajId) || empty($szukajId)) {
        $wiadomosc    = 'Podaj poprawne ID rekordu.';
        $messageClass = 'error';
    } else {
        $wynikEdycji = crm_znajdzRekord($szukajId);
        if (!$wynikEdycji) {
            $wiadomosc    = "Nie znaleziono rekordu o ID $szukajId.";
            $messageClass = 'error';
        }
    }
}

// Opcja 3 krok 2 – Zapisz edycję
if (isset($_POST['akcja']) && $_POST['akcja'] === 'zapisz_edycja') {
    $id    = trim($_POST['id_edycja'] ?? '');
    $imie  = trim($_POST['imie'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sub   = trim($_POST['sub'] ?? '');
    $wynik = crm_edytuj($id, $imie, $email, $sub);
    $wiadomosc    = $wynik['wiadomosc'];
    $messageClass = $wynik['sukces'] ? 'success' : 'error';
}

// Opcja 4 – Usuń
if (isset($_POST['akcja']) && $_POST['akcja'] === 'usun') {
    $id           = trim($_POST['id_usun'] ?? '');
    $wynik        = crm_usun($id);
    $wiadomosc    = $wynik['wiadomosc'];
    $messageClass = $wynik['sukces'] ? 'success' : 'error';
}

// Pobierz aktualną listę klientów do wyświetlenia
$klienty = crm_pobierzWszystkich();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System CRM</title>
    <link rel="stylesheet" href="../../css/crm.css">
</head>
<body class="crm">

<header>
    <h1>System CRM</h1>
</header>

<div class="container">

    <?php if ($wiadomosc): ?>
        <div class="message <?php echo htmlspecialchars($messageClass); ?>">
            <div class="message-content"><?php echo htmlspecialchars($wiadomosc); ?></div>
        </div>
    <?php endif; ?>

    <!-- ===================== -->
    <!-- SEKCJA 1 – Dodaj      -->
    <!-- ===================== -->
    <div class="card">
        <h2>1. Dodaj użytkownika</h2>
        <form method="post" action="">
            <input type="hidden" name="akcja" value="dodaj">

            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie"
                   value="<?php echo htmlspecialchars($_POST['imie'] ?? ''); ?>"
                   required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>

            <label for="sub">Subskrypcje (przytrzymaj Ctrl dla wielu):</label>
            <select id="sub" name="sub[]" multiple required>
                <option value="Newsletter">Newsletter</option>
                <option value="Promocje">Promocje</option>
                <option value="Aktualności">Aktualności</option>
                <option value="Wydarzenia">Wydarzenia</option>
            </select>

            <br>
            <button type="submit" class="btn-save">Zapisz</button>
        </form>
    </div>

    <!-- ===================== -->
    <!-- SEKCJA 2 – Lista      -->
    <!-- ===================== -->
    <div class="card">
        <h2>2. Lista klientów</h2>
        <?php if (empty($klienty)): ?>
            <p>Brak danych klientów.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię</th>
                        <th>Email</th>
                        <th>Subskrypcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($klienty as $klient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($klient['id']); ?></td>
                            <td><?php echo htmlspecialchars($klient['imie']); ?></td>
                            <td><?php echo htmlspecialchars($klient['email']); ?></td>
                            <td><?php echo htmlspecialchars($klient['subskrypcje']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ========================== -->
    <!-- SEKCJA 3 – Edytuj rekord   -->
    <!-- ========================== -->
    <div class="card">
        <h2>3. Edytuj rekord</h2>

        <!-- Krok 1: wyszukaj -->
        <form method="post" action="">
            <input type="hidden" name="akcja" value="szukaj_edycja">
            <label for="id_szukaj">ID rekordu:</label>
            <input type="number" id="id_szukaj" name="id_szukaj"
                   value="<?php echo htmlspecialchars($_POST['id_szukaj'] ?? ''); ?>"
                   placeholder="np. 1" required>
            <button type="submit" class="btn-search">Wyszukaj</button>
        </form>

        <!-- Krok 2: formularz edycji (widoczny tylko gdy znaleziono rekord) -->
        <?php if ($wynikEdycji): ?>
            <hr>
            <p class="message info">
                <span class="message-content">Znaleziono rekord. Uzupełnij dane i zapisz.</span>
            </p>
            <form method="post" action="">
                <input type="hidden" name="akcja"     value="zapisz_edycja">
                <input type="hidden" name="id_edycja" value="<?php echo htmlspecialchars($wynikEdycji['id']); ?>">

                <label for="imie_ed">Imię:</label>
                <input type="text" id="imie_ed" name="imie"
                       value="<?php echo htmlspecialchars($wynikEdycji['imie']); ?>" required>

                <label for="email_ed">Email:</label>
                <input type="email" id="email_ed" name="email"
                       value="<?php echo htmlspecialchars($wynikEdycji['email']); ?>" required>

                <label for="sub_ed">Subskrypcje:</label>
                <input type="text" id="sub_ed" name="sub"
                       value="<?php echo htmlspecialchars($wynikEdycji['subskrypcje']); ?>" required>

                <br>
                <button type="submit" class="btn-save">Zapisz zmiany</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- ===================== -->
    <!-- SEKCJA 4 – Usuń       -->
    <!-- ===================== -->
    <div class="card">
        <h2>4. Usuń rekord</h2>
        <form method="post" action="">
            <input type="hidden" name="akcja" value="usun">
            <label for="id_usun">ID rekordu:</label>
            <input type="number" id="id_usun" name="id_usun"
                   placeholder="np. 2" required>
            <button type="submit" class="btn-delete">Usuń</button>
        </form>
    </div>

    <!-- ===================== -->
    <!-- SEKCJA 5 – Eksport    -->
    <!-- ===================== -->
    <div class="card">
        <h2>5. Eksport listy email</h2>
        <p>Kliknij przycisk, aby pobrać listę adresów email jako plik tekstowy.</p>
        <form method="post" action="">
            <input type="hidden" name="akcja" value="eksport">
            <button type="submit" class="btn-export">Pobierz listę email</button>
        </form>
    </div>

    <div class="powrot">
        <a href="../../index.html">Powrót do strony głównej</a>
    </div>

</div><!-- /.container -->

</body>
</html>
