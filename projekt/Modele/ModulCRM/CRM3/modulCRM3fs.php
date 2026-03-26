<?php
// BŁĄD ORYGINAŁU 1: require('cezar.php') – plik nie istnieje; poprawna nazwa to szyfrowanie.php
require_once '../szyfrowanie.php';

// ── ID przekazywane przez ukryte pole POST (nie przez plik id.txt) ──────────
// BŁĄD ORYGINAŁU 2: id.txt przechowywał niezaszyfrowane ID, ale w pliku crm.txt
// ID jest zaszyfrowane, więc porównanie nigdy nie zachodziło.
// Teraz ID płynie bezpośrednio przez POST i jest jawną liczbą.
$id = (int)trim($_POST['id'] ?? 0);
if ($id <= 0) {
    die("Brak lub niepoprawne ID rekordu.");
}

// ── Dane z formularza ────────────────────────────────────────────────────────
$imie  = trim($_POST['imie'] ?? '');
$email = trim($_POST['mail'] ?? '');
$sub   = trim($_POST['sub']  ?? '');

// ── Walidacja ────────────────────────────────────────────────────────────────
$bledy = [];
if (empty($imie) || !preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $imie)) {
    $bledy[] = "Imię może zawierać tylko litery i spacje.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $bledy[] = "Niepoprawny adres e-mail.";
}
if (empty($sub)) {
    $bledy[] = "Pole subskrypcje jest wymagane.";
}

if (!empty($bledy)) {
    $wiadomosc    = "Błędy: " . implode(' ', $bledy);
    $messageClass = "error";
} else {
    $plik  = '../crm.txt';
    $linie = file_exists($plik)
        ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];

    $znaleziono = false;
    foreach ($linie as $indeks => $linia) {
        $tablica = explode(';', $linia);
        // Porównaj odszyfrowane ID z pliku z przekazanym ID
        if (isset($tablica[0]) && (int)cezar($tablica[0], -3) === $id) {
            $tablica[0] = cezar((string)$id, 3); // ID pozostaje takie samo
            $tablica[1] = cezar($imie, 3);
            $tablica[2] = cezar($email, 3);
            $tablica[3] = cezar($sub, 3);
            $linie[$indeks] = implode(';', $tablica);
            $znaleziono = true;
            break;
        }
    }

    if ($znaleziono) {
        file_put_contents($plik, implode("\n", $linie) . "\n");
        $wiadomosc    = "Zaktualizowano klienta ID $id: $imie | $email | $sub";
        $messageClass = "success";
    } else {
        $wiadomosc    = "Nie znaleziono rekordu o ID $id do zapisania.";
        $messageClass = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Zapis edycji</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-3-fs">
<header>
    <h1>CRM – Zapis edycji</h1>
    <nav class="menu">
        <a href="modulCRM3.php">Edytuj kolejny</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <div class="message <?= $messageClass ?>">
            <div class="message-content"><?= htmlspecialchars($wiadomosc) ?></div>
        </div>
        <div class="powrot">
            <a href="modulCRM3.php" class="btn-search">Edytuj kolejny rekord</a>
            &nbsp;
            <a href="../modulCRM.php" class="btn-save">Powrót do menu</a>
        </div>
    </div>
</div>
</body>
</html>
