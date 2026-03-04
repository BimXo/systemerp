<?php
$imi=$_POST['imie'];
$email=$_POST['mail'];
$subskrypcja=$_POST['sub'];


$line=file('crm.txt');
$tab=explode(";",$zawartosc);
$id=$tab[0];

$teks=$id.';'.$imi.';'.$email.';'.$subskrypcja."\n";
file_put_contents('crm.txt',$teks,FILE_APPEND);


?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie </title>
</head>
<body>
    <h1>Dane zostały Zapisane</h1>

</body>
</html>
