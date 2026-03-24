<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz emaile z pliku CRM
    $plik = '../crm.txt';
    $linie = file_exists($plik) ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $emaile = [];
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (isset($dane[2]) && !empty($dane[2])) {
            $emaile[] = $dane[2];
        }
    }
    
    // Zapisz do pliku tymczasowego
    $zawartosc = implode("\n", $emaile) . "\n";
    file_put_contents('email.txt', $zawartosc);
    
    // Wyślij plik do pobrania
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="emails.txt"');
    header('Content-Length: ' . strlen($zawartosc));
    echo $zawartosc;
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Eksport email</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-5">
<header>
    <h1>CRM - Eksport listy email</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <p>Kliknij przycisk, aby pobrać listę adresów email (plik tekstowy).</p>
        <form method="POST">
            <button type="submit">Pobierz listę email</button>
        </form>
    </div>
</div>

</body>
</html>