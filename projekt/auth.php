<?php
//  auth.php  –  Logowanie, rejestracja, wylogowanie

session_start();

define('USERS_FILE', __DIR__ . '/users.txt');

//Helpers 

function auth_zalogowany(): bool {
    return isset($_SESSION['user']);
}

function auth_pobierzUsera(): ?array {
    return $_SESSION['user'] ?? null;
}

function auth_maUprawnienie(string $modul): bool {
    $user = auth_pobierzUsera();
    if (!$user) return false;
    return in_array($modul, $user['uprawnienia'] ?? []);
}

// Plik users.txt (format: login;hash;imie;crm;sprzedaz;hr) 

function auth_wczytajUzytkownikow(): array {
    if (!file_exists(USERS_FILE)) return [];
    $linie       = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $uzytkownicy = [];
    foreach ($linie as $linia) {
        $p = explode(';', $linia);
        if (count($p) < 6) continue;
        $uzytkownicy[] = [
            'login'       => $p[0],
            'hash'        => $p[1],
            'imie'        => $p[2],
            'uprawnienia' => array_values(array_filter([
                $p[3] === '1' ? 'crm'      : '',
                $p[4] === '1' ? 'sprzedaz' : '',
                $p[5] === '1' ? 'hr'       : '',
            ])),
        ];
    }
    return $uzytkownicy;
}

function auth_zapiszUzytkownikow(array $uzytkownicy): void {
    $linie = [];
    foreach ($uzytkownicy as $u) {
        $linie[] = implode(';', [
            $u['login'],
            $u['hash'],
            $u['imie'],
            in_array('crm',      $u['uprawnienia']) ? '1' : '0',
            in_array('sprzedaz', $u['uprawnienia']) ? '1' : '0',
            in_array('hr',       $u['uprawnienia']) ? '1' : '0',
        ]);
    }
    file_put_contents(USERS_FILE, implode("\n", $linie) . "\n");
}

function auth_znajdzUsera(string $login): ?array {
    foreach (auth_wczytajUzytkownikow() as $u) {
        if ($u['login'] === $login) return $u;
    }
    return null;
}

//Akcje

function auth_zaloguj(string $login, string $haslo): array {
    $user = auth_znajdzUsera(trim($login));
    if (!$user) {
        return ['ok' => false, 'blad' => 'Nie znaleziono użytkownika.'];
    }
    if (!password_verify($haslo, $user['hash'])) {
        return ['ok' => false, 'blad' => 'Nieprawidłowe hasło.'];
    }
    $_SESSION['user'] = $user;
    return ['ok' => true];
}

function auth_wyloguj(): void {
    session_destroy();
}

function auth_rejestruj(string $login, string $haslo, string $imie, array $uprawnienia): array {
    $login = trim($login);
    $imie  = trim($imie);

    if (empty($login) || !preg_match('/^[a-zA-Z0-9_]{3,30}$/', $login)) {
        return ['ok' => false, 'blad' => 'Login: 3–30 znaków, tylko litery, cyfry i _.'];
    }
    if (strlen($haslo) < 4) {
        return ['ok' => false, 'blad' => 'Hasło musi mieć co najmniej 4 znaki.'];
    }
    if (empty($imie)) {
        return ['ok' => false, 'blad' => 'Podaj imię.'];
    }
    if (auth_znajdzUsera($login)) {
        return ['ok' => false, 'blad' => 'Taki login już istnieje.'];
    }

    $dozwolone     = ['crm', 'sprzedaz', 'hr'];
    $uzytkownicy   = auth_wczytajUzytkownikow();
    $uzytkownicy[] = [
        'login'       => $login,
        'hash'        => password_hash($haslo, PASSWORD_DEFAULT),
        'imie'        => $imie,
        'uprawnienia' => array_values(array_intersect($uprawnienia, $dozwolone)),
    ];
    auth_zapiszUzytkownikow($uzytkownicy);
    return ['ok' => true];
}

//    Zmienne dostępne po require_once w index.php

$_auth_wiadomosc = '';
$_auth_blad      = '';
$_auth_tryb      = $_GET['tryb'] ?? 'logowanie';

if (isset($_GET['wyloguj'])) {
    auth_wyloguj();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'logowanie') {
        $wynik = auth_zaloguj($_POST['login'] ?? '', $_POST['haslo'] ?? '');
        if ($wynik['ok']) {
            header('Location: index.php');
            exit;
        }
        $_auth_blad = $wynik['blad'];
        $_auth_tryb = 'logowanie';
    }

    if ($akcja === 'rejestracja') {
        $wynik = auth_rejestruj(
            $_POST['login']       ?? '',
            $_POST['haslo']       ?? '',
            $_POST['imie']        ?? '',
            $_POST['uprawnienia'] ?? []
        );
        if ($wynik['ok']) {
            $_auth_wiadomosc = 'Konto zostało utworzone. Możesz się teraz zalogować.';
            $_auth_tryb      = 'logowanie';
        } else {
            $_auth_blad = $wynik['blad'];
            $_auth_tryb = 'rejestracja';
        }
    }
}
