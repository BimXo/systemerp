<?php
$znaleziono=false;
$id=$_POST['idih'];
$line=file('../../crm.txt');    
foreach($line as $linia){
    $tab=explode(";",$linia);
    if($tab[0]!=$id){
        $nowe[] = $linia;
        
    }
    else{
       echo "Znaleziono id dane zostaną usuniete";
    
}}
file_put_contents('../../crm.txt', $nowe);

?>






