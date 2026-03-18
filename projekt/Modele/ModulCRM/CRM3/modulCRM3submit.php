<?php
$znaleziono = false;
$id = $_POST['id'];
$linie = file('../../crm.txt');    
foreach($linie as $linia){
    $tablica = explode(";",$linia);
    if($tablica[0] == $id){
        $wiadomosc = "Znaleziono ID, uzupełnij dane i zapisz.";
        $znaleziono = true;
        break;
    }
    
}
if($znaleziono == false){
    die("Nie znaleziono id");
}
file_put_contents("id.txt", $id);


?>







<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Edycja rekordu</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>CRM - Edycja rekordu</h1>
    <nav>
        <a href="modulCRM3.php">Wyszukaj ponownie</a>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <?php if (!empty($wiadomosc)): ?>
            <div class="message"><?php echo htmlspecialchars($wiadomosc); ?></div>
        <?php endif; ?>
        <form method="post" action="modulCRM3fs.php">
            <input type="text" name="imie" placeholder="Imię" required>
            <input type="text" name="mail" placeholder="Email" required>
            <input type="text" name="sub" placeholder="Subskrypcja" required>
            <button type="submit">Zapisz</button>
        </form>
    </div>
</div>

</body>
</html>