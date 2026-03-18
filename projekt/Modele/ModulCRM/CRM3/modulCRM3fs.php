    <?php
    $id = file_get_contents("id.txt");
    
    $imie = $_POST["imie"];
    $email = $_POST["mail"];
    $sub = $_POST["sub"];

    $linie = file('../../crm.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linie as $indeks => $linia){
        $tablica = explode(";",$linia);
        if($tablica[0] == $id){
            $tablica[1] = $imie;
            $tablica[2] = $email;
            $tablica[3] = $sub;
            
            $linie[$indeks] = implode(";", $tablica);
            echo "Zapisano dane";
            echo "<br>".$imie."  ".$email."  ".$sub;
            break;
            
        }
        
    }
    file_put_contents('../../crm.txt', implode("\n", $linie));


    ?>