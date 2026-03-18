<?php
class KontrolerSprzedazy {
    public $model; // public, aby widok miał łatwy dostęp

    public function __construct($model) {
        $this->model = $model;
    }

    public function pobierzNajwyzszaTransakcjaPrzychod() {
        $dane = $this->model->pobierzWszystkie(); // Poprawione z . na ->
        if (empty($dane)) return null;
        usort($dane, function($a, $b) { return $b[3] <=> $a[3]; });
        return $dane[0];
    }

    public function pobierzNajwyzszyPrzychodProdukt() {
        $dane = $this->model->pobierzWszystkie();
        $przychody = [];
        foreach ($dane as $wiersz) {
            $przychody[$wiersz[2]] = ($przychody[$wiersz[2]] ?? 0) + $wiersz[3];
        }
        arsort($przychody);
        return !empty($przychody) ? key($przychody) : null;
    }

    public function pobierzStatystykiMiedzyDatami($poczatek, $koniec) {
        $dane = $this->model->pobierzWszystkie();
        $statystyki = ['count' => 0, 'sum' => 0];
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