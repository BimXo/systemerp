<?php
// Pobierz ID z POST
$id = trim($_POST['id'] ?? '');
if (empty($id) || !is_numeric($id)) {
    die("Niepoprawne ID.");
}

// Znajdź rekord
$plik = '../crm.txt';
$linie = file_exists($plik) ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$rekord = null;
foreach ($linie as $linia) {
    $dane = explode(';', $linia);
    if (isset($dane[0]) && $dane[0] == $id) {
        $rekord = [
            'id' => $dane[0],
            'imie' => $dane[1] ?? '',
            'email' => $dane[2] ?? '',
            'subskrypcje' => $dane[3] ?? ''
        ];
        break;
    }
}

if (!$rekord) {
    die("Nie znaleziono rekordu o ID $id.");
}

// Zapisz ID do pliku tymczasowego
file_put_contents('id.txt', $id);

$wiadomosc = "Znaleziono rekord. Uzupełnij dane i zapisz.";
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Edycja rekordu</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-3-submit">
<header>
    <h1>CRM - Edycja rekordu</h1>
    <nav>
        <a href="modulCRM3.php">Wyszukaj ponownie</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container result-page">
    <div class="card">
        <div class="message info">
            <div class="message-content"><?php echo htmlspecialchars($wiadomosc); ?></div>
        </div>
        <form method="post" action="modulCRM3fs.php">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" value="<?php echo htmlspecialchars($rekord['imie']); ?>" required>
            
            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($rekord['email']); ?>" required>
            
            <label for="sub">Subskrypcje:</label>
            <input type="text" id="sub" name="sub" value="<?php echo htmlspecialchars($rekord['subskrypcje']); ?>" required>
            
            <div class="result-actions">
                <button type="submit" class="btn-save">Zapisz zmiany</button>
                <a href="modulCRM3.php" class="btn-search">Anuluj</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>