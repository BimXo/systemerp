<?php
$imi=$_POST['imie'];
$email=$_POST['mail'];
$subskrypcja=$_POST['sub'];
$line=file('../../crm.txt');
$zawartosc=end($line);
$tab=explode(";",$zawartosc);
$id=(int)$tab[0]+1;
if(ctype_alpha($imi)){
    $teks=$id.';'.$imi.';'.$email.';'.$subskrypcja."\n";
    file_put_contents('../../crm.txt',$teks,FILE_APPEND);
    echo "Zapisano dane: ".$imi." , ".$email." , ".$subskrypcja;

}
else{
    echo "Niepoprawne dane";
}
?>
