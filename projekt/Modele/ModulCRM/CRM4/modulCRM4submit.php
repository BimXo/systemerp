<?php
$id = $_POST['id'];
$linie = file('../../crm.txt');
$nowe = [];
$wiadomosc = "Nie znaleziono rekordu do usunięcia.";
foreach($linie as $linia){
    $tablica = explode(";", $linia);
    if($tablica[0] != $id){
        $nowe[] = $linia;
    } else {
        $wiadomosc = "Usunięto rekord o ID: $id";
    }
}
file_put_contents('../../crm.txt', $nowe);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Usuwanie</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
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

