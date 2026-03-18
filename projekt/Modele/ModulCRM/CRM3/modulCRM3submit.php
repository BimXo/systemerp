<?php
$znaleziono = false;
$id = $_POST['id'];
$linie = file('../../crm.txt');    
foreach($linie as $linia){
    $tablica = explode(";",$linia);
    if($tablica[0] == $id){
        echo "Znaleziono id podaj dane:";
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
    <title>Opcja 3</title>
</head>
<body>
    <form method="post" action="modulCRM3fs.php">
    <input type="text" name="imie" required>
    <input type="text" name="mail" required>
    <input type="text" name="sub" required>
    <input type="submit" value="Zapisz">
</body>
</html>