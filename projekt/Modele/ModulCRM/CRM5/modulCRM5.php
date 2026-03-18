<?php

$linie = file('../../crm.txt');
foreach($linie as $linia){
     $tablica = explode(";",$linia);
     $nowe[] = $tablica[2]. "\n";     
 }
file_put_contents('email.txt', $nowe);
$plik = 'email.txt';
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="email.txt"');
header('Content-Length: ' . filesize($plik));
readfile($plik);
exit;


?>