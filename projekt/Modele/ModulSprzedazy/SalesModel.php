<?php
class ModelSprzedazy {
    private $nazwaPliku = "sales_db.txt";

    // Zmienione na public, aby kontroler mógł czytać plik bezpośrednio
    public function czytajDane() {
        if (!file_exists($this->nazwaPliku)) return [];
        $wiersze = [];
        if (($uchwyt = fopen($this->nazwaPliku, "r")) !== FALSE) {
            while (($dane = fgetcsv($uchwyt, 1000, ";")) !== FALSE) {
                $wiersze[] = $dane;
            }
            fclose($uchwyt);
        }
        return $wiersze;
    }

    public function zapiszDane($dane) {
        $uchwyt = fopen($this->nazwaPliku, "w");
        foreach ($dane as $wiersz) {
            fputcsv($uchwyt, $wiersz, ";");
        }
        fclose($uchwyt);
    }

    public function utworz($idKlienta, $produkt, $kwota, $data) {
        $dane = $this->czytajDane();
        $id = count($dane) + 1;
        $dane[] = [$id, $idKlienta, $produkt, $kwota, $data];
        $this->zapiszDane($dane);
        return $id;
    }

    public function pobierzWszystkie() {
        return $this->czytajDane();
    }
    
    // ... reszta metod (update/delete) pozostaje bez zmian
}
?>