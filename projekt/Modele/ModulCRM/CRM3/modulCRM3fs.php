    <?php
    $id = file_get_contents("id.txt");
    
    $imie = $_POST["imie"];
    $email = $_POST["mail"];
    $sub = $_POST["sub"];

    $linie = file('../../crm.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linie as $index => $linia){
        $tab=explode(";",$linia);
        if($tab[0]==$id){
            $tab[1]=$imie;
            $tab[2]=$email;
            $tab[3]=$sub;
            
            $linie[$index] = implode(";", $tab);
            echo "Zapisano dane";
            echo "<br>".$imie."  ".$email."  ".$sub;
            break;
            
        }
        
    }
    file_put_contents('../../crm.txt', implode("\n", $linie));


    ?>