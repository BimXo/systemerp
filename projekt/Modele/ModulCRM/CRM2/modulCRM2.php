<?php
// Pobierz dane z pliku
$plik = '../crm.txt';
$linie = file_exists($plik) ? file($plik, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$klienty = [];
foreach ($linie as $linia) {
    $dane = explode(';', $linia);
    if (count($dane) >= 4) {
        $klienty[] = [
            'id' => $dane[0],
            'imie' => $dane[1],
            'email' => $dane[2],
            'subskrypcje' => $dane[3]
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Podgląd danych</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-2">
<header>
    <h1>CRM - Podgląd danych klientów</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2>Lista klientów</h2>
        <?php if (empty($klienty)): ?>
            <p>Brak danych klientów.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię</th>
                        <th>Email</th>
                        <th>Subskrypcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($klienty as $klient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($klient['id']); ?></td>
                            <td><?php echo htmlspecialchars($klient['imie']); ?></td>
                            <td><?php echo htmlspecialchars($klient['email']); ?></td>
                            <td><?php echo htmlspecialchars($klient['subskrypcje']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>