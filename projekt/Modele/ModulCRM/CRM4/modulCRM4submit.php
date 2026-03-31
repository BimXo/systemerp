<?php
require_once '../szyfrowanie.php';

//Walidacja ID
$id = trim($_POST['id'] ?? '');
if (empty($id) || !ctype_digit($id) || (int)$id <= 0) {
    die("Niepoprawne ID.");
}
$id = (int)$id;

// Usuń rekord
//odszyfruj $tablica[0] przed porównaniem.
$plik    = '../crm.txt';
$linie   = file_exists($plik)
    ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    : [];

$nowe     = [];
$usunieto = false;

foreach ($linie as $linia) {
    $tablica = explode(';', $linia);
    if (isset($tablica[0]) && (int)cezar($tablica[0], -3) === $id) {
        $usunieto = true;   // pomiń ten wiersz – to jest rekord do usunięcia
    } else {
        $nowe[] = $linia;
    }
}

if ($usunieto) {
    file_put_contents($plik, implode("\n", $nowe) . (empty($nowe) ? '' : "\n"));
    $wiadomosc    = "Usunięto klienta o ID: $id.";
    $messageClass = "success";
} else {
    $wiadomosc    = "Nie znaleziono klienta o ID: $id.";
    $messageClass = "error";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Usunięcie klienta</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-4-submit">
<header>
    <h1>CRM – Usunięcie klienta</h1>
    <nav class="menu">
        <a href="modulCRM4.php">Usuń kolejnego</a>
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
            <a href="modulCRM4.php" class="btn-delete">Usuń kolejnego klienta</a>
            &nbsp;
            <a href="../modulCRM.php" class="btn-save">Powrót do menu</a>
        </div>
    </div>
</div>
</body>
</html>
