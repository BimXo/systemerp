<?php
require_once '../szyfrowanie.php';

// ── Dane z POST ─────────────────────────────────────────────────────────────
$imie        = trim($_POST['imie'] ?? '');
$email       = trim($_POST['mail'] ?? '');
$subskrypcje = $_POST['sub'] ?? [];

// ── Ścieżka do bazy ─────────────────────────────────────────────────────────
$plik = '../crm.txt';

// ── Walidacja ────────────────────────────────────────────────────────────────
$bledy = [];
if (empty($imie) || !preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $imie)) {
    $bledy[] = "Imię może zawierać tylko litery i spacje.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $bledy[] = "Niepoprawny adres e-mail.";
}
if (empty($subskrypcje)) {
    $bledy[] = "Wybierz przynajmniej jedną subskrypcję.";
}

if (empty($bledy)) {
    // Wczytaj istniejące ID (odszyfrowane) do zbioru
    $linie = file_exists($plik)
        ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
 
    $istniejaceId = [];
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (isset($dane[0])) {
            $istniejaceId[] = (int)cezar($dane[0], -3);
        }
    }
 
    // Losuj ID z zakresu 1000–9999 dopóki nie trafi się unikalne
    do {
        $id = rand(1000, 9999);
    } while (in_array($id, $istniejaceId, true));
 

    file_put_contents($plik, $wiersz, FILE_APPEND);

    $wiadomosc    = "Zapisano klienta: ID $id | $imie | $email | Subskrypcje: $subskrypcje";
    $messageClass = "success";
} else {
    $wiadomosc    = "Błędy formularza: " . implode(' ', $bledy);
    $messageClass = "error";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Zapis klienta</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-1-submit">
<header>
    <h1>CRM – Zapis klienta</h1>
    <nav class="menu">
        <a href="modulCRM1.php">Dodaj kolejnego</a>
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
            <a href="modulCRM1.php" class="btn-save">Dodaj kolejnego klienta</a>
            &nbsp;
            <a href="../modulCRM.php" class="btn-search">Powrót do menu</a>
        </div>
    </div>
</div>
</body>
</html>
