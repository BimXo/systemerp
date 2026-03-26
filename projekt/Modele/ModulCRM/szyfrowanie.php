<?php
function cezar($text, $shift) {
    $result = "";
    $shift = $shift % 26; // Zabezpieczenie przed zbyt dużym przesunięciem

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];

        // Szyfrowanie wielkich liter
        if (ctype_upper($char)) {
            $result .= chr((ord($char) - 65 + $shift + 26) % 26 + 65);
        }
        // Szyfrowanie małych liter
        elseif (ctype_lower($char)) {
            $result .= chr((ord($char) - 97 + $shift + 26) % 26 + 97);
        }
        // Pozostawienie cyfr i znaków specjalnych bez zmian
        else {
            $result .= $char;
        }
    }
    return $result;
}
?>
