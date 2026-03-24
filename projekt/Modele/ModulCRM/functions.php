<?php

define('CRM_PLIK', __DIR__ . '/crm.txt');

// -------------------------
// Odczyt wszystkich klientów
// -------------------------
function crm_pobierzWszystkich(): array {
    $linie = file_exists(CRM_PLIK)
        ? file(CRM_PLIK, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
    $klienty = [];
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (count($dane) >= 4) {
            $klienty[] = [
                'id'          => $dane[0],
                'imie'        => $dane[1],
                'email'       => $dane[2],
                'subskrypcje' => $dane[3],
            ];
        }
    }
    return $klienty;
}

// -------------------------
// Znajdź rekord po ID
// -------------------------
function crm_znajdzRekord(string $id): ?array {
    foreach (crm_pobierzWszystkich() as $k) {
        if ($k['id'] === $id) {
            return $k;
        }
    }
    return null;
}

// -------------------------
// Następne wolne ID
// -------------------------
function crm_nastepneId(): int {
    $linie = file_exists(CRM_PLIK)
        ? file(CRM_PLIK, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
    $maxId = 0;
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (isset($dane[0]) && is_numeric($dane[0])) {
            $maxId = max($maxId, (int) $dane[0]);
        }
    }
    return $maxId + 1;
}

// -------------------------
// Opcja 1 – Dodaj klienta
// -------------------------
function crm_dodaj(string $imie, string $email, array $subskrypcje): array {
    $bledy = [];
    if (empty($imie) || !preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $imie)) {
        $bledy[] = 'Imię może zawierać tylko litery i spacje.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $bledy[] = 'Niepoprawny adres email.';
    }
    if (empty($subskrypcje)) {
        $bledy[] = 'Wybierz przynajmniej jedną subskrypcję.';
    }

    if (!empty($bledy)) {
        return ['sukces' => false, 'wiadomosc' => implode(' ', $bledy)];
    }

    $id             = crm_nastepneId();
    $subskrypcjaStr = implode(', ', $subskrypcje);
    $tekst          = $id . ';' . $imie . ';' . $email . ';' . $subskrypcjaStr . "\n";
    file_put_contents(CRM_PLIK, $tekst, FILE_APPEND);

    return [
        'sukces'    => true,
        'wiadomosc' => "Zapisano: ID $id, $imie, $email, subskrypcje: $subskrypcjaStr",
    ];
}

// -------------------------
// Opcja 3 – Edytuj klienta
// -------------------------
function crm_edytuj(string $id, string $imie, string $email, string $sub): array {
    $bledy = [];
    if (empty($imie) || !preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $imie)) {
        $bledy[] = 'Imię może zawierać tylko litery i spacje.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $bledy[] = 'Niepoprawny adres email.';
    }
    if (empty($sub)) {
        $bledy[] = 'Subskrypcje są wymagane.';
    }

    if (!empty($bledy)) {
        return ['sukces' => false, 'wiadomosc' => implode(' ', $bledy)];
    }

    $linie     = file_exists(CRM_PLIK)
        ? file(CRM_PLIK, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
    $znaleziono = false;

    foreach ($linie as $indeks => $linia) {
        $tablica = explode(';', $linia);
        if (isset($tablica[0]) && $tablica[0] == $id) {
            $tablica[1]        = $imie;
            $tablica[2]        = $email;
            $tablica[3]        = $sub;
            $linie[$indeks]    = implode(';', $tablica);
            $znaleziono        = true;
            break;
        }
    }

    if ($znaleziono) {
        file_put_contents(CRM_PLIK, implode("\n", $linie) . "\n");
        return ['sukces' => true, 'wiadomosc' => "Zaktualizowano rekord ID $id: $imie, $email, $sub"];
    }

    return ['sukces' => false, 'wiadomosc' => "Nie znaleziono rekordu o ID $id."];
}

// -------------------------
// Opcja 4 – Usuń klienta
// -------------------------
function crm_usun(string $id): array {
    if (!is_numeric($id) || empty($id)) {
        return ['sukces' => false, 'wiadomosc' => 'Niepoprawne ID.'];
    }

    $linie    = file_exists(CRM_PLIK)
        ? file(CRM_PLIK, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
    $nowe     = [];
    $usunieto = false;

    foreach ($linie as $linia) {
        $tablica = explode(';', $linia);
        if (isset($tablica[0]) && $tablica[0] == $id) {
            $usunieto = true;
        } else {
            $nowe[] = $linia;
        }
    }

    if ($usunieto) {
        file_put_contents(CRM_PLIK, implode("\n", $nowe) . "\n");
        return ['sukces' => true, 'wiadomosc' => "Usunięto rekord o ID: $id"];
    }

    return ['sukces' => false, 'wiadomosc' => "Nie znaleziono rekordu o ID: $id"];
}

// -------------------------
// Opcja 5 – Eksport emaili
// -------------------------
function crm_eksportEmaili(): void {
    $linie  = file_exists(CRM_PLIK)
        ? file(CRM_PLIK, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
    $emaile = [];
    foreach ($linie as $linia) {
        $dane = explode(';', $linia);
        if (isset($dane[2]) && !empty($dane[2])) {
            $emaile[] = $dane[2];
        }
    }
    $zawartosc = implode("\n", $emaile) . "\n";

    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="emails.txt"');
    header('Content-Length: ' . strlen($zawartosc));
    echo $zawartosc;
    exit;
}
