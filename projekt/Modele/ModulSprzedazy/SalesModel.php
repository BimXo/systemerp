<?php
class ModelSprzedazy {
    private $nazwaPliku = "sales_db.txt";

    public function czytajDane() {
        if (!file_exists($this->nazwaPliku)) return [];
        $wiersze = [];
        if (($uchwyt = fopen($this->nazwaPliku, "r")) !== FALSE) {
            while (($dane = fgetcsv($uchwyt, 1000, ";")) !== FALSE) {
                if (count($dane) >= 5) {
                    $wiersze[] = $dane;
                }
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

    // CREATE
    public function utworz($idKlienta, $produkt, $kwota, $data) {
        $dane = $this->czytajDane();
        // Wyznacz nowe ID (max istniejącego + 1)
        $maxId = 0;
        foreach ($dane as $wiersz) {
            if ((int)$wiersz[0] > $maxId) $maxId = (int)$wiersz[0];
        }
        $id = $maxId + 1;
        $dane[] = [$id, $idKlienta, $produkt, (float)$kwota, $data];
        $this->zapiszDane($dane);
        return $id;
    }

    // READ ALL
    public function pobierzWszystkie() {
        return $this->czytajDane();
    }

    // READ ONE
    public function pobierzPoId($id) {
        $dane = $this->czytajDane();
        foreach ($dane as $wiersz) {
            if ((int)$wiersz[0] === (int)$id) return $wiersz;
        }
        return null;
    }

    // UPDATE
    public function aktualizuj($id, $idKlienta, $produkt, $kwota, $data) {
        $dane = $this->czytajDane();
        $znaleziono = false;
        foreach ($dane as &$wiersz) {
            if ((int)$wiersz[0] === (int)$id) {
                $wiersz[1] = $idKlienta;
                $wiersz[2] = $produkt;
                $wiersz[3] = (float)$kwota;
                $wiersz[4] = $data;
                $znaleziono = true;
                break;
            }
        }
        unset($wiersz);
        if ($znaleziono) {
            $this->zapiszDane($dane);
        }
        return $znaleziono;
    }

    // DELETE
    public function usun($id) {
        $dane = $this->czytajDane();
        $noweDane = array_filter($dane, function($wiersz) use ($id) {
            return (int)$wiersz[0] !== (int)$id;
        });
        if (count($noweDane) === count($dane)) return false;
        $this->zapiszDane(array_values($noweDane));
        return true;
    }
}
?>
