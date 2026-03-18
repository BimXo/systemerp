<?php
echo "<pre>";
$tekst = str_replace(";"," ",file_get_contents('../../crm.txt'));
echo $tekst;
echo "</pre>";


?>