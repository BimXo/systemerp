<?php
$znaleziono = false;
$id = $_POST['id'];
$linie = file('../../crm.txt');    
foreach($linie as $linia){
    $tablica = explode(";",$linia);
    if($tablica[0] != $id){
        $nowe[] = $linia;
        
    }
    else{
       echo "Znaleziono id dane zostaną usuniete";
    
}
}
file_put_contents('../../crm.txt', $nowe);

?>






