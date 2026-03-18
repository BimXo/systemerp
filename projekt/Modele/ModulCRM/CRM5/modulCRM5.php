<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $linie = file('../../crm.txt');
    $nowe = [];
    foreach($linie as $linia){
        $tablica = explode(";", $linia);
        $nowe[] = $tablica[2] . "\n";
    }
    file_put_contents('email.txt', $nowe);
    $plik = 'email.txt';
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="email.txt"');
    header('Content-Length: ' . filesize($plik));
    readfile($plik);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Eksport email</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
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