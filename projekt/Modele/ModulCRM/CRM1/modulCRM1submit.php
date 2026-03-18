<?php
$imi = $_POST['imie'];

$email = $_POST['mail'];

$subskrypcja = $_POST['sub'];

$line = file('../../crm.txt');

$ids = [];

foreach($line as $l){
    $tab = explode(";", $l);
    $ids[] = $tab[0];
}

do{
    $id = rand(1000,9999); // losowe ID
}while(in_array($id,$ids));

if(ctype_alpha($imi)){
    $teks = $id.';'.$imi.';'.$email.';'.$subskrypcja."\n";
    file_put_contents('../../crm.txt',$teks,FILE_APPEND);
    echo "Zapisano dane: ".$imi." , ".$email." , ".$subskrypcja;
}
else{
    echo "Niepoprawne dane";
}
?>