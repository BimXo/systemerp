<?php

class ModelHR {
    private $plik = "employees.txt";

    // ── ODCZYT / ZAPIS ──────────────────────────────────────────────────────

    private function czytajDane(): array {
        if (!file_exists($this->plik)) return [];
        $dane = file_get_contents($this->plik);
        return json_decode($dane, true) ?? [];
    }

    private function zapiszDane(array $dane): void {
        file_put_contents($this->plik, json_encode(array_values($dane), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // ── CREATE ──────────────────────────────────────────────────────────────

    public function utworz(string $imie, string $dataUrodzenia, string $dzial, int $poziom): int {
        $dane  = $this->czytajDane();

        // Wyznacz nowe ID jako max istniejącego + 1 (bezpieczne po usunięciach)
        $maxId = 0;
        foreach ($dane as $p) {
            if ($p['id'] > $maxId) $maxId = $p['id'];
        }
        $id = $maxId + 1;

        $dane[] = [
            'id'         => $id,
            'name'       => $imie,
            'birthdate'  => $dataUrodzenia,   // YYYY-MM-DD
            'department' => $dzial,
            'level'      => $poziom,
        ];
        $this->zapiszDane($dane);
        return $id;
    }

    // ── READ ALL ────────────────────────────────────────────────────────────

    public function pobierzWszystkich(): array {
        return $this->czytajDane();
    }

    // ── READ ONE ────────────────────────────────────────────────────────────

    public function pobierzPoId(int $id): ?array {
        foreach ($this->czytajDane() as $pracownik) {
            if ($pracownik['id'] === $id) return $pracownik;
        }
        return null;
    }

    // ── UPDATE ──────────────────────────────────────────────────────────────

    public function aktualizuj(int $id, string $imie, string $dataUrodzenia, string $dzial, int $poziom): bool {
        $dane      = $this->czytajDane();
        $znalezion = false;

        foreach ($dane as &$pracownik) {
            if ($pracownik['id'] === $id) {
                $pracownik['name']       = $imie;
                $pracownik['birthdate']  = $dataUrodzenia;
                $pracownik['department'] = $dzial;
                $pracownik['level']      = $poziom;
                $znalezion = true;
                break;
            }
        }
        unset($pracownik);

        if ($znalezion) $this->zapiszDane($dane);
        return $znalezion;
    }

    // ── DELETE ──────────────────────────────────────────────────────────────

    public function usun(int $id): bool {
        $dane    = $this->czytajDane();
        $noweDane = array_filter($dane, fn($p) => $p['id'] !== $id);
        if (count($noweDane) === count($dane)) return false;
        $this->zapiszDane($noweDane);
        return true;
    }

    // ── SPECJALNE: najstarszy i najmłodszy (punkt 2) ─────────────────────

    /**
     * Zwraca tablicę ['oldest' => 'Imię', 'youngest' => 'Imię']
     * lub null dla obu pól gdy brak danych.
     */
    public function pobierzNajstarszegoINajmlodszego(): array {
        $dane = $this->czytajDane();
        if (empty($dane)) return ['oldest' => null, 'youngest' => null];

        usort($dane, fn($a, $b) => strcmp($a['birthdate'], $b['birthdate']));

        return [
            'oldest'   => $dane[0]['name'],
            'youngest' => $dane[count($dane) - 1]['name'],
        ];
    }

    // ── SPECJALNE: średni wiek (punkt 3) ─────────────────────────────────

    /**
     * Zwraca średni wiek pracowników w pełnych latach (float).
     * Używa dokładnego obliczenia przez diff(), a nie dzielenia przez 365.
     */
    public function pobierzSredniWiek(): float {
        $dane = $this->czytajDane();
        if (empty($dane)) return 0.0;

        $dzisiaj = new DateTime('today');
        $suma    = 0;

        foreach ($dane as $pracownik) {
            $urodziny = new DateTime($pracownik['birthdate']);
            $suma    += (int)$urodziny->diff($dzisiaj)->y;
        }

        return $suma / count($dane);
    }

    // ── SPECJALNE: nadchodzące urodziny (punkt 4) ─────────────────────────

    /**
     * Zwraca pracowników, których urodziny przypadają w ciągu 14 dni
     * od $dataPoczatkowa (YYYY-MM-DD). Poprawnie obsługuje przełom roku.
     */
    public function pobierzNadchodzaceUrodziny(string $dataPoczatkowa): array {
        $dane    = $this->czytajDane();
        $poczatek = new DateTime($dataPoczatkowa);
        $koniec   = (clone $poczatek)->modify('+14 days');

        $wynik = [];
        foreach ($dane as $pracownik) {
            // Sprawdź urodziny w bieżącym i następnym roku
            foreach ([0, 1] as $rokDodatek) {
                $urodziny = new DateTime(
                    ($poczatek->format('Y') + $rokDodatek) . '-' .
                    (new DateTime($pracownik['birthdate']))->format('m-d')
                );
                if ($urodziny >= $poczatek && $urodziny <= $koniec) {
                    $wynik[] = $pracownik;
                    break;   // nie dodawaj tego samego pracownika dwa razy
                }
            }
        }
        return $wynik;
    }

    // ── SPECJALNE: liczba po poziomie (punkt 5) ───────────────────────────

    /**
     * Zwraca liczbę pracowników z poziomem >= $minimalnyPoziom.
     */
    public function policzPoPoziomie(int $minimalnyPoziom): int {
        return count(array_filter(
            $this->czytajDane(),
            fn($p) => $p['level'] >= $minimalnyPoziom
        ));
    }

    // ── SPECJALNE: liczba na dział (punkt 6) ──────────────────────────────

    /**
     * Zwraca tablicę asocjacyjną ['NazwaDziału' => liczba_pracowników].
     * Posortowaną alfabetycznie po nazwie działu.
     */
    public function policzPoDzialach(): array {
        $wynik = [];
        foreach ($this->czytajDane() as $pracownik) {
            $dzial = $pracownik['department'];
            $wynik[$dzial] = ($wynik[$dzial] ?? 0) + 1;
        }
        ksort($wynik);
        return $wynik;
    }
}
