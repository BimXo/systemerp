<?php

class ModelHR {
    private $plik = "employees.txt";

    private function czytajDane() {
        if (!file_exists($this->plik)) {
            return [];
        }
        $dane = file_get_contents($this->plik);
        return json_decode($dane, true) ?? [];
    }

    private function zapiszDane($dane) {
        file_put_contents($this->plik, json_encode($dane, JSON_PRETTY_PRINT));
    }

    public function pobierzWszystkich() {
        return $this->czytajDane();
    }

    public function utworz($imie, $dataUrodzenia, $dzial, $poziom) {
        $dane = $this->czytajDane();

        $id = count($dane) + 1;

        $pracownik = [
            "id" => $id,
            "name" => $imie,
            "birthdate" => $dataUrodzenia, // YYYY-MM-DD
            "department" => $dzial,
            "level" => (int)$poziom
        ];

        $dane[] = $pracownik;
        $this->zapiszDane($dane);
    }

    public function usun($id) {
        $id = (int)$id;
        $dane = $this->czytajDane();
        $dane = array_filter($dane, fn($pracownik) => $pracownik["id"] !== $id);
        $this->zapiszDane(array_values($dane));
    }

    public function aktualizuj($id, $imie, $dataUrodzenia, $dzial, $poziom) {
        $dane = $this->czytajDane();

        foreach ($dane as &$pracownik) {
            if ($pracownik["id"] === $id) {
                $pracownik["name"] = $imie;
                $pracownik["birthdate"] = $dataUrodzenia;
                $pracownik["department"] = $dzial;
                $pracownik["level"] = (int)$poziom;
            }
        }

        $this->zapiszDane($dane);
    }


    public function pobierzNajstarszegoINajmlodszego() {
        $dane = $this->czytajDane();

        usort($dane, fn($a, $b) => strtotime($a["birthdate"]) - strtotime($b["birthdate"]));

        return [
            "oldest" => $dane[0]["name"] ?? null,
            "youngest" => $dane[count($dane)-1]["name"] ?? null
        ];
    }

    public function pobierzSredniWiek() {
        $dane = $this->czytajDane();
        $dzisiaj = time();

        $wieki = array_map(function($pracownik) use ($dzisiaj) {
            return floor(($dzisiaj - strtotime($pracownik["birthdate"])) / (365*24*60*60));
        }, $dane);

        return count($wieki) ? array_sum($wieki) / count($wieki) : 0;
    }

    public function pobierzNadchodzaceUrodziny($data) {
        $dane = $this->czytajDane();
        $podstawa = strtotime($data);

        return array_filter($dane, function($pracownik) use ($podstawa) {
            $dzieńUrodzin = strtotime(date("Y") . "-" . date("m-d", strtotime($pracownik["birthdate"])));
            return ($dzieńUrodzin >= $podstawa && $dzieńUrodzin <= strtotime("+14 days", $podstawa));
        });
    }

    public function policzPoPoziomie($minimalnyPoziom) {
        $dane = $this->czytajDane();
        return count(array_filter($dane, fn($pracownik) => $pracownik["level"] >= $minimalnyPoziom));
    }

    public function policzPoDzialach() {
        $dane = $this->czytajDane();
        $wynik = [];

        foreach ($dane as $pracownik) {
            $dzial = $pracownik["department"];
            if (!isset($wynik[$dzial])) {
                $wynik[$dzial] = 0;
            }
            $wynik[$dzial]++;
        }

        return $wynik;
    }
}