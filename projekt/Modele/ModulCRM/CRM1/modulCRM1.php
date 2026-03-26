<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Dodaj klienta</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-1">
    
<header>
    <h1>CRM – Dodaj klienta</h1>
    <nav class="menu">
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2>Nowy klient</h2>
        <form method="POST" action="modulCRM1submit.php">

            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" placeholder="Imię klienta" required>

            <label for="mail">Adres e-mail:</label>
            <input type="email" id="mail" name="mail" placeholder="klient@example.com" required>

            <label for="sub">Subskrypcje (Ctrl = wielokrotny wybór):</label>
            <select id="sub" name="sub[]" multiple required>
                <option value="Newsletter">Newsletter</option>
                <option value="Promocje">Promocje</option>
                <option value="Aktualności">Aktualności</option>
                <option value="Wydarzenia">Wydarzenia</option>
            </select>

            <button type="submit" class="btn-save">Zapisz klienta</button>
        </form>

        <div class="powrot">
            <a href="../modulCRM.php">Powrót do menu CRM</a>
        </div>
    </div>
</div>
</body>
</html>
