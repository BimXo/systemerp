<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System CRM opcja 1</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-1">
<header>
    <h1>CRM - Dodaj użytkownika</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <form method="post" action="modulCRM1submit.php">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" required><br><br>
            
            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" required><br><br>
            
            <label for="sub">Subskrypcje (przytrzymaj Ctrl dla wielu):</label><br>
            <select id="sub" name="sub[]" multiple required>
                <option value="Newsletter">Newsletter</option>
                <option value="Promocje">Promocje</option>
                <option value="Aktualności">Aktualności</option>
                <option value="Wydarzenia">Wydarzenia</option>
            </select><br><br>
            
            <button type="submit" class="btn-save">Zapisz</button>
        </form>
    </div>
</div>

</body>
</html>

<?php






?>