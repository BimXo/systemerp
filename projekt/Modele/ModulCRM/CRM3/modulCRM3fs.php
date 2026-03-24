<?php
// Pobierz ID z pliku tymczasowego
$id = trim(file_get_contents('id.txt'));
if (empty($id)) {
    die("Brak ID rekordu.");
}

// Pobierz dane z POST
$imie = trim($_POST['imie'] ?? '');
$email = trim($_POST['mail'] ?? '');
$sub = trim($_POST['sub'] ?? '');

// Walidacja
$bledy = [];
if (empty($imie) || !preg_match('/^[a-zA-Z\s]+$/', $imie)) {
    $bledy[] = "Imię może zawierać tylko litery i spacje.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $bledy[] = "Niepoprawny adres email.";
}
if (empty($sub)) {
    $bledy[] = "Subskrypcje są wymagane.";
}

if (!empty($bledy)) {
    $wiadomosc = "Błędy: " . implode(' ', $bledy);
} else {
    // Znajdź i zaktualizuj rekord
    $plik = '../crm.txt';
    $linie = file_exists($plik) ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $znaleziono = false;
    foreach ($linie as $indeks => $linia) {
        $tablica = explode(';', $linia);
        if (isset($tablica[0]) && $tablica[0] == $id) {
            $tablica[1] = $imie;
            $tablica[2] = $email;
            $tablica[3] = $sub;
            $linie[$indeks] = implode(';', $tablica);
            $znaleziono = true;
            break;
        }
    }

    if ($znaleziono) {
        file_put_contents($plik, implode("\n", $linie) . "\n");
        $wiadomosc = "Zapisano dane: $imie, $email, $sub";
    } else {
        $wiadomosc = "Nie znaleziono rekordu do zapisania.";
    }
}

// Usuń plik tymczasowy
unlink('id.txt');
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Zapis</title>
    <link rel="stylesheet" href="../../css/crm.css">
</head>
<body class="crm crm-3-fs">
<header>
    <h1>CRM - Zapis rekordu</h1>
    <nav>
        <a href="modulCRM3.php">Wyszukaj ponownie</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <div class="message"><?php echo htmlspecialchars($wiadomosc); ?></div>
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Zapis</title>
    <link rel="stylesheet" href="../../css/crm.css">
</head>
<body class="crm crm-3-fs">
<header>
    <h1>CRM - Zapis rekordu</h1>
    <nav>
        <a href="modulCRM3.php">Wyszukaj ponownie</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <div class="message"><?php echo htmlspecialchars($wiadomosc); ?></div>
    </div>
</div>

</body>
</html>