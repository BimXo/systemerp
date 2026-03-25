<?php
class KontrolerSprzedazy {
    public $model;

    public function __construct($model) {
        $this->model = $model;
    }

    // Zwraca wiersz transakcji z najwyższą kwotą
    public function pobierzNajwyzszaTransakcjaPrzychod() {
        $dane = $this->model->pobierzWszystkie();
        if (empty($dane)) return null;
        usort($dane, function($a, $b) {
            return (float)$b[3] <=> (float)$a[3];
        });
        return $dane[0];
    }

    // Zwraca nazwę produktu z najwyższym łącznym przychodem
    public function pobierzNajwyzszyPrzychodProdukt() {
        $dane = $this->model->pobierzWszystkie();
        $przychody = [];
        foreach ($dane as $wiersz) {
            $przychody[$wiersz[2]] = ($przychody[$wiersz[2]] ?? 0) + (float)$wiersz[3];
        }
        if (empty($przychody)) return null;
        arsort($przychody);
        return ['produkt' => key($przychody), 'suma' => reset($przychody)];
    }

    // Zwraca liczbę transakcji i sumę kwot między datami (punkty 4 i 5)
    public function pobierzStatystykiMiedzyDatami($poczatek, $koniec) {
        $dane = $this->model->pobierzWszystkie();
        $statystyki = ['count' => 0, 'sum' => 0.0];
        foreach ($dane as $wiersz) {
            if ($wiersz[4] >= $poczatek && $wiersz[4] <= $koniec) {
                $statystyki['count']++;
                $statystyki['sum'] += (float)$wiersz[3];
            }
        }
        return $statystyki;
    }
}
?>
