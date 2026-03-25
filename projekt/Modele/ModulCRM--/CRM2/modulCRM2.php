<?php
require_once '../szyfrowanie.php';

$plik    = '../crm.txt';
$linie   = file_exists($plik)
    ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    : [];

$klienty = [];
foreach ($linie as $linia) {
    $dane = explode(';', $linia);
    if (count($dane) >= 4) {
        $klienty[] = [
            'id'          => cezar($dane[0], -3),
            'imie'        => cezar($dane[1], -3),
            'email'       => cezar($dane[2], -3),
            'subskrypcje' => cezar($dane[3], -3),
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Lista klientów</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-2">
<header>
    <h1>CRM – Lista klientów</h1>
    <nav class="menu">
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2>Wszyscy klienci (<?= count($klienty) ?>)</h2>

        <?php if (empty($klienty)): ?>
            <div class="message info">
                <div class="message-content">Brak klientów w bazie danych.</div>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię</th>
                        <th>E-mail</th>
                        <th>Subskrypcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($klienty as $k): ?>
                    <tr>
                        <td><?= htmlspecialchars($k['id']) ?></td>
                        <td><?= htmlspecialchars($k['imie']) ?></td>
                        <td><?= htmlspecialchars($k['email']) ?></td>
                        <td><?= htmlspecialchars($k['subskrypcje']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="powrot">
            <a href="../modulCRM.php">← Powrót do menu CRM</a>
        </div>
    </div>
</div>
</body>
</html>
