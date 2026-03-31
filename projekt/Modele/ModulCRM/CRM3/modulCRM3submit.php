<?php
require_once '../szyfrowanie.php';

// ── Pobierz i waliduj ID ────────────────────────────────────────────────────
$id = trim($_POST['id'] ?? '');
if (empty($id) || !ctype_digit($id) || (int)$id <= 0) {
    die("Niepoprawne ID.");
}
$id = (int)$id;

// ── Szukaj rekordu ──────────────────────────────────────────────────────────
$plik   = '../crm.txt';
$linie  = file_exists($plik)
    ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    : [];

$rekord = null;
foreach ($linie as $linia) {
    $dane = explode(';', $linia);
    if (count($dane) >= 4 && (int)cezar($dane[0], -3) === $id) {
        $rekord = [
            'id'          => $id,
            'imie'        => cezar($dane[1], -3),
            'email'       => cezar($dane[2], -3),
            'subskrypcje' => cezar($dane[3], -3),
        ];
        break;
    }
}

if (!$rekord) {
    // Pokaż czytelny komunikat zamiast die()
    $blad = "Nie znaleziono klienta o ID $id.";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Edycja klienta</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-3-submit">
<header>
    <h1>CRM – Edycja klienta</h1>
    <nav class="menu">
        <a href="modulCRM3.php">Szukaj ponownie</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <?php if (isset($blad)): ?>
            <div class="message error">
                <div class="message-content"><?= htmlspecialchars($blad) ?></div>
            </div>
            <div class="powrot">
                <a href="modulCRM3.php" class="btn-search">← Wróć do wyszukiwania</a>
            </div>
        <?php else: ?>
            <div class="message info">
                <div class="message-content">Znaleziono klienta ID <?= $rekord['id'] ?>. Wprowadź nowe dane i zapisz.</div>
            </div>

            <form method="POST" action="modulCRM3fs.php">
                <!-- Przekazujemy ID przez ukryte pole – bezpieczniejsze niż plik id.txt -->
                <input type="hidden" name="id" value="<?= $rekord['id'] ?>">

                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie"
                       value="<?= htmlspecialchars($rekord['imie']) ?>" required>

                <label for="mail">Adres e-mail:</label>
                <input type="email" id="mail" name="mail"
                       value="<?= htmlspecialchars($rekord['email']) ?>" required>

                <label for="sub">Subskrypcje:</label>
                <input type="text" id="sub" name="sub"
                       value="<?= htmlspecialchars($rekord['subskrypcje']) ?>" required>

                <button type="submit" class="btn-save">Zapisz zmiany</button>
                <a href="modulCRM3.php" class="btn-search" style="display:inline-block;margin-left:8px;">Anuluj</a>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
