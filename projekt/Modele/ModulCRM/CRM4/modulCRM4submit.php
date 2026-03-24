<?php
// Pobierz ID
$id = trim($_POST['id'] ?? '');
if (empty($id) || !is_numeric($id)) {
    die("Niepoprawne ID.");
}

// Usuń rekord
$plik = '../crm.txt';
$linie = file_exists($plik) ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$nowe = [];
$usunieto = false;
foreach ($linie as $linia) {
    $tablica = explode(';', $linia);
    if (isset($tablica[0]) && $tablica[0] != $id) {
        $nowe[] = $linia;
    } else {
        $usunieto = true;
    }
}

if ($usunieto) {
    file_put_contents($plik, implode("\n", $nowe) . "\n");
    $wiadomosc = "Usunięto rekord o ID: $id";
} else {
    $wiadomosc = "Nie znaleziono rekordu o ID: $id";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Usuwanie</title>
    <link rel="stylesheet" href="../../css/crm.css">
</head>
<body class="crm crm-4-submit">
<header>
    <h1>CRM - Usuwanie rekordu</h1>
    <nav>
        <a href="modulCRM4.php">Usuń inny rekord</a>
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