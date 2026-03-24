<?php
$id = file_get_contents("id.txt");
$imie = $_POST["imie"];
$email = $_POST["mail"];
$sub = $_POST["sub"];

$linie = file('../../crm.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$wiadomosc = "Nie znaleziono rekordu do zapisania.";
foreach($linie as $indeks => $linia){
    $tablica = explode(";", $linia);
    if($tablica[0] == $id){
        $tablica[1] = $imie;
        $tablica[2] = $email;
        $tablica[3] = $sub;
        $linie[$indeks] = implode(";", $tablica);
        $wiadomosc = "Zapisano dane: $imie, $email, $sub";
        break;
    }
}
file_put_contents('../../crm.txt', implode("\n", $linie));
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
    <h1>CRM - Zapis rekordu</h1>
    <nav>
        <a href="modulCRM3.php">Wyszukaj ponownie</a>
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