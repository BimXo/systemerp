<?php
// BŁĄD ORYGINAŁU: require 'szyfrowanie.php' – plik jest poziom wyżej, nie w CRM5/
require_once '../szyfrowanie.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plik  = '../crm.txt';
    $linie = file_exists($plik)
        ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];

    $emaile = [];
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (isset($dane[2]) && $dane[2] !== '') {
            $emaile[] = cezar($dane[2], -3);
        }
    }

    // Wyślij jako plik do pobrania
    $zawartosc = implode("\n", $emaile) . "\n";
    header('Content-Type: text/plain; charset=UTF-8');
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
    <title>CRM – Eksport e-mail</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-5">
<header>
    <h1>CRM – Eksport listy e-mail</h1>
    <nav class="menu">
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2>Pobierz adresy e-mail klientów</h2>
        <p>Kliknij przycisk, aby pobrać listę wszystkich adresów e-mail zapisanych w systemie jako plik <code>emails.txt</code>.</p>
        <form method="POST">
            <button type="submit" class="btn-export">⬇ Pobierz listę e-mail</button>
        </form>

        <div class="powrot">
            <a href="../modulCRM.php">← Powrót do menu CRM</a>
        </div>
    </div>
</div>
</body>
</html>
