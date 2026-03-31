<?php
require_once 'auth.php';
// auth.php obsługuje już: session_start, POST logowania/rejestracji, GET wyloguj
// Po require dostępne są: $_auth_wiadomosc, $_auth_blad, $_auth_tryb

$user = auth_pobierzUsera();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System ERP</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<header>
    <h1 class="systemerp">System ERP</h1>
    <?php if ($user): ?>
        <nav>
            Zalogowany jako: <strong><?= htmlspecialchars($user['imie']) ?></strong>
            &nbsp;&mdash;&nbsp;
            <a href="index.php?wyloguj=1">Wyloguj</a>
        </nav>
    <?php endif; ?>
</header>

<div class="container">

    <?php if ($user): ?>
//PANEL GŁÓWNY – zalogowany użytkownik

        <div class="card">
            <h2>Witaj, <?= htmlspecialchars($user['imie']) ?>!</h2>
            <p>Wybierz moduł, do którego masz dostęp:</p>
        </div>

        <section class="section">
            <h3>Moduł CRM</h3>
            <?php if (auth_maUprawnienie('crm')): ?>
                <p>Zarządzanie klientami i subskrypcjami.</p>
                <a href="Modele/ModulCRM/modulCRM.php">Otwórz Moduł CRM</a>
            <?php else: ?>
                <p>Brak uprawnień do tego modułu.</p>
            <?php endif; ?>
        </section>
            
        <section class="section">
            <h3>Moduł Sprzedaży</h3>
            <?php if (auth_maUprawnienie('sprzedaz')): ?>
                <p>Transakcje, statystyki i raporty sprzedażowe.</p>
                <a href="Modele/ModulSprzedazy/SalesView.php">Otwórz Moduł Sprzedaży</a>
            <?php else: ?>
                <p>Brak uprawnień do tego modułu.</p>
            <?php endif; ?>      
        </section>

        <section class="section">
                <h3>Moduł HR</h3>
                <?php if (auth_maUprawnienie('hr')): ?>
                    <p>Pracownicy, działy, poziomy i urodziny.</p>
                    <a href="Modele/ModulHR/hr_controller.php">Otwórz Moduł HR</a>
                <?php else: ?>
                    <p>Brak uprawnień do tego modułu.</p>
                <?php endif; ?>
        </section>

    <?php else: ?>
    //PANEL AUTH – niezalogowany

        <nav>
            <a href="?tryb=logowanie">Logowanie</a>
            |
            <a href="?tryb=rejestracja">Rejestracja</a>
        </nav>

        <?php if ($_auth_wiadomosc): ?>
            <div class="message"><?= htmlspecialchars($_auth_wiadomosc) ?></div>
        <?php endif; ?>

        <?php if ($_auth_blad): ?>
            <div class="message"><?= htmlspecialchars($_auth_blad) ?></div>
        <?php endif; ?>

        <?php if ($_auth_tryb === 'logowanie'): ?>
//Formularz logowania
            <div class="card">
                <h2>Logowanie</h2>
                <form method="post" action="">
                    <input type="hidden" name="akcja" value="logowanie">

                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login"
                           value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                           autocomplete="username" required>

                    <label for="haslo">Hasło:</label>
                    <input type="password" id="haslo" name="haslo"
                           autocomplete="current-password" required>

                    <button type="submit">Zaloguj się</button>
                </form>
                <p>Nie masz konta? <a href="?tryb=rejestracja">Zarejestruj się</a></p>
            </div>

        <?php else: ?>
//Formularz rejestracji
            <div class="card">
                <h2>Rejestracja</h2>
                <form method="post" action="?tryb=rejestracja">
                    <input type="hidden" name="akcja" value="rejestracja">

                    <label for="reg_imie">Imię:</label>
                    <input type="text" id="reg_imie" name="imie"
                           value="<?= htmlspecialchars($_POST['imie'] ?? '') ?>"
                           placeholder="np. Jan Kowalski" required>

                    <label for="reg_login">Login:</label>
                    <input type="text" id="reg_login" name="login"
                           value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                           placeholder="3–30 znaków, litery/cyfry/_"
                           autocomplete="username" required>

                    <label for="reg_haslo">Hasło:</label>
                    <input type="password" id="reg_haslo" name="haslo"
                           placeholder="min. 4 znaki"
                           autocomplete="new-password" required>

                    <fieldset>
                        <legend>Uprawnienia do modułów:</legend>

                        <label>
                            <input type="checkbox" name="uprawnienia[]" value="crm"
                                <?= in_array('crm', $_POST['uprawnienia'] ?? []) ? 'checked' : '' ?>>
                            Moduł CRM
                        </label>

                        <label>
                            <input type="checkbox" name="uprawnienia[]" value="sprzedaz"
                                <?= in_array('sprzedaz', $_POST['uprawnienia'] ?? []) ? 'checked' : '' ?>>
                            Moduł Sprzedaży
                        </label>

                        <label>
                            <input type="checkbox" name="uprawnienia[]" value="hr"
                                <?= in_array('hr', $_POST['uprawnienia'] ?? []) ? 'checked' : '' ?>>
                            Moduł HR
                        </label>
                    </fieldset>

                    <button type="submit">Zarejestruj się</button>
                </form>
                <p>Masz już konto? <a href="?tryb=logowanie">Zaloguj się</a></p>
            </div>

        <?php endif; ?>
    <?php endif; ?>

</div>

</body>
</html>
