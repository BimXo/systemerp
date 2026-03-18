<?php
$imie = $_POST['imie'];
$email = $_POST['mail'];
$subskrypcja = $_POST['sub'];

$linie = file('../../crm.txt');
$ids = [];
foreach($linie as $linia){
    $tablica = explode(";", $linia);
    $ids[] = $tablica[0];
}

do {
    $id = rand(1000,9999);
} while (in_array($id, $ids));

if (ctype_alpha($imie)) {
    $tekst = $id.';'.$imie.';'.$email.';'.$subskrypcja."\n";
    file_put_contents('../../crm.txt', $tekst, FILE_APPEND);
    $wiadomosc = "Zapisano dane: $imie , $email , $subskrypcja";
} else {
    $wiadomosc = "Niepoprawne dane";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Zapis</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>CRM - Zapis danych</h1>
    <nav>
        <a href="modulCRM1.php">Powrót do formularza</a>
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
