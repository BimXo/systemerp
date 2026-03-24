<?php
// Pobierz dane z POST
$imie = trim($_POST['imie'] ?? '');
$email = trim($_POST['mail'] ?? '');
$subskrypcje = $_POST['sub'] ?? [];

// Walidacja
$bledy = [];
if (empty($imie) || !preg_match('/^[a-zA-Z\s]+$/', $imie)) {
    $bledy[] = "Imię może zawierać tylko litery i spacje.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $bledy[] = "Niepoprawny adres email.";
}
if (empty($subskrypcje)) {
    $bledy[] = "Wybierz przynajmniej jedną subskrypcję.";
}

if (empty($bledy)) {
    // Znajdź następne ID
    $plik = '../crm.txt';
    $linie = file_exists($plik) ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $maxId = 0;
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (isset($dane[0]) && is_numeric($dane[0])) {
            $maxId = max($maxId, (int)$dane[0]);
        }
    }
    $id = $maxId + 1;

    // Przygotuj subskrypcje
    $subskrypcjaStr = implode(', ', $subskrypcje);

    // Zapisz do pliku
    $tekst = $id . ';' . $imie . ';' . $email . ';' . $subskrypcjaStr . "\n";
    file_put_contents($plik, $tekst, FILE_APPEND);

    $wiadomosc = "Zapisano dane: ID $id, $imie, $email, Subskrypcje: $subskrypcjaStr";
    $messageClass = "success";
} else {
    $wiadomosc = "Błędy: " . implode(' ', $bledy);
    $messageClass = "error";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Zapis</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-1-submit">
<header>
    <h1>CRM - Zapis danych</h1>
    <nav>
        <a href="modulCRM1.php">Powrót do formularza</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container result-page">
    <div class="card">
        <div class="message <?php echo $messageClass; ?>">
            <div class="message-content"><?php echo htmlspecialchars($wiadomosc); ?></div>
        </div>
        <div class="result-actions">
            <a href="modulCRM1.php" class="btn-save">Dodaj kolejnego użytkownika</a>
            <a href="../modulCRM.php" class="btn-search">Powrót do menu</a>
        </div>
    </div>
</div>

</body>
</html>
