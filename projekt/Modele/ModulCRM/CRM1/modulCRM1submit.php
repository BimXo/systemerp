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

do{
    $id = rand(1000,9999);
}while(in_array($id,$ids));

if(ctype_alpha($imie)){
    $tekst = $id.';'.$imie.';'.$email.';'.$subskrypcja."\n";
    file_put_contents('../../crm.txt',$tekst,FILE_APPEND);
    echo "Zapisano dane: ".$imie." , ".$email." , ".$subskrypcja;
}
else{
    echo "Niepoprawne dane";
}
?>
